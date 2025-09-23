<?php

namespace App\Controller;

use App\Service\Monitoring\HealthCheckService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MonitoringController extends AbstractController
{
    public function __construct(
        private readonly HealthCheckService $healthCheckService,
        #[Autowire(service: 'monolog.logger.monitoring')]
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/healthz', name: 'app_healthz', methods: ['GET'])]
    public function healthz(): JsonResponse
    {
        $payload = $this->healthCheckService->collect();
        $statusCode = $payload['status'] === 'ok' ? JsonResponse::HTTP_OK : JsonResponse::HTTP_SERVICE_UNAVAILABLE;

        if ($payload['status'] !== 'ok') {
            $this->logger->warning('Health check degraded.', ['payload' => $payload]);
        }

        return new JsonResponse($payload, $statusCode);
    }
}
