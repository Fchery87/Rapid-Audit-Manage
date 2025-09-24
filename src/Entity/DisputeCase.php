<?php

namespace App\Entity;

use App\Repository\DisputeCaseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: DisputeCaseRepository::class)]
#[ORM\Table(name: 'dispute_cases')]
#[ORM\Index(name: 'idx_dispute_cases_account', columns: ['account_aid'])]
class DisputeCase
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_OPEN = 'open';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_READY_TO_SEND = 'ready_to_send';
    public const STATUS_SENT = 'sent';
    public const STATUS_RESOLVED = 'resolved';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_OPEN,
        self::STATUS_IN_REVIEW,
        self::STATUS_READY_TO_SEND,
        self::STATUS_SENT,
        self::STATUS_RESOLVED,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'account_aid', type: 'integer')]
    private int $accountAid;

    #[ORM\Column(type: 'string', length: 180)]
    private string $title;

    #[ORM\Column(type: 'string', length: 40)]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $summary = null;

    /**
     * List of targeted dispute items derived from the credit report snapshot.
     * @var array<int, array<string, mixed>>
     */
    #[ORM\Column(name: 'target_items', type: 'json')]
    private array $targetItems = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    /** @var Collection<int, DisputeTask> */
    #[ORM\OneToMany(mappedBy: 'disputeCase', targetEntity: DisputeTask::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $tasks;

    /** @var Collection<int, DisputeLetter> */
    #[ORM\OneToMany(mappedBy: 'disputeCase', targetEntity: DisputeLetter::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $letters;

    /** @var Collection<int, CollaborationNote> */
    #[ORM\OneToMany(mappedBy: 'disputeCase', targetEntity: CollaborationNote::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $notes;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->tasks = new ArrayCollection();
        $this->letters = new ArrayCollection();
        $this->notes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountAid(): int
    {
        return $this->accountAid;
    }

    public function setAccountAid(int $accountAid): self
    {
        $this->accountAid = $accountAid;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException(sprintf('Unknown dispute case status "%s".', $status));
        }

        $this->status = $status;

        return $this;
    }

    /**
     * @return array<int, string>
     */
    public static function activeStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_OPEN,
            self::STATUS_IN_REVIEW,
            self::STATUS_READY_TO_SEND,
            self::STATUS_SENT,
        ];
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): self
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getTargetItems(): array
    {
        return $this->targetItems;
    }

    /**
     * @param array<int, array<string, mixed>> $targetItems
     */
    public function setTargetItems(array $targetItems): self
    {
        $this->targetItems = $targetItems;
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

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function touch(): self
    {
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    /**
     * @return Collection<int, DisputeTask>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(DisputeTask $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
            $task->setDisputeCase($this);
        }

        return $this;
    }

    public function removeTask(DisputeTask $task): self
    {
        if ($this->tasks->removeElement($task)) {
            if ($task->getDisputeCase() === $this) {
                $task->setDisputeCase(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DisputeLetter>
     */
    public function getLetters(): Collection
    {
        return $this->letters;
    }

    public function addLetter(DisputeLetter $letter): self
    {
        if (!$this->letters->contains($letter)) {
            $this->letters->add($letter);
            $letter->setDisputeCase($this);
        }

        return $this;
    }

    public function removeLetter(DisputeLetter $letter): self
    {
        if ($this->letters->removeElement($letter)) {
            if ($letter->getDisputeCase() === $this) {
                $letter->setDisputeCase(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollaborationNote>
     */
    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(CollaborationNote $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes->add($note);
            $note->setDisputeCase($this);
        }

        return $this;
    }

    public function removeNote(CollaborationNote $note): self
    {
        if ($this->notes->removeElement($note)) {
            if ($note->getDisputeCase() === $this) {
                $note->setDisputeCase(null);
            }
        }

        return $this;
    }
}
