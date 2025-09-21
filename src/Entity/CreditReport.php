<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CreditReportRepository;

/**
 * @ORM\Entity(repositoryClass=CreditReportRepository::class)
 * @ORM\Table(name="credit_reports", indexes={
 *     @ORM\Index(name="idx_credit_reports_aid", columns={"account_aid"})
 * }, uniqueConstraints={
 *     @ORM\UniqueConstraint(name="uniq_credit_reports_filename", columns={"filename"})
 * })
 */
class CreditReport
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * Arbitrary link to legacy accounts.aid
     * @ORM\Column(name="account_aid", type="integer")
     */
    private int $accountAid;

    /**
     * Stored upload filename (under var/uploads)
     * @ORM\Column(type="string", length=255)
     */
    private string $filename;

    /**
     * @ORM\Column(name="parsed_at", type="datetime_immutable")
     */
    private \DateTimeImmutable $parsedAt;

    /**
     * @ORM\Column(name="client_data", type="json")
     */
    private array $clientData = [];

    /**
     * @ORM\Column(name="derogatory_accounts", type="json")
     */
    private array $derogatoryAccounts = [];

    /**
     * @ORM\Column(name="inquiry_accounts", type="json")
     */
    private array $inquiryAccounts = [];

    /**
     * @ORM\Column(name="public_records", type="json")
     */
    private array $publicRecords = [];

    /**
     * @ORM\Column(name="credit_info", type="json")
     */
    private array $creditInfo = [];

    /**
     * @ORM\Column(name="meta", type="json", nullable=true)
     */
    private ?array $meta = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountAid(): int
    {
        return $this->accountAid;
    }

    public function setAccountAid(int $aid): self
    {
        $this->accountAid = $aid;
        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;
        return $this;
    }

    public function getParsedAt(): \DateTimeImmutable
    {
        return $this->parsedAt;
    }

    public function setParsedAt(\DateTimeImmutable $parsedAt): self
    {
        $this->parsedAt = $parsedAt;
        return $this;
    }

    public function getClientData(): array
    {
        return $this->clientData;
    }

    public function setClientData(array $clientData): self
    {
        $this->clientData = $clientData;
        return $this;
    }

    public function getDerogatoryAccounts(): array
    {
        return $this->derogatoryAccounts;
    }

    public function setDerogatoryAccounts(array $derogatoryAccounts): self
    {
        $this->derogatoryAccounts = $derogatoryAccounts;
        return $this;
    }

    public function getInquiryAccounts(): array
    {
        return $this->inquiryAccounts;
    }

    public function setInquiryAccounts(array $inquiryAccounts): self
    {
        $this->inquiryAccounts = $inquiryAccounts;
        return $this;
    }

    public function getPublicRecords(): array
    {
        return $this->publicRecords;
    }

    public function setPublicRecords(array $publicRecords): self
    {
        $this->publicRecords = $publicRecords;
        return $this;
    }

    public function getCreditInfo(): array
    {
        return $this->creditInfo;
    }

    public function setCreditInfo(array $creditInfo): self
    {
        $this->creditInfo = $creditInfo;
        return $this;
    }

    public function getMeta(): ?array
    {
        return $this->meta;
    }

    public function setMeta(?array $meta): self
    {
        $this->meta = $meta;
        return $this;
    }
}