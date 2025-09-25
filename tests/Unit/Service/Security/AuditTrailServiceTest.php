<?php

namespace App\Tests\Unit\Service\Security;

use App\Entity\AuditLogEntry;
use App\Service\Security\AuditTrailService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AuditTrailServiceTest extends TestCase
{
    public function testRecordPersistsEntryWithRequestContext(): void
    {
        $capturedEntry = null;
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects($this->once())
            ->method('persist')
            ->willReturnCallback(function (AuditLogEntry $entry) use (&$capturedEntry): void {
                $capturedEntry = $entry;
            });
        $entityManager->expects($this->once())
            ->method('flush');

        $request = new Request();
        $request->server->set('REMOTE_ADDR', '203.0.113.5');
        $request->headers->set('User-Agent', 'PHPUnit/Telemetry');

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('info')
            ->with(
                'audit.event',
                $this->callback(static function (array $context): bool {
                    return $context['event'] === 'test.event'
                        && $context['actor_id'] === 'tester'
                        && $context['actor_type'] === 'system'
                        && $context['account_aid'] === 42
                        && $context['subject_type'] === 'resource'
                        && $context['subject_id'] === '99'
                        && $context['metadata'] === ['key' => 'value']
                        && $context['ip'] === '203.0.113.5'
                        && $context['user_agent'] === 'PHPUnit/Telemetry';
                })
            );

        $service = new AuditTrailService($entityManager, $requestStack, $logger);
        $service->record(
            eventType: 'test.event',
            accountAid: 42,
            actorId: 'tester',
            metadata: ['key' => 'value'],
            subjectType: 'resource',
            subjectId: '99',
            actorType: 'system'
        );

        self::assertInstanceOf(AuditLogEntry::class, $capturedEntry);
        self::assertSame('test.event', $capturedEntry->getEventType());
        self::assertSame(42, $capturedEntry->getAccountAid());
        self::assertSame('tester', $capturedEntry->getActorId());
        self::assertSame('system', $capturedEntry->getActorType());
        self::assertSame('resource', $capturedEntry->getSubjectType());
        self::assertSame('99', $capturedEntry->getSubjectId());
        self::assertSame(['key' => 'value'], $capturedEntry->getMetadata());
        self::assertSame('203.0.113.5', $capturedEntry->getIpAddress());
        self::assertSame('PHPUnit/Telemetry', $capturedEntry->getUserAgent());
        self::assertNotNull($capturedEntry->getOccurredAt());
    }
}
