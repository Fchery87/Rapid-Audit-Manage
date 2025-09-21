<?php

namespace App\Controller;

use App\Entity\CreditReport;
use App\Repository\CreditReportRepository;
use App\Service\FileStorage;
use App\Service\ReportParser;
use App\Service\PdfRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AuditController extends AbstractController
{
    public function view(
        Request $request,
        FileStorage $storage,
        ReportParser $parser,
        ManagerRegistry $doctrine,
        CreditReportRepository $reports,
        EntityManagerInterface $em
    ): Response {
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

        $existing = $reports->findOneBy(['filename' => $file]);

        if ($existing instanceof CreditReport) {
            $clientData = $existing->getClientData();
            $derogatory = $existing->getDerogatoryAccounts();
            $inquiries = $existing->getInquiryAccounts();
            $public = $existing->getPublicRecords();
            $creditInfo = $existing->getCreditInfo();
        } else {
            $parsed = $parser->loadReportData($absolutePath);
            // Convert client_data (object) to array for JSON storage
            $clientData = json_decode(json_encode($parsed['client_data']), true);
            $derogatory = $parsed['derogatory_accounts'];
            $inquiries = $parsed['inquiry_accounts'];
            $public = $parsed['public_records'];
            $creditInfo = $parsed['credit_info'];

            $entity = (new CreditReport())
                ->setAccountAid((int) $account['aid'])
                ->setFilename($file)
                ->setParsedAt(new \DateTimeImmutable('now'))
                ->setClientData($clientData)
                ->setDerogatoryAccounts($derogatory)
                ->setInquiryAccounts($inquiries)
                ->setPublicRecords($public)
                ->setCreditInfo($creditInfo);

            $em->persist($entity);
            $em->flush();
        }

        return $this->render('report/simple-audit.html.twig', [
            'account' => $account,
            'client_data' => $clientData,
            'derogatory_accounts' => $derogatory,
            'inquiry_accounts' => $inquiries,
            'public_records' => $public,
            'credit_info' => $creditInfo,
            'report_file' => $file,
        ]);
    }

    public function pdf(
        Request $request,
        FileStorage $storage,
        ReportParser $parser,
        PdfRenderer $pdfRenderer,
        ManagerRegistry $doctrine,
        CreditReportRepository $reports,
        EntityManagerInterface $em
    ): Response {
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

        $existing = $reports->findOneBy(['filename' => $file]);

        if ($existing instanceof CreditReport) {
            $clientData = $existing->getClientData();
            $derogatory = $existing->getDerogatoryAccounts();
            $inquiries = $existing->getInquiryAccounts();
            $public = $existing->getPublicRecords();
            $creditInfo = $existing->getCreditInfo();
        } else {
            $parsed = $parser->loadReportData($absolutePath);
            $clientData = json_decode(json_encode($parsed['client_data']), true);
            $derogatory = $parsed['derogatory_accounts'];
            $inquiries = $parsed['inquiry_accounts'];
            $public = $parsed['public_records'];
            $creditInfo = $parsed['credit_info'];

            $entity = (new CreditReport())
                ->setAccountAid((int) $account['aid'])
                ->setFilename($file)
                ->setParsedAt(new \DateTimeImmutable('now'))
                ->setClientData($clientData)
                ->setDerogatoryAccounts($derogatory)
                ->setInquiryAccounts($inquiries)
                ->setPublicRecords($public)
                ->setCreditInfo($creditInfo);

            $em->persist($entity);
            $em->flush();
        }

        $html = $this->renderView('report/simple-audit.html.twig', [
            'account' => $account,
            'client_data' => $clientData,
            'derogatory_accounts' => $derogatory,
            'inquiry_accounts' => $inquiries,
            'public_records' => $public,
            'credit_info' => $creditInfo,
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