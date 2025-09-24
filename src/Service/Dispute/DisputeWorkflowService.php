<?php

namespace App\Service\Dispute;

use App\Entity\CollaborationNote;
use App\Entity\DisputeCase;
use App\Entity\DisputeLetter;
use App\Entity\DisputeTask;
use App\Repository\DisputeCaseRepository;
use App\Repository\DisputeLetterRepository;
use App\Repository\DisputeTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;

class DisputeWorkflowService
{
    public function __construct(
        private readonly DisputeCaseRepository $cases,
        private readonly DisputeTaskRepository $tasks,
        private readonly DisputeLetterRepository $letters,
        private readonly EntityManagerInterface $entityManager,
        private readonly DisputeLetterGenerator $letterGenerator,
    ) {
    }

    /**
     * @param array<string, mixed> $account
     * @param array<string, mixed> $reportData
     */
    public function getCaseOverview(int $accountAid, array $account, array $reportData): array
    {
        $case = $this->getOrCreatePrimaryCase(
            $accountAid,
            $account,
            $reportData['derogatory'] ?? [],
            $reportData['clientData'] ?? []
        );

        return $this->buildSnapshot($case);
    }

    /**
     * @param array<string, mixed> $account
     * @param array<string, mixed> $clientProfile
     * @param array<string, mixed> $derogatory
     */
    public function getOrCreatePrimaryCase(int $accountAid, array $account, array $derogatory, array $clientProfile): DisputeCase
    {
        $case = $this->cases->findPrimaryOpenCase($accountAid);
        $wasNew = false;

        if (!$case instanceof DisputeCase) {
            $case = (new DisputeCase())
                ->setAccountAid($accountAid)
                ->setTitle(sprintf('%s %s – Dispute Plan', $account['first_name'] ?? 'Client', $account['last_name'] ?? ''))
                ->setStatus(DisputeCase::STATUS_OPEN);
            $case->setSummary($this->buildSummary($account, $clientProfile, $derogatory));
            $case->setTargetItems($this->selectTargetItems($derogatory));
            $this->entityManager->persist($case);
            $wasNew = true;
        } else {
            $case->touch();
            if (!$case->getSummary()) {
                $case->setSummary($this->buildSummary($account, $clientProfile, $derogatory));
            }
            if (!$case->getTargetItems()) {
                $case->setTargetItems($this->selectTargetItems($derogatory));
            }
        }

        $this->ensureSeedTasks($case, $case->getTargetItems());

        if ($wasNew) {
            $this->entityManager->flush();
        }

        return $case;
    }

    /**
     * @return array<string, mixed>
     */
    public function buildSnapshot(DisputeCase $case): array
    {
        $tasks = $case->getTasks()->toArray();
        $letters = $case->getLetters()->toArray();
        $notes = $case->getNotes()->toArray();

        $progress = $this->calculateProgress($tasks);

        return [
            'case' => [
                'id' => $case->getId(),
                'title' => $case->getTitle(),
                'status' => $case->getStatus(),
                'summary' => $case->getSummary(),
                'target_items' => $case->getTargetItems(),
                'created_at' => $case->getCreatedAt(),
                'updated_at' => $case->getUpdatedAt(),
                'progress' => $progress,
            ],
            'tasks' => array_map(
                static fn(DisputeTask $task): array => [
                    'id' => $task->getId(),
                    'description' => $task->getDescription(),
                    'assigned_to' => $task->getAssignedTo(),
                    'client_visible' => $task->isClientVisible(),
                    'status' => $task->getStatus(),
                    'due_at' => $task->getDueAt(),
                    'created_at' => $task->getCreatedAt(),
                    'completed_at' => $task->getCompletedAt(),
                    'created_by' => $task->getCreatedBy(),
                ],
                $tasks
            ),
            'letters' => array_map(
                static fn(DisputeLetter $letter): array => [
                    'id' => $letter->getId(),
                    'bureau' => $letter->getBureau(),
                    'status' => $letter->getStatus(),
                    'body' => $letter->getBody(),
                    'items' => $letter->getItems(),
                    'created_at' => $letter->getCreatedAt(),
                    'sent_at' => $letter->getSentAt(),
                    'prepared_by' => $letter->getPreparedBy(),
                ],
                $letters
            ),
            'notes' => array_map(
                static fn(CollaborationNote $note): array => [
                    'id' => $note->getId(),
                    'author' => $note->getAuthor(),
                    'message' => $note->getMessage(),
                    'visibility' => $note->getVisibility(),
                    'created_at' => $note->getCreatedAt(),
                ],
                $notes
            ),
            'recommended_items' => $case->getTargetItems(),
        ];
    }

