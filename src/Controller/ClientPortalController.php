<?php

namespace App\Controller;

use App\Entity\ClientDocument;
use App\Entity\CreditReport;
use App\Entity\User;
use App\Repository\ClientDocumentRepository;
use App\Repository\CreditReportRepository;
use App\Repository\DisputeTaskRepository;
use App\Service\Account\AccountLocator;
use App\Service\Client\ClientDocumentStorage;
use App\Service\Dispute\DisputeWorkflowService;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[IsGranted('ROLE_CLIENT')]
class ClientPortalController extends AbstractController
{
    /**
     * @var array<string, array<int, array<string, mixed>>>
     */
    private array $accountCache = [];

    public function __construct(
        private readonly AccountLocator $accountLocator,
        private readonly CreditReportRepository $creditReports,
        private readonly DisputeTaskRepository $taskRepository,
        private readonly DisputeWorkflowService $workflow,
        private readonly ClientDocumentRepository $documents,
        private readonly ClientDocumentStorage $storage,
    ) {
    }

    #[Route('/client', name: 'app_client_dashboard', methods: ['GET'])]
    public function dashboard(Request $request): Response
    {
        $user = $this->requireClientUser();
        $accounts = $this->getAccountsForUser($user);

        if ($accounts === []) {
            return $this->render('client/dashboard.html.twig', [
                'accounts' => [],
                'active_account' => null,
                'audits' => [],
                'tasks' => [],
                'documents' => [],
            ]);
        }

        $selectedAid = $request->query->getInt('aid');
        $activeAccount = $this->matchAccount($accounts, $selectedAid) ?? $accounts[0];
        $activeAid = (int) $activeAccount['aid'];

        $audits = $this->creditReports->findRecentForAccount($activeAid, 10);
        $tasks = $this->taskRepository->findClientTasksForAccount($activeAid);
        $documents = $this->documents->findForAccount($activeAid);

        return $this->render('client/dashboard.html.twig', [
            'accounts' => $accounts,
            'active_account' => $activeAccount,
            'audits' => $audits,
            'tasks' => $tasks,
            'documents' => $documents,
        ]);
    }

    #[Route('/client/tasks/{taskId}/acknowledge', name: 'app_client_task_acknowledge', methods: ['POST'])]
    public function acknowledgeTask(int $taskId, Request $request): RedirectResponse
    {
        $user = $this->requireClientUser();

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('ack_task_' . $taskId, (string) $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        try {
            $task = $this->workflow->requireTask($taskId);
        } catch (InvalidArgumentException $exception) {
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        }

        $case = $task->getDisputeCase();
        if ($case === null) {
            throw new NotFoundHttpException('Task is not attached to a dispute case.');
        }

        $accountAid = $case->getAccountAid();
        $this->requireAccount($user, $accountAid);

        if (!$task->isClientVisible()) {
            throw $this->createAccessDeniedException('Task is not available to clients.');
        }

        $this->workflow->acknowledgeTask($task, $user->getUserIdentifier());
        $this->addFlash('success', 'client.dashboard.flash_acknowledged');

        return $this->redirectToRoute('app_client_dashboard', ['aid' => $accountAid]);
    }

    #[Route('/client/documents', name: 'app_client_document_upload', methods: ['POST'])]
    public function uploadDocument(Request $request): RedirectResponse
    {
        $user = $this->requireClientUser();
        $accountAid = $request->request->getInt('account_aid');
        $this->requireAccount($user, $accountAid);

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('client_upload_' . $accountAid, (string) $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $file = $request->files->get('document');
        if (!$file instanceof UploadedFile) {
            $this->addFlash('error', 'client.dashboard.flash_upload_missing');

            return $this->redirectToRoute('app_client_dashboard', ['aid' => $accountAid]);
        }

        try {
            $this->storage->store($file, $accountAid, $user->getUserIdentifier());
            $this->addFlash('success', 'client.dashboard.flash_upload_success');
        } catch (RuntimeException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_client_dashboard', ['aid' => $accountAid]);
    }

    #[Route('/client/documents/{id}', name: 'app_client_document_download', methods: ['GET'])]
    public function downloadDocument(int $id): BinaryFileResponse
    {
        $user = $this->requireClientUser();
        $document = $this->documents->find($id);
        if (!$document instanceof ClientDocument) {
            throw new NotFoundHttpException('Document not found.');
        }

        $this->requireAccount($user, $document->getAccountAid());

        try {
            $path = $this->storage->resolvePath($document);
        } catch (RuntimeException $exception) {
            throw new NotFoundHttpException($exception->getMessage(), $exception);
        }

        return $this->file(
            $path,
            $document->getOriginalName(),
            ResponseHeaderBag::DISPOSITION_ATTACHMENT
        );
    }

    #[Route('/client/audits/{id}', name: 'app_client_audit_view', methods: ['GET'])]
    public function viewAudit(int $id): Response
    {
        $user = $this->requireClientUser();
        $report = $this->creditReports->find($id);
        if (!$report instanceof CreditReport) {
            throw new NotFoundHttpException('Audit not found.');
        }

        $accountAid = $report->getAccountAid();
        $account = $this->requireAccount($user, $accountAid);

        return $this->render('client/audit.html.twig', [
            'report' => $report,
            'account' => $account,
            'client_data' => $report->getClientData(),
            'derogatory' => $report->getDerogatoryAccounts(),
            'inquiries' => $report->getInquiryAccounts(),
            'public_records' => $report->getPublicRecords(),
            'credit_info' => $report->getCreditInfo(),
        ]);
    }

    private function requireClientUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Client authentication required.');
        }

        return $user;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getAccountsForUser(User $user): array
    {
        $email = (string) $user->getEmail();
        if ($email === '') {
            return [];
        }

        if (!array_key_exists($email, $this->accountCache)) {
            $this->accountCache[$email] = $this->accountLocator->findAccountsForEmail($email);
        }

        return $this->accountCache[$email];
    }

    /**
     * @param array<int, array<string, mixed>> $accounts
     */
    private function matchAccount(array $accounts, int $aid): ?array
    {
        foreach ($accounts as $account) {
            if ((int) ($account['aid'] ?? 0) === $aid) {
                return $account;
            }
        }

        return null;
    }

    private function requireAccount(User $user, int $accountAid): array
    {
        $accounts = $this->getAccountsForUser($user);
        foreach ($accounts as $account) {
            if ((int) ($account['aid'] ?? 0) === $accountAid) {
                return $account;
            }
        }

        throw $this->createAccessDeniedException('Account is not associated with this login.');
    }
}
