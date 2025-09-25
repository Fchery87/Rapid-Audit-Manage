<?php

namespace App\Controller;

use App\Security\SensitiveDataProtector;
use App\Service\FileStorage;
use App\Service\ReportParser;
use App\Service\Security\AuditTrailService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserInterface;

#[IsGranted('ROLE_ANALYST')]
class ParserController extends AbstractController
{
    public function __construct(
        private readonly FileStorage $storage,
        private readonly ReportParser $parser,
        private readonly EntityManagerInterface $entityManager,
        private readonly SensitiveDataProtector $protector,
        private readonly AuditTrailService $auditTrail,
    ) {
    }

    #[Route('/html-report', name: 'app_parse_html', methods: ['GET'])]
    public function showReport(Request $request): Response
    {
        $fileName = $request->query->get('file');
        if (!$fileName) {
            throw new NotFoundHttpException('File not specified');
        }

        try {
            $absolutePath = $this->storage->resolvePath($fileName);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $account = $this->getAccountDetails($fileName);
        if (!$account) {
            throw new NotFoundHttpException('Account details not found for file.');
        }

        $parsedReport = $this->parser->parse($absolutePath);
        $payload = $parsedReport->toArray();
        $clientData = $payload['client_data'];
        $derogatoryAccounts = $payload['derogatory_accounts'];
        $inquiryAccounts = $payload['inquiry_accounts'];
        $publicRecords = $payload['public_records'];
        $creditInfo = $payload['credit_info'];

        $response = $this->render('credit-report.html.twig', [
            'first_name' => $account['first_name'],
            'last_name' => $account['last_name'],
            'name' => $clientData['trans_union']['name'] ?? null,
            'report_date' => $clientData['trans_union']['report_data'] ?? null,
            'transunion_credit_score' => $clientData['trans_union']['credit_score'] ?? null,
            'equifax_credit_score' => $clientData['equifax']['credit_score'] ?? null,
            'experian_credit_score' => $clientData['experian']['credit_score'] ?? null,
            'equifax_delinquent' => $clientData['equifax']['delinquent'] ?? null,
            'experian_delinquent' => $clientData['experian']['delinquent'] ?? null,
            'transunion_delinquent' => $clientData['trans_union']['delinquent'] ?? null,
            'equifax_derogatory' => $clientData['equifax']['derogatory'] ?? null,
            'experian_derogatory' => $clientData['experian']['derogatory'] ?? null,
            'transunion_derogatory' => $clientData['trans_union']['derogatory'] ?? null,
            'equifax_collection' => $clientData['equifax']['collection'] ?? null,
            'experian_collection' => $clientData['experian']['collection'] ?? null,
            'transunion_collection' => $clientData['trans_union']['collection'] ?? null,
            'equifax_public_records' => $clientData['equifax']['public_records'] ?? null,
            'experian_public_records' => $clientData['experian']['public_records'] ?? null,
            'transunion_public_records' => $clientData['trans_union']['public_records'] ?? null,
            'equifax_inquiries' => $clientData['equifax']['inquiries'] ?? null,
            'experian_inquiries' => $clientData['experian']['inquiries'] ?? null,
            'transunion_inquiries' => $clientData['trans_union']['inquiries'] ?? null,
            'derogatory_accounts' => $derogatoryAccounts['accounts'],
            'derogatory_accounts_total' => $derogatoryAccounts['total'],
            'inquiry_accounts' => $inquiryAccounts['accounts'],
            'inquiry_total' => $inquiryAccounts['total'],
            'equifax_open_accounts' => $clientData['equifax']['open_accounts'] ?? null,
            'transunion_open_accounts' => $clientData['trans_union']['open_accounts'] ?? null,
            'experian_open_accounts' => $clientData['experian']['open_accounts'] ?? null,
            'equifax_total_accounts' => $clientData['equifax']['total_accounts'] ?? null,
            'transunion_total_accounts' => $clientData['trans_union']['total_accounts'] ?? null,
            'experian_total_accounts' => $clientData['experian']['total_accounts'] ?? null,
            'equifax_closed_accounts' => $clientData['equifax']['closed_accounts'] ?? null,
            'transunion_closed_accounts' => $clientData['trans_union']['closed_accounts'] ?? null,
            'experian_closed_accounts' => $clientData['experian']['closed_accounts'] ?? null,
            'equifax_balances' => $clientData['equifax']['balances'] ?? null,
            'transunion_balances' => $clientData['trans_union']['balances'] ?? null,
            'experian_balances' => $clientData['experian']['balances'] ?? null,
            'equifax_payments' => $clientData['equifax']['payments'] ?? null,
            'transunion_payments' => $clientData['trans_union']['payments'] ?? null,
            'experian_payments' => $clientData['experian']['payments'] ?? null,
            'public_records' => $publicRecords['records'],
            'public_records_total' => $publicRecords['total'],
            'credit_info' => $creditInfo,
            'meta' => $payload['meta'],
        ]);

        $this->auditTrail->recordAnalystReportViewed(
            $this->resolveActorId(),
            isset($account['aid']) ? (int) $account['aid'] : null,
            $fileName,
            ['format' => 'html', 'route' => 'app_parse_html']
        );

        return $response;
    }

    #[Route('/parse-html-raw', name: 'app_parse_html_raw', methods: ['GET'])]
    public function showRawReport(Request $request): JsonResponse
    {
        $fileName = $request->query->get('file');
        if (!$fileName) {
            throw new NotFoundHttpException('File not specified');
        }

        try {
            $absolutePath = $this->storage->resolvePath($fileName);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $parsedReport = $this->parser->parse($absolutePath);

        $response = $this->json($parsedReport->toArray());

        $this->auditTrail->recordAnalystReportViewed(
            $this->resolveActorId(),
            $this->resolveAccountAid($fileName),
            $fileName,
            ['format' => 'json', 'route' => 'app_parse_html_raw']
        );

        return $response;
    }

    private function connection(): Connection
    {
        return $this->entityManager->getConnection();
    }

    private function getAccountDetails(string $fileName): ?array
    {
        $sql = 'SELECT a.aid, a.first_name, a.last_name FROM accounts a INNER JOIN account_files f ON f.aid = a.aid WHERE f.filename = :filename';

        $result = $this->connection()->executeQuery($sql, ['filename' => $fileName]);

        $account = $result->fetchAssociative() ?: null;

        if (!$account) {
            return null;
        }

        $aid = isset($account['aid']) ? (int) $account['aid'] : null;
        $account = $this->protector->decryptAccountRecord($account);

        if ($aid !== null) {
            $account['aid'] = $aid;
        }

        return $account;
    }

    private function resolveAccountAid(string $fileName): ?int
    {
        $sql = 'SELECT aid FROM account_files WHERE filename = :filename';
        $result = $this->connection()->executeQuery($sql, ['filename' => $fileName]);
        $aid = $result->fetchOne();

        return $aid !== false ? (int) $aid : null;
    }

    private function resolveActorId(): string
    {
        $user = $this->getUser();

        if ($user instanceof UserInterface) {
            return $user->getUserIdentifier();
        }

        return 'anonymous';
    }
}

