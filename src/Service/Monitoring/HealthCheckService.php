<?php

namespace App\Service\Monitoring;

use DateTimeImmutable;
use Doctrine\DBAL\Exception as DBALException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class HealthCheckService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        #[Autowire('%kernel.project_dir%')]
        private readonly string $projectDir,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function collect(): array
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'storage' => $this->checkClientStorage(),
            'logs' => $this->checkLogDirectory(),
        ];

        $status = 'ok';
        foreach ($checks as $result) {
            if ($result['status'] === 'critical') {
                $status = 'critical';
                break;
            }
            if ($result['status'] === 'degraded' && $status !== 'critical') {
                $status = 'degraded';
            }
        }

        return [
            'status' => $status,
            'checks' => $checks,
            'timestamp' => (new DateTimeImmutable())->format(DATE_ATOM),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkDatabase(): array
    {
        $connection = $this->entityManager->getConnection();

        try {
            $start = microtime(true);
            if (!$connection->isConnected()) {
                $connection->connect();
            }
            $connection->executeQuery($connection->getDatabasePlatform()->getDummySelectSQL())->free();
            $duration = (microtime(true) - $start) * 1000;

            return [
                'status' => 'ok',
                'latency_ms' => round($duration, 2),
            ];
        } catch (DBALException $exception) {
            return [
                'status' => 'critical',
                'message' => $exception->getMessage(),
            ];
        } catch (\Throwable $exception) {
            return [
                'status' => 'critical',
                'message' => $exception->getMessage(),
            ];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function checkClientStorage(): array
    {
        $storagePath = $this->projectDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'client-documents';

        if (!is_dir($storagePath)) {
            return [
                'status' => 'degraded',
                'message' => 'Client storage directory missing.',
            ];
        }

        if (!is_writable($storagePath)) {
            return [
                'status' => 'degraded',
                'message' => 'Client storage directory is not writable.',
            ];
        }

        $freeBytes = @disk_free_space($storagePath);
        if ($freeBytes === false) {
            return [
                'status' => 'degraded',
                'message' => 'Unable to determine free space for client storage.',
            ];
        }

        return [
            'status' => 'ok',
            'free_space_bytes' => $freeBytes,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function checkLogDirectory(): array
    {
        $logPath = $this->projectDir . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'log';

        if (!is_dir($logPath)) {
            return [
                'status' => 'degraded',
                'message' => 'Log directory missing.',
            ];
        }

        if (!is_writable($logPath)) {
            return [
                'status' => 'degraded',
                'message' => 'Log directory is not writable.',
            ];
        }

        return [
            'status' => 'ok',
        ];
    }
}
