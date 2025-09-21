<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use App\Service\FileStorage;
use App\Service\ReportParser;

class ParserController extends AbstractController
{
    public function __init(Request $request, FileStorage $storage, ReportParser $parser, ManagerRegistry $doctrine)
    {
        if (!$request->query->has('file')) {
            throw new NotFoundHttpException('File not found!');
        }

        $fileName = urldecode($request->query->get('file'));
        try {
            $absolutePath = $storage->resolvePath($fileName);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $account_details = $this->get_account_details($doctrine, $fileName);
        if (!$account_details) {
            throw new NotFoundHttpException('Account details not found for file.');
        }

        $parsed = $parser->loadReportData($absolutePath);
        $client_data = $parsed['client_data'];
        $derogatory_accounts = $parsed['derogatory_accounts'];
        $inquiry_accounts = $parsed['inquiry_accounts'];
        $public_records = $parsed['public_records'];
        $credit_info = $parsed['credit_info'];

        return $this->render('credit-report.html.twig', [
            "first_name"    =>  $account_details['first_name'],
            "last_name"     =>  $account_details['last_name'],
            'name' => $client_data->trans_union['name'],
            'report_date' => $client_data->trans_union['report_data'],
            "transunion_credit_score" => $client_data->trans_union['credit_score'],
            "equifax_credit_score" => $client_data->equifax['credit_score'],
            "experian_credit_score" => $client_data->experian['credit_score'],
            "equifax_delinquent" => $client_data->equifax['delinquent'],
            "experian_delinquent" => $client_data->experian['delinquent'],
            "transunion_delinquent" => $client_data->trans_union['delinquent'],
            "equifax_derogatory" =>  $client_data->equifax['derogatory'],
            "experian_derogatory" =>  $client_data->experian['derogatory'],
            "transunion_derogatory" =>  $client_data->trans_union['derogatory'],
            "equifax_collection" =>  $client_data->equifax['collection'],
            "experian_collection" =>  $client_data->experian['collection'],
            "transunion_collection" =>  $client_data->trans_union['collection'],
            "equifax_public_records" =>  $client_data->equifax['public_records'],
            "experian_public_records" =>  $client_data->experian['public_records'],
            "transunion_public_records" =>  $client_data->trans_union['public_records'],
            "equifax_inquiries" =>  $client_data->equifax['inquiries'],
            "experian_inquiries" =>  $client_data->experian['inquiries'],
            "transunion_inquiries" =>  $client_data->trans_union['inquiries'],
            "derogatory_accounts" => $derogatory_accounts['accounts'],
            "derogatory_accounts_total" =>  $derogatory_accounts['total'],
            "inquiry_accounts"  =>  $inquiry_accounts["accounts"],
            "inquiry_total" =>  $inquiry_accounts["total"],
            "equifax_open_accounts" =>  $client_data->equifax['open_accounts'],
            "transunion_open_accounts" =>  $client_data->trans_union['open_accounts'],
            "experian_open_accounts" =>  $client_data->experian['open_accounts'],
            "equifax_total_accounts" =>  $client_data->equifax['total_accounts'],
            "transunion_total_accounts" =>  $client_data->trans_union['total_accounts'],
            "experian_total_accounts" =>  $client_data->experian['total_accounts'],
            "equifax_closed_accounts" =>  $client_data->equifax['closed_accounts'],
            "transunion_closed_accounts" =>  $client_data->trans_union['closed_accounts'],
            "experian_closed_accounts" =>  $client_data->experian['closed_accounts'],
            "equifax_balances" =>  $client_data->equifax['balances'],
            "transunion_balances" =>  $client_data->trans_union['balances'],
            "experian_balances" =>  $client_data->experian['balances'],
            "equifax_payments" =>  $client_data->equifax['payments'],
            "transunion_payments" =>  $client_data->trans_union['payments'],
            "experian_payments" =>  $client_data->experian['payments'],
            "public_records"    =>  $public_records['records'],
            "public_records_total"  =>  $public_records['total'],
            "credit_info"   => $credit_info,
        ]);
    }

    public function __init_raw(Request $request, FileStorage $storage, ReportParser $parser)
    {

        $fileParam = $request->query->get('file');
        if (!$fileParam) {
            throw new NotFoundHttpException('File not specified');
        }

        try {
            $absolutePath = $storage->resolvePath($fileParam);
        } catch (\RuntimeException $e) {
            throw new NotFoundHttpException('File not found!');
        }

        $parsed = $parser->loadReportData($absolutePath);

        $response = new Response();
        $response->setContent(json_encode($parsed));
        $response->headers->set('Content-Type', 'application/json');

        return $response;

    }

    private function get_account_details(ManagerRegistry $doctrine, $file = null)
    {
        if ($file) {
            $em = $doctrine->getManager();
            $conn = $em->getConnection();
            $query = "SELECT first_name, last_name FROM accounts a INNER JOIN account_files f ON f.aid = a.aid WHERE f.filename = :filename";
            $statement = $conn->prepare($query);
            $rows = $statement->executeQuery(['filename' => $file])->fetchAllAssociative();
            return $rows[0] ?? null;
        }

        return null;
    }
}

?>
