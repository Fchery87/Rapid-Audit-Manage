<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Service\FileStorage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ParserControllerTest extends WebTestCase
{
    private const FIXTURE = __DIR__ . '/../Fixtures/reports/identityiq-sample.html';

    public function testRawParserEndpointParsesUploadedFixture(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var FileStorage $storage */
        $storage = $container->get(FileStorage::class);
        $uploadDir = $storage->getUploadDir();
        $filename = str_repeat('b', 64) . '.html';
        $path = $uploadDir . DIRECTORY_SEPARATOR . $filename;

        if (!copy(self::FIXTURE, $path)) {
            self::fail('Failed to copy parser fixture into upload directory.');
        }

        try {
            $user = (new User())
                ->setUsername('analyst@example.com')
                ->setEmail('analyst@example.com')
                ->setRoles(['ROLE_ANALYST']);

            $client->loginUser($user);

            $client->request('GET', '/parse-html-raw', ['file' => $filename]);

            self::assertResponseIsSuccessful();
            self::assertSame('application/json', $client->getResponse()->headers->get('content-type'));

            $data = json_decode((string) $client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

            self::assertSame('2.0.0', $data['meta']['parser_version']);
            self::assertSame($filename, $data['meta']['source_name']);
            self::assertSame(250, $data['meta']['sections'][0]['limit']);
            self::assertArrayHasKey('client_data', $data);
            self::assertArrayHasKey('derogatory_accounts', $data);
            self::assertArrayHasKey('inquiry_accounts', $data);
            self::assertArrayHasKey('public_records', $data);
        } finally {
            @unlink($path);
        }
    }
}
