<?php

namespace App\Entity;

use App\Repository\DisputeTaskRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: DisputeTaskRepository::class)]
#[ORM\Table(name: 'dispute_tasks')]
#[ORM\Index(name: 'idx_dispute_tasks_case', columns: ['dispute_case_id'])]
#[ORM\Index(name: 'idx_dispute_tasks_status', columns: ['status'])]
class DisputeTask
{
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_READY = 'ready';
    public const STATUS_DONE = 'done';

    public const STATUSES = [
        self::STATUS_OPEN,
        self::STATUS_IN_PROGRESS,
        self::STATUS_READY,
        self::STATUS_DONE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: DisputeCase::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'dispute_case_id', nullable: false, onDelete: 'CASCADE')]
    private ?DisputeCase $disputeCase = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $description;

    #[ORM\Column(name: 'assigned_to', type: 'string', length: 60)]
    private string $assignedTo;

    #[ORM\Column(name: 'client_visible', type: 'boolean')]
    private bool $clientVisible = true;

    #[ORM\Column(type: 'string', length: 32)]
    private string $status = self::STATUS_OPEN;

    #[ORM\Column(name: 'due_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dueAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'completed_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(name: 'created_by', type: 'string', length: 120)]
    private string $createdBy;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDisputeCase(): ?DisputeCase
    {
        return $this->disputeCase;
    }

    public function setDisputeCase(?DisputeCase $disputeCase): self
    {
        $this->disputeCase = $disputeCase;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getAssignedTo(): string
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(string $assignedTo): self
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }

    public function isClientVisible(): bool
    {
        return $this->clientVisible;
    }

    public function setClientVisible(bool $clientVisible): self
    {
        $this->clientVisible = $clientVisible;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException(sprintf('Unknown task status "%s".', $status));
        }

        $this->status = $status;
        if ($status === self::STATUS_DONE) {
            $this->completedAt = new \DateTimeImmutable();
        } else {
            $this->completedAt = null;
        }

        return $this;
    }

    public function getDueAt(): ?\DateTimeInterface
    {
        return $this->dueAt;
    }

    public function setDueAt(?\DateTimeInterface $dueAt): self
    {
        $this->dueAt = $dueAt;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeInterface
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeInterface $completedAt): self
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }
}
