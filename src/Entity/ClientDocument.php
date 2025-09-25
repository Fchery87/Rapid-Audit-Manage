<?php

namespace App\Entity;

use App\Repository\ClientDocumentRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientDocumentRepository::class)]
#[ORM\Table(name: 'client_documents')]
#[ORM\Index(name: 'idx_client_documents_account', columns: ['account_aid'])]
class ClientDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'account_aid', type: 'integer')]
    private int $accountAid;

    #[ORM\Column(name: 'stored_name', type: 'string', length: 128)]
    private string $storedName;

    #[ORM\Column(name: 'original_name', type: 'string', length: 255)]
    private string $originalName;

    #[ORM\Column(name: 'mime_type', type: 'string', length: 120)]
    private string $mimeType;

    #[ORM\Column(type: 'integer')]
    private int $size;

    #[ORM\Column(name: 'uploaded_by', type: 'string', length: 120)]
    private string $uploadedBy;

    #[ORM\Column(name: 'uploaded_at', type: 'datetime_immutable')]
    private DateTimeImmutable $uploadedAt;

    public function __construct()
    {
        $this->uploadedAt = new DateTimeImmutable();
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

    public function getStoredName(): string
    {
        return $this->storedName;
    }

    public function setStoredName(string $storedName): self
    {
        $this->storedName = $storedName;

        return $this;
    }

    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    public function setOriginalName(string $originalName): self
    {
        $this->originalName = $originalName;

        return $this;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getUploadedBy(): string
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(string $uploadedBy): self
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    public function getUploadedAt(): DateTimeImmutable
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(DateTimeImmutable $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }
}
