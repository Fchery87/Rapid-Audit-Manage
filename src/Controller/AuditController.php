<?php

namespace App\Controller;

use App\Entity\CreditReport;
use App\Repository\CreditReportRepository;
use App\Service\Account\AccountLocator;
use App\Service\FileStorage;
use App\Service\PdfRenderer;
use App\Service\ReportParser;
use App\Service\Dispute\DisputeWorkflowService;
use App\Service\Security\AuditTrailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

#[IsGranted('ROLE_ANALYST')]
class AuditController extends AbstractController
{
    public function __construct(
        private readonly FileStorage $storage,
        private readonly ReportParser $parser,
        private readonly PdfRenderer $pdfRenderer,
        private readonly CreditReportRepository $reports,
        private readonly EntityManagerInterface $entityManager,
        private readonly AccountLocator $accounts,
        private readonly DisputeWorkflowService $workflow,
        private readonly AuditTrailService $auditTrail,
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

        $account = $this->accounts->findAccountByFile($file);
        if (!$account) {
            throw new NotFoundHttpException('Account not found for file');
        }

        $reportData = $this->resolveReportData($file, $absolutePath, (int) $account['aid']);
        $disputeWorkflow = $this->workflow->getCaseOverview(
            (int) $account['aid'],
            $account,
            [
                'clientData' => $reportData['clientData'],
                'derogatory' => $reportData['derogatory'],
            ]
        );

        $response = $this->render('report/simple-audit.html.twig', [
            'account' => $account,
            'client_data' => $reportData['clientData'],
            'derogatory_accounts' => $reportData['derogatory'],
            'inquiry_accounts' => $reportData['inquiries'],
            'public_records' => $reportData['public'],
            'credit_info' => $reportData['creditInfo'],
            'report_file' => $file,
            'meta' => $reportData['meta'],
            'dispute_workflow' => $disputeWorkflow,
        ]);

        $this->auditTrail->recordAnalystReportViewed(
            $this->resolveActorId(),
            (int) $account['aid'],
            $file,
            ['format' => 'html']
        );

        return $response;
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

        $account = $this->accounts->findAccountByFile($file);
        if (!$account) {
            throw new NotFoundHttpException('Account not found for file');
        }

        $reportData = $this->resolveReportData($file, $absolutePath, (int) $account['aid']);
        $disputeWorkflow = $this->workflow->getCaseOverview(
            (int) $account['aid'],
            $account,
            [
                'clientData' => $reportData['clientData'],
                'derogatory' => $reportData['derogatory'],
            ]
        );

        $html = $this->renderView('report/simple-audit.html.twig', [
            'account' => $account,
            'client_data' => $reportData['clientData'],
            'derogatory_accounts' => $reportData['derogatory'],
            'inquiry_accounts' => $reportData['inquiries'],
            'public_records' => $reportData['public'],
            'credit_info' => $reportData['creditInfo'],
            'report_file' => $file,
            'pdf_export' => true,
            'meta' => $reportData['meta'],
            'dispute_workflow' => $disputeWorkflow,
        ]);

        $response = $this->pdfRenderer->renderPdfResponse(
            $html,
            sprintf('simple-audit-%s.pdf', $account['last_name'] ?? 'report')
        );

        $this->auditTrail->recordAnalystReportViewed(
            $this->resolveActorId(),
            (int) $account['aid'],
            $file,
            ['format' => 'pdf']
        );

        return $response;
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
                'meta' => $existing->getMeta() ?? [],
            ];
        }

        $parsedReport = $this->parser->parse($absolutePath);
        $payload = $parsedReport->toArray();
        $clientData = $payload['client_data'];
        $derogatory = $payload['derogatory_accounts'];
        $inquiries = $payload['inquiry_accounts'];
        $public = $payload['public_records'];
        $creditInfo = $payload['credit_info'];
        $meta = $payload['meta'];

        $entity = (new CreditReport())
            ->setAccountAid($accountAid)
            ->setFilename($file)
            ->setParsedAt($parsedReport->provenance->parsedAt)
            ->setClientData($clientData)
            ->setDerogatoryAccounts($derogatory)
            ->setInquiryAccounts($inquiries)
            ->setPublicRecords($public)
            ->setCreditInfo($creditInfo)
            ->setMeta($meta);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return [
            'clientData' => $clientData,
            'derogatory' => $derogatory,
            'inquiries' => $inquiries,
            'public' => $public,
            'creditInfo' => $creditInfo,
            'meta' => $meta,
        ];
    }

    private function resolveActorId(): string
    {
        $user = $this->getUser();

        if ($user instanceof UserInterface) {
            return $user->getUserIdentifier();
        }

        return 'anonymous';
    }
}

