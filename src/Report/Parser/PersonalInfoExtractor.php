<?php

namespace App\Report\Parser;

use App\Report\Dto\BureauProfile;
use App\Report\Dto\ClientProfile;
use DOMDocument;

final class PersonalInfoExtractor
{
    private const TRANS_UNION_MAP = [
        'report_data' => 7,
        'name' => 11,
        'also_known_as' => 15,
        'former_name' => 19,
        'date_of_birth' => 23,
        'current_address' => 27,
        'previous_address' => 31,
        'employers' => 35,
        'credit_score' => 39,
        'lending_rank' => 43,
        'score_scale' => 47,
        'total_accounts' => 58,
        'open_accounts' => 62,
        'closed_accounts' => 66,
        'delinquent' => 70,
        'derogatory' => 74,
        'collection' => 78,
        'balances' => 82,
        'payments' => 86,
        'public_records' => 90,
        'inquiries' => 94,
    ];

    private const EXPERIAN_MAP = [
        'report_data' => 8,
        'name' => 12,
        'also_known_as' => 16,
        'former_name' => 20,
        'date_of_birth' => 24,
        'current_address' => 28,
        'previous_address' => 32,
        'employers' => 36,
        'credit_score' => 40,
        'lending_rank' => 45,
        'score_scale' => 48,
        'total_accounts' => 59,
        'open_accounts' => 63,
        'closed_accounts' => 67,
        'delinquent' => 71,
        'derogatory' => 75,
        'collection' => 79,
        'balances' => 83,
        'payments' => 87,
        'public_records' => 91,
        'inquiries' => 95,
    ];

    private const EQUIFAX_MAP = [
        'report_data' => 9,
        'name' => 13,
        'also_known_as' => 16,
        'former_name' => 21,
        'date_of_birth' => 25,
        'current_address' => 29,
        'previous_address' => 33,
        'employers' => 37,
        'credit_score' => 41,
        'lending_rank' => 45,
        'score_scale' => 49,
        'total_accounts' => 60,
        'open_accounts' => 64,
        'closed_accounts' => 68,
        'delinquent' => 72,
        'derogatory' => 76,
        'collection' => 80,
        'balances' => 84,
        'payments' => 88,
        'public_records' => 92,
        'inquiries' => 96,
    ];

    public function __construct(private readonly ValueSanitizer $sanitizer)
    {
    }

    public function extract(DOMDocument $document): ClientProfile
    {
        $values = [];

        foreach ($document->getElementsByTagName('table') as $table) {
            $class = $table->getAttribute('class');
            if ($class === null || $class === '' || !str_contains($class, 'rpt_content_table')) {
                continue;
            }

            foreach ($table->getElementsByTagName('td') as $cell) {
                $values[] = $this->sanitizer->collapseWhitespace($cell->nodeValue);
            }
        }

        return new ClientProfile(
            $this->createProfile(self::TRANS_UNION_MAP, $values),
            $this->createProfile(self::EXPERIAN_MAP, $values),
            $this->createProfile(self::EQUIFAX_MAP, $values)
        );
    }

    /**
     * @param array<string, int> $map
     * @param array<int, string|null> $values
     */
    private function createProfile(array $map, array $values): BureauProfile
    {
        $data = [];
        foreach ($map as $key => $index) {
            $data[$key] = $values[$index] ?? null;
        }

        return new BureauProfile(
            $data['report_data'] ?? null,
            $data['name'] ?? null,
            $data['also_known_as'] ?? null,
            $data['former_name'] ?? null,
            $data['date_of_birth'] ?? null,
            $data['current_address'] ?? null,
            $data['previous_address'] ?? null,
            $data['employers'] ?? null,
            $data['credit_score'] ?? null,
            $data['lending_rank'] ?? null,
            $data['score_scale'] ?? null,
            $data['total_accounts'] ?? null,
            $data['open_accounts'] ?? null,
            $data['closed_accounts'] ?? null,
            $data['delinquent'] ?? null,
            $data['derogatory'] ?? null,
            $data['collection'] ?? null,
            $data['balances'] ?? null,
            $data['payments'] ?? null,
            $data['public_records'] ?? null,
            $data['inquiries'] ?? null
        );
    }
}
