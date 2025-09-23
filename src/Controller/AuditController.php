<?php

namespace App\Controller;

use App\Entity\CreditReport;
use App\Repository\CreditReportRepository;
use App\Service\Account\AccountLocator;
use App\Service\Dispute\DisputeWorkflowService;
use App\Service\FileStorage;
use App\Service\PdfRenderer;
use App\Service\ReportParser;
use App\Service\Report\ActionPlanExporter;
use App\Service\Report\ReportInsightsBuilder;
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
        private readonly AccountLocator $accounts,
        private readonly DisputeWorkflowService $workflow,
        private readonly ReportInsightsBuilder $insightsBuilder,
        private readonly ActionPlanExporter $actionPlanExporter,
    ) {
    }

    #[Route('/simple-audit', name: 'app_simple_audit', methods: ['GET'])]
    public function view(Request $request): Response
    {
        $file = $request->query->get('file');
        if (!$file) {
            throw new NotFoundHttpException('File not specified');
        }

        $context = $this->buildAuditContext($file);
        $reportData = $context['report_data'];

        return $this->render('report/simple-audit.html.twig', [
            'account' => $context['account'],
            'client_data' => $reportData['clientData'],
            'derogatory_accounts' => $reportData['derogatory'],
            'inquiry_accounts' => $reportData['inquiries'],
            'public_records' => $reportData['public'],
            'credit_info' => $reportData['creditInfo'],
            'report_file' => $file,
            'meta' => $reportData['meta'],
            'dispute_workflow' => $context['dispute_workflow'],
            'progress_timeline' => $context['progress_timeline'],
            'bureau_comparison' => $context['bureau_comparison'],
            'recommendation_scores' => $context['recommendation_scores'],
            'action_plan' => $context['action_plan'],
        ]);
    }

    #[Route('/simple-audit.pdf', name: 'app_simple_audit_pdf', methods: ['GET'])]
    public function pdf(Request $request): Response
    {
        $file = $request->query->get('file');
        if (!$file) {
            throw new NotFoundHttpException('File not specified');
        }

        $context = $this->buildAuditContext($file);
        $account = $context['account'];
        $reportData = $context['report_data'];

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
            'dispute_workflow' => $context['dispute_workflow'],
            'progress_timeline' => $context['progress_timeline'],
            'bureau_comparison' => $context['bureau_comparison'],
            'recommendation_scores' => $context['recommendation_scores'],
            'action_plan' => $context['action_plan'],
        ]);

        return $this->pdfRenderer->renderPdfResponse(
            $html,
            sprintf('simple-audit-%s.pdf', $account['last_name'] ?? 'report')
        );
    }

    #[Route('/simple-audit/action-plan.csv', name: 'app_simple_audit_action_plan', methods: ['GET'])]
    public function exportActionPlan(Request $request): Response
    {
        $file = $request->query->get('file');
        if (!$file) {
            throw new NotFoundHttpException('File not specified');
        }

        $context = $this->buildAuditContext($file);
        $csv = $this->actionPlanExporter->toCsv($context['action_plan']);

        $account = $context['account'];
        $suffix = $this->slugify((string) ($account['last_name'] ?? 'report'));
        $filename = sprintf('action-plan-%s.csv', $suffix);

        return new Response($csv, Response::HTTP_OK, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
        ]);
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

    /**
     * @return array{
     *     account: array<string, mixed>,
     *     report_data: array<string, mixed>,
     *     dispute_workflow: array<string, mixed>,
     *     progress_timeline: array<string, mixed>,
     *     bureau_comparison: array<string, mixed>,
     *     recommendation_scores: array<string, mixed>,
     *     action_plan: array<string, mixed>
     * }
     */
    private function buildAuditContext(string $file): array
    {
        [$account, $absolutePath] = $this->resolveAccountContext($file);

        $reportData = $this->resolveReportData($file, $absolutePath, (int) $account['aid']);
        $disputeWorkflow = $this->workflow->getCaseOverview(
            (int) $account['aid'],
            $account,
            [
                'clientData' => $reportData['clientData'],
                'derogatory' => $reportData['derogatory'],
            ]
        );

        $history = $this->reports->findHistoryForAccount((int) $account['aid']);

        $progressTimeline = $this->insightsBuilder->buildProgressTimeline($history, $reportData);
        $bureauComparison = $this->insightsBuilder->buildBureauComparison(
            $reportData['clientData'],
            $reportData['creditInfo'],
            $reportData['derogatory']
        );
        $recommendationScores = $this->insightsBuilder->buildRecommendationScores(
            $reportData,
            $progressTimeline,
            $disputeWorkflow
        );
        $actionPlan = $this->insightsBuilder->buildActionPlan(
            $recommendationScores,
            $disputeWorkflow,
            $progressTimeline
        );

        return [
            'account' => $account,
            'report_data' => $reportData,
            'dispute_workflow' => $disputeWorkflow,
            'progress_timeline' => $progressTimeline,
            'bureau_comparison' => $bureauComparison,
            'recommendation_scores' => $recommendationScores,
            'action_plan' => $actionPlan,
        ];
    }

    /**
     * @return array{0: array<string, mixed>, 1: string}
     */
    private function resolveAccountContext(string $file): array
    {
        try {
            $absolutePath = $this->storage->resolvePath($file);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $account = $this->accounts->findAccountByFile($file);
        if (!$account) {
            throw new NotFoundHttpException('Account not found for file');
        }

        return [$account, $absolutePath];
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        $value = trim($value, '-');

        return $value !== '' ? $value : 'report';
    }
}
