<?php

namespace App\Service\Security;

use App\Entity\AuditLogEntry;
use App\Entity\ClientDocument;
use App\Entity\CreditReport;
use App\Entity\DisputeTask;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AuditTrailService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestStack $requestStack,
        #[Autowire(service: 'monolog.logger.audit')]
        private readonly LoggerInterface $logger,
    ) {
    }

    public function record(
        string $eventType,
        ?int $accountAid,
        string $actorId,
        array $metadata = [],
        ?string $subjectType = null,
        ?string $subjectId = null,
        string $actorType = 'user'
    ): void {
        $entry = (new AuditLogEntry())
            ->setEventType($eventType)
            ->setAccountAid($accountAid)
            ->setActorId($actorId)
            ->setActorType($actorType)
            ->setSubjectType($subjectType)
            ->setSubjectId($subjectId)
            ->setMetadata($metadata)
            ->setOccurredAt(new DateTimeImmutable());

        [$ip, $userAgent] = $this->resolveRequestContext();
        $entry->setIpAddress($ip);
        $entry->setUserAgent($userAgent);

        $this->entityManager->persist($entry);
        $this->entityManager->flush();

        $this->logger->info('audit.event', [
            'event' => $eventType,
            'actor_id' => $actorId,
            'actor_type' => $actorType,
            'account_aid' => $accountAid,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'metadata' => $metadata,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'occurred_at' => $entry->getOccurredAt()->format(DATE_ATOM),
        ]);
    }

    public function recordClientAuditView(CreditReport $report, string $actorId, array $metadata = []): void
    {
        $subjectId = $report->getId() !== null ? (string) $report->getId() : $report->getFilename();
        $metadata = array_merge(['filename' => $report->getFilename()], $metadata);

        $this->record(
            eventType: 'client.audit.viewed',
            accountAid: $report->getAccountAid(),
            actorId: $actorId,
            metadata: $metadata,
            subjectType: 'credit_report',
            subjectId: $subjectId,
            actorType: 'client'
        );
    }

    public function recordClientDocumentUploaded(ClientDocument $document, string $actorId): void
    {
        $this->record(
            eventType: 'client.document.uploaded',
            accountAid: $document->getAccountAid(),
            actorId: $actorId,
            metadata: [
                'original_name' => $document->getOriginalName(),
                'mime_type' => $document->getMimeType(),
                'size' => $document->getSize(),
            ],
            subjectType: 'client_document',
            subjectId: $document->getId() !== null ? (string) $document->getId() : $document->getStoredName(),
            actorType: 'client'
        );
    }

    public function recordClientDocumentDownloaded(ClientDocument $document, string $actorId): void
    {
        $this->record(
            eventType: 'client.document.downloaded',
            accountAid: $document->getAccountAid(),
            actorId: $actorId,
            metadata: [
                'original_name' => $document->getOriginalName(),
                'mime_type' => $document->getMimeType(),
                'size' => $document->getSize(),
            ],
            subjectType: 'client_document',
            subjectId: $document->getId() !== null ? (string) $document->getId() : $document->getStoredName(),
            actorType: 'client'
        );
    }

    public function recordClientTaskAcknowledged(DisputeTask $task, string $actorId): void
    {
        $case = $task->getDisputeCase();
        $accountAid = $case ? $case->getAccountAid() : null;

        $this->record(
            eventType: 'client.task.acknowledged',
            accountAid: $accountAid,
            actorId: $actorId,
            metadata: [
                'task_id' => $task->getId(),
                'description' => $task->getDescription(),
            ],
            subjectType: 'dispute_task',
            subjectId: $task->getId() !== null ? (string) $task->getId() : null,
            actorType: 'client'
        );
    }

    public function recordAnalystReportViewed(string $actorId, ?int $accountAid, string $reportIdentifier, array $metadata = []): void
    {
        $this->record(
            eventType: 'analyst.report.viewed',
            accountAid: $accountAid,
            actorId: $actorId,
            metadata: array_merge(['report' => $reportIdentifier], $metadata),
            subjectType: 'credit_report',
            subjectId: $reportIdentifier,
            actorType: 'analyst'
        );
    }

    private function resolveRequestContext(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request instanceof Request) {
            return [null, null];
        }

        return [
            $request->getClientIp(),
            $request->headers->get('User-Agent'),
        ];
    }
}
