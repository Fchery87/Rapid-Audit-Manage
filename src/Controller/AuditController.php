<?php

namespace App\Controller;

use App\Service\FileStorage;
use App\Service\ReportParser;
use App\Service\PdfRenderer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuditController extends AbstractController
{
    public function view(Request $request, FileStorage $storage, ReportParser $parser, ManagerRegistry $doctrine): Response
    {
        $file = $request->query->get('file');
        if (!$file) {
            throw new NotFoundHttpException('File not specified');
        }

        try {
            $absolutePath = $storage->resolvePath($file);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $account = $this->getAccountByFile($doctrine, $file);
        if (!$account) {
            throw new NotFoundHttpException('Account not found for file');
        }

        $parsed = $parser->loadReportData($absolutePath);

        return $this->render('report/simple-audit.html.twig', [
            'account' => $account,
            'client_data' => $parsed['client_data'],
            'derogatory_accounts' => $parsed['derogatory_accounts'],
            'inquiry_accounts' => $parsed['inquiry_accounts'],
            'public_records' => $parsed['public_records'],
            'credit_info' => $parsed['credit_info'],
            'report_file' => $file,
        ]);
    }

    public function pdf(Request $request, FileStorage $storage, ReportParser $parser, PdfRenderer $pdfRenderer, ManagerRegistry $doctrine): Response
    {
        $file = $request->query->get('file');
        if (!$file) {
            throw new NotFoundHttpException('File not specified');
        }

        try {
            $absolutePath = $storage->resolvePath($file);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $account = $this->getAccountByFile($doctrine, $file);
        if (!$account) {
            throw new NotFoundHttpException('Account not found for file');
        }

        $parsed = $parser->loadReportData($absolutePath);

        $html = $this->renderView('report/simple-audit.html.twig', [
            'account' => $account,
            'client_data' => $parsed['client_data'],
            'derogatory_accounts' => $parsed['derogatory_accounts'],
            'inquiry_accounts' => $parsed['inquiry_accounts'],
            'public_records' => $parsed['public_records'],
            'credit_info' => $parsed['credit_info'],
            'report_file' => $file,
            'pdf_export' => true,
        ]);

        return $pdfRenderer->renderPdfResponse($html, sprintf('simple-audit-%s.pdf', $account['last_name'] ?? 'report'));
    }

    private function getAccountByFile(ManagerRegistry $doctrine, string $file): ?array
    {
        $em = $doctrine->getManager();
        $conn = $em->getConnection();
        $query = "SELECT a.aid, a.first_name, a.last_name, a.email FROM accounts a INNER JOIN account_files f ON f.aid = a.aid WHERE f.filename = :filename";
        $rows = $conn->prepare($query)->executeQuery(['filename' => $file])->fetchAllAssociative();
        return $rows[0] ?? null;
    }
}