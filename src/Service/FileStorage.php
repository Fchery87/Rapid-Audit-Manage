<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class FileStorage
{
    private $projectDir;

    public function __construct(KernelInterface $kernel)
    {
        $this->projectDir = $kernel->getProjectDir();
    }

    public function getUploadDir(): string
    {
        $dir = $this->projectDir . '/var/uploads';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir;
    }

    public function storeUploadedHtml(UploadedFile $file): string
    {
        // Basic validation
        $allowedMime = [
            'text/html',
            'text/plain',
            'application/xhtml+xml',
        ];
        $mime = $file->getClientMimeType();
        if (!in_array($mime, $allowedMime, true)) {
            throw new \RuntimeException('Invalid file type. Please upload an HTML file.');
        }

        // Limit size to 5MB
        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \RuntimeException('File too large. Maximum size is 5MB.');
        }

        // Force .html extension for storage
        $storedName = hash('sha256', uniqid('', true)) . '.html';
        $targetPath = $this->getUploadDir() . DIRECTORY_SEPARATOR . $storedName;

        $file->move($this->getUploadDir(), $storedName);

        return $storedName;
    }

    public function resolvePath(string $storedName): string
    {
        // Prevent path traversal by allowing only our generated pattern
        if (!preg_match('/^[a-f0-9]{64}\.html$/', $storedName)) {
            throw new \RuntimeException('Invalid filename.');
        }
        return $this->getUploadDir() . DIRECTORY_SEPARATOR . $storedName;
    }

    /**
     * Returns a list of stored .html files (filenames only).
     *
     * @return string[]
     */
    public function listHtmlFilenames(): array
    {
        $dir = $this->getUploadDir();
        if (!is_dir($dir)) {
            return [];
        }
        $files = array_values(array_filter(scandir($dir) ?: [], function ($f) use ($dir) {
            return is_file($dir . DIRECTORY_SEPARATOR . $f) && preg_match('/\.html$/i', $f);
        }));

        sort($files);
        return $files;
    }
}

?>