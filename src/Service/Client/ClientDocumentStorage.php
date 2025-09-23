<?php

namespace App\Service\Client;

use App\Entity\ClientDocument;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class ClientDocumentStorage
{
    private const MAX_FILE_BYTES = 15_728_640; // 15 MB

    /**
     * @var string[]
     */
    private const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/tiff',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
    ];

    private readonly string $storageDir;

    public function __construct(
        KernelInterface $kernel,
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->storageDir = $kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'client-documents';
    }

    public function store(UploadedFile $file, int $accountAid, string $uploadedBy): ClientDocument
    {
        $this->assertValid($file);

        $directory = $this->getAccountDirectory($accountAid);
        $storedName = $this->generateStoredName($file);

        $file->move($directory, $storedName);

        $document = (new ClientDocument())
            ->setAccountAid($accountAid)
            ->setStoredName($storedName)
            ->setOriginalName($this->sanitizeOriginalName($file->getClientOriginalName()))
            ->setMimeType($file->getMimeType() ?? $file->getClientMimeType() ?? 'application/octet-stream')
            ->setSize((int) $file->getSize())
            ->setUploadedBy($uploadedBy)
            ->setUploadedAt(new DateTimeImmutable());

        $this->entityManager->persist($document);
        $this->entityManager->flush();

        return $document;
    }

    public function resolvePath(ClientDocument $document): string
    {
        $path = $this->getAccountDirectory($document->getAccountAid()) . DIRECTORY_SEPARATOR . $document->getStoredName();
        if (!is_file($path)) {
            throw new RuntimeException('Document file is unavailable.');
        }

        return $path;
    }

    private function assertValid(UploadedFile $file): void
    {
        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed. Please try again.');
        }

        $size = $file->getSize();
        if ($size !== null && $size > self::MAX_FILE_BYTES) {
            throw new RuntimeException('File is too large. Maximum size is 15 MB.');
        }

        $mimeType = $file->getMimeType() ?? $file->getClientMimeType();
        if ($mimeType === null || !$this->isAllowedMimeType($mimeType)) {
            throw new RuntimeException('Unsupported file type. Upload a PDF, image, or office document.');
        }
    }

    private function isAllowedMimeType(string $mimeType): bool
    {
        foreach (self::ALLOWED_MIME_TYPES as $allowed) {
            if (strcasecmp($allowed, $mimeType) === 0) {
                return true;
            }
        }

        return false;
    }

    private function getAccountDirectory(int $accountAid): string
    {
        $directory = $this->storageDir . DIRECTORY_SEPARATOR . $accountAid;
        if (!is_dir($directory)) {
            if (!@mkdir($directory, 0770, true) && !is_dir($directory)) {
                throw new RuntimeException('Unable to initialise secure document storage.');
            }
        }

        return $directory;
    }

    private function generateStoredName(UploadedFile $file): string
    {
        $random = bin2hex(random_bytes(24));
        $extension = $file->guessExtension();
        if (!$extension) {
            $original = $file->getClientOriginalName();
            if ($original) {
                $extension = pathinfo($original, PATHINFO_EXTENSION);
            }
        }

        $extension = $extension ? strtolower(preg_replace('/[^a-z0-9]+/i', '', $extension)) : '';

        return $extension !== '' ? $random . '.' . $extension : $random;
    }

    private function sanitizeOriginalName(?string $originalName): string
    {
        $name = trim((string) $originalName);
        if ($name === '') {
            return 'document';
        }

        $name = preg_replace('/[\x00-\x1f\x7f]/u', '', $name) ?? 'document';
        if (mb_strlen($name) > 200) {
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $base = mb_substr($name, 0, 200 - (mb_strlen($extension) + ($extension ? 1 : 0)));
            $name = $extension ? $base . '.' . $extension : $base;
        }

        return $name;
    }
}