    /**
     * @param array<int, DisputeTask> $tasks
     * @return array<string, mixed>
     */
    private function calculateProgress(array $tasks): array
    {
        $total = count($tasks);
        $completed = 0;
        $clientOpen = 0;

        foreach ($tasks as $task) {
            if ($task->getStatus() === DisputeTask::STATUS_DONE) {
                ++$completed;
            }
            if ($task->isClientVisible() && $task->getStatus() !== DisputeTask::STATUS_DONE) {
                ++$clientOpen;
            }
        }

        $percent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => $percent,
            'open_client_tasks' => $clientOpen,
        ];
    }


    public function addTask(DisputeCase $case, string $description, string $assignedTo, bool $clientVisible, ?\DateTimeInterface $dueAt, string $createdBy, bool $flush = true): DisputeTask
    {
        $task = (new DisputeTask())
            ->setDescription($description)
            ->setAssignedTo($assignedTo)
            ->setClientVisible($clientVisible)
            ->setCreatedBy($createdBy)
            ->setStatus(DisputeTask::STATUS_OPEN)
            ->setDueAt($dueAt);

        $case->addTask($task);
        $case->touch();

        $this->entityManager->persist($task);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $task;
    }

    public function completeTask(DisputeTask $task): void
    {
        $task->setStatus(DisputeTask::STATUS_DONE);
        if ($task->getDisputeCase()) {
            $task->getDisputeCase()->touch();
        }
        $this->entityManager->flush();
    }

    public function startTask(DisputeTask $task): void
    {
        $task->setStatus(DisputeTask::STATUS_IN_PROGRESS);
        if ($task->getDisputeCase()) {
            $task->getDisputeCase()->touch();
        }
        $this->entityManager->flush();
    }

    public function reopenTask(DisputeTask $task): void
    {
        $task->setStatus(DisputeTask::STATUS_OPEN);
        if ($task->getDisputeCase()) {
            $task->getDisputeCase()->touch();
        }
        $this->entityManager->flush();
    }

    public function addNote(DisputeCase $case, string $author, string $message, string $visibility): CollaborationNote
    {
        if (!in_array($visibility, [CollaborationNote::VISIBILITY_CLIENT, CollaborationNote::VISIBILITY_INTERNAL], true)) {
            throw new InvalidArgumentException('Invalid note visibility.');
        }

        $note = (new CollaborationNote())
            ->setAuthor($author)
            ->setMessage($message)
            ->setVisibility($visibility);

        $case->addNote($note);
        $case->touch();

        $this->entityManager->persist($note);
        $this->entityManager->flush();

        return $note;
    }

    /**
     * @param array<string, mixed> $account
     * @param array<string, mixed> $clientProfile
     * @param array<int, array<string, mixed>> $items
     */
    public function prepareLetter(DisputeCase $case, array $account, array $clientProfile, array $items, string $bureau, string $preparedBy): DisputeLetter
    {
        if ($items === []) {
            throw new InvalidArgumentException('At least one dispute item is required to generate a letter.');
        }

        $body = $this->letterGenerator->generateLetter($case, $items, $account, $clientProfile, $bureau, $preparedBy);

        $letter = (new DisputeLetter())
            ->setDisputeCase($case)
            ->setBureau($bureau)
            ->setPreparedBy($preparedBy)
            ->setItems($items)
            ->setBody($body)
            ->setStatus(DisputeLetter::STATUS_READY);

        $case->addLetter($letter);
        $case->setStatus(DisputeCase::STATUS_READY_TO_SEND);
        $case->touch();

        $this->entityManager->persist($letter);
        $this->entityManager->flush();

        return $letter;
    }

    public function markLetterSent(DisputeLetter $letter): void
    {
        $letter->markSent(new \DateTimeImmutable());
        $case = $letter->getDisputeCase();
        if ($case instanceof DisputeCase) {
            $case->setStatus(DisputeCase::STATUS_SENT);
            $case->touch();
        }
        $this->entityManager->flush();
    }

    public function requireCase(int $caseId): DisputeCase
    {
        $case = $this->cases->find($caseId);
        if (!$case instanceof DisputeCase) {
            throw new InvalidArgumentException('Dispute case not found.');
        }

        return $case;
    }

    public function requireTask(int $taskId): DisputeTask
    {
        $task = $this->tasks->find($taskId);
        if (!$task instanceof DisputeTask) {
            throw new InvalidArgumentException('Task not found.');
        }

        return $task;
    }

    public function requireLetter(int $letterId): DisputeLetter
    {
        $letter = $this->letters->find($letterId);
        if (!$letter instanceof DisputeLetter) {
            throw new InvalidArgumentException('Letter not found.');
        }

        return $letter;
    }

    /**
     * @param array<string, mixed> $account
     * @param array<string, mixed> $clientProfile
     * @param array<string, mixed> $derogatory
     */
    private function buildSummary(array $account, array $clientProfile, array $derogatory): string
    {
        $totalNegative = (int) ($derogatory['total'] ?? count($derogatory['accounts'] ?? []));
        $name = trim(($account['first_name'] ?? '') . ' ' . ($account['last_name'] ?? '')) ?: 'client';
        $score = $clientProfile['trans_union']['credit_score'] ?? null;

        $summary = sprintf('%s has %d derogatory tradelines targeted for dispute.', ucfirst($name), $totalNegative);
        if ($score) {
            $summary .= sprintf(' Current TransUnion score: %s.', $score);
        }

        return $summary;
    }

    /**
     * @param array<string, mixed> $derogatory
     * @return array<int, array<string, mixed>>
     */
    private function selectTargetItems(array $derogatory): array
    {
        $accounts = $derogatory['accounts'] ?? [];
        $targets = [];
        foreach ($accounts as $account) {
            $targets[] = [
                'account' => $account['account'] ?? 'Unknown creditor',
                'issue' => $account['unique_status'] ?? 'Unspecified issue',
                'bureaus' => $this->resolveBureaus($account),
                'trans_union_account_date' => $account['trans_union_account_date'] ?? null,
                'experian_account_date' => $account['experian_account_date'] ?? null,
                'equifax_account_date' => $account['equifax_account_date'] ?? null,
            ];

            if (count($targets) >= 3) {
                break;
            }
        }

        return $targets;
    }

    /**
     * @param array<string, mixed> $account
     * @return array<int, string>
     */
    private function resolveBureaus(array $account): array
    {
        $bureaus = [];
        if (!empty($account['trans_union_account_status'])) {
            $bureaus[] = 'TransUnion';
        }
        if (!empty($account['experian_account_status'])) {
            $bureaus[] = 'Experian';
        }
        if (!empty($account['equifax_account_status'])) {
            $bureaus[] = 'Equifax';
        }

        if ($bureaus === []) {
            $bureaus[] = 'All Bureaus';
        }

        return $bureaus;
    }

    /**
     * @param array<int, array<string, mixed>> $targetItems
     */
    private function ensureSeedTasks(DisputeCase $case, array $targetItems): void
    {
        $existing = [];
        foreach ($case->getTasks() as $task) {
            $existing[] = mb_strtolower($task->getDescription());
        }

        $systemCreator = 'System Planner';
        $tasksAdded = false;

        foreach ($targetItems as $item) {
            $description = sprintf('Draft dispute narrative for %s (%s)', $item['account'], implode('/', $item['bureaus'] ?? []));
            if (!in_array(mb_strtolower($description), $existing, true)) {
                $this->addTask($case, $description, 'Analyst', true, new \DateTimeImmutable('+3 days'), $systemCreator, false);
                $existing[] = mb_strtolower($description);
                $tasksAdded = true;
            }
        }

        $identityTask = 'Collect identity documents and proof of address';
        if (!in_array(mb_strtolower($identityTask), $existing, true)) {
            $this->addTask($case, $identityTask, 'Client', true, new \DateTimeImmutable('+5 days'), $systemCreator, false);
            $existing[] = mb_strtolower($identityTask);
            $tasksAdded = true;
        }

        $qaTask = 'Quality review dispute package before mailing';
        if (!in_array(mb_strtolower($qaTask), $existing, true)) {
            $this->addTask($case, $qaTask, 'Analyst', false, new \DateTimeImmutable('+7 days'), $systemCreator, false);
            $tasksAdded = true;
        }

        if ($tasksAdded) {
            $this->entityManager->flush();
        }
    }
}
