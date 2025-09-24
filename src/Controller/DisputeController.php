<?php

namespace App\Controller;

use App\Entity\DisputeTask;
use App\Repository\CreditReportRepository;
use App\Service\Account\AccountLocator;
use App\Service\Dispute\DisputeWorkflowService;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ANALYST')]
#[Route('/disputes')]
class DisputeController extends AbstractController
{
    public function __construct(
        private readonly DisputeWorkflowService $workflow,
        private readonly AccountLocator $accounts,
        private readonly CreditReportRepository $reports,
    ) {
    }

    #[Route('/{aid}', name: 'app_dispute_dashboard', methods: ['GET'])]
    public function dashboard(int $aid): Response
    {
        $account = $this->accounts->findAccountByAid($aid);
        if (!$account) {
            throw new NotFoundHttpException('Account not found');
        }

        $latestReport = $this->reports->findOneBy(['accountAid' => $aid], ['parsedAt' => 'DESC']);
        $reportPayload = [
            'clientData' => $latestReport?->getClientData() ?? [],
            'derogatory' => $latestReport?->getDerogatoryAccounts() ?? ['accounts' => [], 'total' => 0],
        ];

        $overview = $this->workflow->getCaseOverview($aid, $account, $reportPayload);

        return $this->render('disputes/dashboard.html.twig', [
            'account' => $account,
            'case' => $overview['case'],
            'tasks' => $overview['tasks'],
            'letters' => $overview['letters'],
            'notes' => $overview['notes'],
            'recommended_items' => $overview['recommended_items'],
            'report_file' => $latestReport?->getFilename(),
        ]);
    }

    #[Route('/{aid}/tasks', name: 'app_dispute_task_create', methods: ['POST'])]
    public function createTask(int $aid, Request $request): RedirectResponse
    {
        $account = $this->accounts->findAccountByAid($aid);
        if (!$account) {
            throw new NotFoundHttpException('Account not found');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('create_task_' . $aid, $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $case = $this->workflow->requireCase($request->request->getInt('case_id'));

        $description = trim((string) $request->request->get('description', ''));
        if ($description === '') {
            $this->addFlash('error', 'Task description is required.');
            return $this->redirectToRoute('app_dispute_dashboard', ['aid' => $aid]);
        }

        $assignedTo = $request->request->get('assigned_to', 'Analyst');
        $clientVisible = $request->request->has('client_visible');
        $dueInput = $request->request->get('due_at');
        $dueAt = null;
        if ($dueInput) {
            try {
                $dueAt = new \DateTimeImmutable($dueInput);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Unable to parse due date.');
                return $this->redirectToRoute('app_dispute_dashboard', ['aid' => $aid]);
            }
        }

        $createdBy = $this->getUser()?->getUserIdentifier() ?? 'system';
        $this->workflow->addTask($case, $description, $assignedTo, $clientVisible, $dueAt, $createdBy);
        $this->addFlash('success', 'Task added to dispute plan.');

        return $this->redirectToRoute('app_dispute_dashboard', ['aid' => $aid]);
    }

    #[Route('/tasks/{taskId}/status', name: 'app_dispute_task_status', methods: ['POST'])]
    public function updateTaskStatus(int $taskId, Request $request): RedirectResponse
    {
        $aid = $request->request->getInt('account_aid');
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('task_status_' . $taskId, $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $task = $this->workflow->requireTask($taskId);
        $status = $request->request->get('status', DisputeTask::STATUS_IN_PROGRESS);

        if ($status === DisputeTask::STATUS_DONE) {
            $this->workflow->completeTask($task);
            $this->addFlash('success', 'Task marked complete.');
        } elseif ($status === DisputeTask::STATUS_IN_PROGRESS) {
            $this->workflow->startTask($task);
            $this->addFlash('success', 'Task moved in progress.');
        } else {
            $this->workflow->reopenTask($task);
            $this->addFlash('info', 'Task re-opened.');
        }

        return $this->redirectToRoute('app_dispute_dashboard', ['aid' => $aid]);
    }

    #[Route('/{aid}/notes', name: 'app_dispute_note_create', methods: ['POST'])]
    public function addNote(int $aid, Request $request): RedirectResponse
    {
        $account = $this->accounts->findAccountByAid($aid);
        if (!$account) {
            throw new NotFoundHttpException('Account not found');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('add_note_' . $aid, $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $case = $this->workflow->requireCase($request->request->getInt('case_id'));
        $message = trim((string) $request->request->get('message', ''));
        if ($message === '') {
            $this->addFlash('error', 'Note cannot be empty.');
            return $this->redirectToRoute('app_dispute_dashboard', ['aid' => $aid]);
        }

        $visibility = $request->request->get('visibility', 'internal');
        try {
            $this->workflow->addNote($case, $this->getUser()?->getUserIdentifier() ?? 'system', $message, $visibility);
            $this->addFlash('success', 'Note added to dispute file.');
        } catch (InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_dispute_dashboard', ['aid' => $aid]);
    }

    #[Route('/{aid}/letters', name: 'app_dispute_letter_create', methods: ['POST'])]
    public function generateLetter(int $aid, Request $request): RedirectResponse
    {
        $account = $this->accounts->findAccountByAid($aid);
        if (!$account) {
            throw new NotFoundHttpException('Account not found');
        }

        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('prepare_letter_' . $aid, $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $case = $this->workflow->requireCase($request->request->getInt('case_id'));
        $selectedIndexes = $request->request->all('items');
        $targets = $case->getTargetItems();
        $items = [];
        foreach ($selectedIndexes as $index) {
            if (isset($targets[$index])) {
                $items[] = $targets[$index];
            }
        }
        if ($items === []) {
            $items = $targets;
        }

        $bureau = $request->request->get('bureau', 'TransUnion');
        $latestReport = $this->reports->findOneBy(['accountAid' => $aid], ['parsedAt' => 'DESC']);
        $clientProfile = $latestReport?->getClientData() ?? [];

        try {
            $letter = $this->workflow->prepareLetter($case, $account, $clientProfile, $items, $bureau, $this->getUser()?->getUserIdentifier() ?? 'system');
            if ($request->request->has('mark_sent')) {
                $this->workflow->markLetterSent($letter);
            }
            $this->addFlash('success', sprintf('Prepared dispute letter for %s.', $bureau));
        } catch (InvalidArgumentException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('app_dispute_dashboard', ['aid' => $aid]);
    }

    #[Route('/letters/{letterId}/send', name: 'app_dispute_letter_send', methods: ['POST'])]
    public function markLetterSent(int $letterId, Request $request): RedirectResponse
    {
        $aid = $request->request->getInt('account_aid');
        $token = $request->request->get('_token');
        if (!$this->isCsrfTokenValid('send_letter_' . $letterId, $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $letter = $this->workflow->requireLetter($letterId);
        $this->workflow->markLetterSent($letter);
        $this->addFlash('success', 'Letter marked as sent.');

        return $this->redirectToRoute('app_dispute_dashboard', ['aid' => $aid]);
    }
}
