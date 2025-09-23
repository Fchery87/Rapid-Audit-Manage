<?php

namespace App\Controller;

use App\Entity\CreditReport;
use App\Repository\CreditReportRepository;
use App\Security\SensitiveDataProtector;
use App\Service\FileStorage;
use App\Service\PdfRenderer;
use App\Service\ReportParser;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ANALYST')]
class AuditController extends AbstractController
{
    public function __construct(
        private readonly FileStorage $storage,
        private readonly ReportParser $parser,
        private readonly PdfRenderer $pdfRenderer,
        private readonly CreditReportRepository $reports,
        private readonly EntityManagerInterface $entityManager,
        private readonly SensitiveDataProtector $protector,
    ) {
    }

    #[Route('/simple-audit', name: 'app_simple_audit', methods: ['GET'])]
    public function view(Request $request): Response
    {
        $file = $request->query->get('file');
        if (!$file) {
            throw new NotFoundHttpException('File not specified');
        }

        try {
            $absolutePath = $this->storage->resolvePath($file);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $account = $this->getAccountByFile($file);
        if (!$account) {
            throw new NotFoundHttpException('Account not found for file');
        }

        $reportData = $this->resolveReportData($file, $absolutePath, (int) $account['aid']);

        return $this->render('report/simple-audit.html.twig', [
            'account' => $account,
            'client_data' => $reportData['clientData'],
            'derogatory_accounts' => $reportData['derogatory'],
            'inquiry_accounts' => $reportData['inquiries'],
            'public_records' => $reportData['public'],
            'credit_info' => $reportData['creditInfo'],
            'report_file' => $file,
        ]);
    }

    #[Route('/simple-audit.pdf', name: 'app_simple_audit_pdf', methods: ['GET'])]
    public function pdf(Request $request): Response
    {
        $file = $request->query->get('file');
        if (!$file) {
            throw new NotFoundHttpException('File not specified');
        }

        try {
            $absolutePath = $this->storage->resolvePath($file);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $account = $this->getAccountByFile($file);
        if (!$account) {
            throw new NotFoundHttpException('Account not found for file');
        }

        $reportData = $this->resolveReportData($file, $absolutePath, (int) $account['aid']);

        $html = $this->renderView('report/simple-audit.html.twig', [
            'account' => $account,
            'client_data' => $reportData['clientData'],
            'derogatory_accounts' => $reportData['derogatory'],
            'inquiry_accounts' => $reportData['inquiries'],
            'public_records' => $reportData['public'],
            'credit_info' => $reportData['creditInfo'],
            'report_file' => $file,
            'pdf_export' => true,
        ]);

        return $this->pdfRenderer->renderPdfResponse(
            $html,
            sprintf('simple-audit-%s.pdf', $account['last_name'] ?? 'report')
        );
    }

    private function resolveReportData(string $file, string $absolutePath, int $accountAid): array
    {
        $existing = $this->reports->findOneBy(['filename' => $file]);

        if ($existing instanceof CreditReport) {
            return [
                'clientData' => $existing->getClientData(),
                'derogatory' => $existing->getDerogatoryAccounts(),
                'inquiries' => $existing->getInquiryAccounts(),
                'public' => $existing->getPublicRecords(),
                'creditInfo' => $existing->getCreditInfo(),
            ];
        }

        $parsed = $this->parser->loadReportData($absolutePath);
        $clientData = json_decode(
            json_encode($parsed['client_data'], \JSON_THROW_ON_ERROR),
            true,
            512,
            \JSON_THROW_ON_ERROR
        );
        $derogatory = $parsed['derogatory_accounts'];
        $inquiries = $parsed['inquiry_accounts'];
        $public = $parsed['public_records'];
        $creditInfo = $parsed['credit_info'];

        $entity = (new CreditReport())
            ->setAccountAid($accountAid)
            ->setFilename($file)
            ->setParsedAt(new \DateTimeImmutable('now'))
            ->setClientData($clientData)
            ->setDerogatoryAccounts($derogatory)
            ->setInquiryAccounts($inquiries)
            ->setPublicRecords($public)
            ->setCreditInfo($creditInfo);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return [
            'clientData' => $clientData,
            'derogatory' => $derogatory,
            'inquiries' => $inquiries,
            'public' => $public,
            'creditInfo' => $creditInfo,
        ];
    }

    private function getAccountByFile(string $file): ?array
    {
        $sql = 'SELECT a.aid, a.first_name, a.last_name, a.email FROM accounts a INNER JOIN account_files f ON f.aid = a.aid WHERE f.filename = :filename';

        $result = $this->connection()->executeQuery($sql, ['filename' => $file]);

        $account = $result->fetchAssociative() ?: null;

        return $account ? $this->protector->decryptAccountRecord($account) : null;
    }

    private function connection(): Connection
    {
        return $this->entityManager->getConnection();
    }
}

