<?php

namespace App\Entity;

use App\Repository\AuditLogEntryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditLogEntryRepository::class)]
#[ORM\Table(name: 'audit_log_entries')]
#[ORM\Index(name: 'idx_audit_log_event', columns: ['event_type'])]
#[ORM\Index(name: 'idx_audit_log_actor', columns: ['actor_id'])]
#[ORM\Index(name: 'idx_audit_log_account', columns: ['account_aid'])]
class AuditLogEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'event_type', type: 'string', length: 120)]
    private string $eventType;

    #[ORM\Column(name: 'actor_type', type: 'string', length: 60)]
    private string $actorType = 'user';

    #[ORM\Column(name: 'actor_id', type: 'string', length: 191)]
    private string $actorId;

    #[ORM\Column(name: 'account_aid', type: 'integer', nullable: true)]
    private ?int $accountAid = null;

    #[ORM\Column(name: 'subject_type', type: 'string', length: 120, nullable: true)]
    private ?string $subjectType = null;

    #[ORM\Column(name: 'subject_id', type: 'string', length: 191, nullable: true)]
    private ?string $subjectId = null;

    #[ORM\Column(name: 'ip_address', type: 'string', length: 45, nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(name: 'user_agent', type: 'string', length: 255, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'json')]
    private array $metadata = [];

    #[ORM\Column(name: 'occurred_at', type: 'datetime_immutable')]
    private DateTimeImmutable $occurredAt;

    public function __construct()
    {
        $this->occurredAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEventType(): string
    {
        return $this->eventType;
    }

    public function setEventType(string $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function getActorType(): string
    {
        return $this->actorType;
    }

    public function setActorType(string $actorType): self
    {
        $this->actorType = $actorType;

        return $this;
    }

    public function getActorId(): string
    {
        return $this->actorId;
    }

    public function setActorId(string $actorId): self
    {
        $this->actorId = $actorId;

        return $this;
    }

    public function getAccountAid(): ?int
    {
        return $this->accountAid;
    }

    public function setAccountAid(?int $accountAid): self
    {
        $this->accountAid = $accountAid;

        return $this;
    }

    public function getSubjectType(): ?string
    {
        return $this->subjectType;
    }

    public function setSubjectType(?string $subjectType): self
    {
        $this->subjectType = $subjectType;

        return $this;
    }

    public function getSubjectId(): ?string
    {
        return $this->subjectId;
    }

    public function setSubjectId(?string $subjectId): self
    {
        $this->subjectId = $subjectId;

        return $this;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): self
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getOccurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function setOccurredAt(DateTimeImmutable $occurredAt): self
    {
        $this->occurredAt = $occurredAt;

        return $this;
    }
}
