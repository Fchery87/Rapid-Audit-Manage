<?php

namespace App\Entity;

use App\Repository\CollaborationNoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollaborationNoteRepository::class)]
#[ORM\Table(name: 'collaboration_notes')]
#[ORM\Index(name: 'idx_collaboration_notes_case', columns: ['dispute_case_id'])]
class CollaborationNote
{
    public const VISIBILITY_INTERNAL = 'internal';
    public const VISIBILITY_CLIENT = 'client';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: DisputeCase::class, inversedBy: 'notes')]
    #[ORM\JoinColumn(name: 'dispute_case_id', nullable: false, onDelete: 'CASCADE')]
    private ?DisputeCase $disputeCase = null;

    #[ORM\Column(type: 'string', length: 120)]
    private string $author;

    #[ORM\Column(type: 'text')]
    private string $message;

    #[ORM\Column(type: 'string', length: 32)]
    private string $visibility = self::VISIBILITY_INTERNAL;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

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

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getVisibility(): string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
