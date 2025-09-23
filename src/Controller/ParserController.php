<?php

namespace App\Controller;

use App\Security\SensitiveDataProtector;
use App\Service\FileStorage;
use App\Service\ReportParser;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ANALYST')]
class ParserController extends AbstractController
{
    public function __construct(
        private readonly FileStorage $storage,
        private readonly ReportParser $parser,
        private readonly EntityManagerInterface $entityManager,
        private readonly SensitiveDataProtector $protector,
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

        $parsed = $this->parser->loadReportData($absolutePath);
        $clientData = $parsed['client_data'];
        $derogatoryAccounts = $parsed['derogatory_accounts'];
        $inquiryAccounts = $parsed['inquiry_accounts'];
        $publicRecords = $parsed['public_records'];
        $creditInfo = $parsed['credit_info'];

        return $this->render('credit-report.html.twig', [
            'first_name' => $account['first_name'],
            'last_name' => $account['last_name'],
            'name' => $clientData->trans_union['name'],
            'report_date' => $clientData->trans_union['report_data'],
            'transunion_credit_score' => $clientData->trans_union['credit_score'],
            'equifax_credit_score' => $clientData->equifax['credit_score'],
            'experian_credit_score' => $clientData->experian['credit_score'],
            'equifax_delinquent' => $clientData->equifax['delinquent'],
            'experian_delinquent' => $clientData->experian['delinquent'],
            'transunion_delinquent' => $clientData->trans_union['delinquent'],
            'equifax_derogatory' => $clientData->equifax['derogatory'],
            'experian_derogatory' => $clientData->experian['derogatory'],
            'transunion_derogatory' => $clientData->trans_union['derogatory'],
            'equifax_collection' => $clientData->equifax['collection'],
            'experian_collection' => $clientData->experian['collection'],
            'transunion_collection' => $clientData->trans_union['collection'],
            'equifax_public_records' => $clientData->equifax['public_records'],
            'experian_public_records' => $clientData->experian['public_records'],
            'transunion_public_records' => $clientData->trans_union['public_records'],
            'equifax_inquiries' => $clientData->equifax['inquiries'],
            'experian_inquiries' => $clientData->experian['inquiries'],
            'transunion_inquiries' => $clientData->trans_union['inquiries'],
            'derogatory_accounts' => $derogatoryAccounts['accounts'],
            'derogatory_accounts_total' => $derogatoryAccounts['total'],
            'inquiry_accounts' => $inquiryAccounts['accounts'],
            'inquiry_total' => $inquiryAccounts['total'],
            'equifax_open_accounts' => $clientData->equifax['open_accounts'],
            'transunion_open_accounts' => $clientData->trans_union['open_accounts'],
            'experian_open_accounts' => $clientData->experian['open_accounts'],
            'equifax_total_accounts' => $clientData->equifax['total_accounts'],
            'transunion_total_accounts' => $clientData->trans_union['total_accounts'],
            'experian_total_accounts' => $clientData->experian['total_accounts'],
            'equifax_closed_accounts' => $clientData->equifax['closed_accounts'],
            'transunion_closed_accounts' => $clientData->trans_union['closed_accounts'],
            'experian_closed_accounts' => $clientData->experian['closed_accounts'],
            'equifax_balances' => $clientData->equifax['balances'],
            'transunion_balances' => $clientData->trans_union['balances'],
            'experian_balances' => $clientData->experian['balances'],
            'equifax_payments' => $clientData->equifax['payments'],
            'transunion_payments' => $clientData->trans_union['payments'],
            'experian_payments' => $clientData->experian['payments'],
            'public_records' => $publicRecords['records'],
            'public_records_total' => $publicRecords['total'],
            'credit_info' => $creditInfo,
        ]);
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

        $parsed = $this->parser->loadReportData($absolutePath);

        return $this->json($parsed);
    }

    private function connection(): Connection
    {
        return $this->entityManager->getConnection();
    }

    private function getAccountDetails(string $fileName): ?array
    {
        $sql = 'SELECT first_name, last_name FROM accounts a INNER JOIN account_files f ON f.aid = a.aid WHERE f.filename = :filename';

        $result = $this->connection()->executeQuery($sql, ['filename' => $fileName]);

        $account = $result->fetchAssociative() ?: null;

        return $account ? $this->protector->decryptAccountRecord($account) : null;
    }
}

