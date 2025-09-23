<?php

namespace App\Entity;

use App\Repository\DisputeLetterRepository;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Entity(repositoryClass: DisputeLetterRepository::class)]
#[ORM\Table(name: 'dispute_letters')]
#[ORM\Index(name: 'idx_dispute_letters_case', columns: ['dispute_case_id'])]
#[ORM\Index(name: 'idx_dispute_letters_status', columns: ['status'])]
class DisputeLetter
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_READY = 'ready';
    public const STATUS_SENT = 'sent';

    public const STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_READY,
        self::STATUS_SENT,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: DisputeCase::class, inversedBy: 'letters')]
    #[ORM\JoinColumn(name: 'dispute_case_id', nullable: false, onDelete: 'CASCADE')]
    private ?DisputeCase $disputeCase = null;

    #[ORM\Column(type: 'string', length: 120)]
    private string $bureau;

    #[ORM\Column(type: 'string', length: 120)]
    private string $preparedBy;

    #[ORM\Column(type: 'string', length: 32)]
    private string $status = self::STATUS_DRAFT;

    #[ORM\Column(type: 'text')]
    private string $body;

    /**
     * @var array<int, array<string, mixed>>
     */
    #[ORM\Column(type: 'json')]
    private array $items = [];

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(name: 'sent_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sentAt = null;

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

    public function getBureau(): string
    {
        return $this->bureau;
    }

    public function setBureau(string $bureau): self
    {
        $this->bureau = $bureau;
        return $this;
    }

    public function getPreparedBy(): string
    {
        return $this->preparedBy;
    }

    public function setPreparedBy(string $preparedBy): self
    {
        $this->preparedBy = $preparedBy;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException(sprintf('Unknown letter status "%s".', $status));
        }

        $this->status = $status;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function markSent(\DateTimeInterface $sentAt): self
    {
        $this->sentAt = $sentAt;
        $this->status = self::STATUS_SENT;
        return $this;
    }
}
