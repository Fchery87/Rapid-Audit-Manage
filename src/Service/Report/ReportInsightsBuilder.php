<?php

namespace App\Service\Report;

use App\Entity\CreditReport;
use DateInterval;
use DateTimeImmutable;

class ReportInsightsBuilder
{
    /**
     * @param CreditReport[] $history
     * @param array<string, mixed> $current
     * @return array<string, mixed>
     */
    public function buildProgressTimeline(array $history, array $current): array
    {
        $points = [];

        if ($history !== []) {
            usort($history, static fn(CreditReport $a, CreditReport $b) => $a->getParsedAt() <=> $b->getParsedAt());
        }

        foreach ($history as $report) {
            $points[] = $this->mapPoint(
                $report->getParsedAt(),
                $report->getClientData(),
                $report->getDerogatoryAccounts(),
                $report->getInquiryAccounts(),
                $report->getCreditInfo()
            );
        }

        if ($points === [] && isset($current['meta']['parsed_at'])) {
            $parsedAt = DateTimeImmutable::createFromFormat(DateTimeImmutable::ATOM, (string) $current['meta']['parsed_at']);
            if (!$parsedAt instanceof DateTimeImmutable) {
                $parsedAt = new DateTimeImmutable('now');
            }

            $points[] = $this->mapPoint(
                $parsedAt,
                $current['clientData'] ?? [],
                $current['derogatory'] ?? [],
                $current['inquiries'] ?? [],
                $current['creditInfo'] ?? []
            );
        }

        $scoreChange = $this->calculateScoreChange($points);
        $derogChange = $this->calculateDelta($points, 'derogatory_total', false);
        $inquiryChange = $this->calculateDelta($points, 'inquiries_total', false);
        $utilizationChange = $this->calculateDelta($points, 'utilization', true);

        return [
            'points' => $points,
            'score_change' => $scoreChange,
            'derogatory_change' => $derogChange,
            'inquiry_change' => $inquiryChange,
            'utilization_change' => $utilizationChange,
            'trend' => $this->describeTrend($scoreChange),
        ];
    }

    /**
     * @param array<string, mixed> $clientData
     * @param array<string, mixed> $creditInfo
     * @param array<string, mixed> $derogatory
     * @return array<string, mixed>
     */
    public function buildBureauComparison(array $clientData, array $creditInfo, array $derogatory): array
    {
        $bureaus = [];
        $scoreValues = [];
        $utilizationValues = [];
        $labels = [
            'trans_union' => 'TransUnion',
            'experian' => 'Experian',
            'equifax' => 'Equifax',
        ];

        foreach ($labels as $key => $label) {
            $profile = $clientData[$key] ?? [];
            $score = $this->toInt($profile['credit_score'] ?? null);
            $inquiries = $this->toInt($profile['inquiries'] ?? null);
            $derogCount = $this->countDerogatoryForBureau($derogatory['accounts'] ?? [], $key);
            $utilization = $this->toFloat($creditInfo[$key . '_percent'] ?? null);

            if ($score !== null) {
                $scoreValues[$key] = $score;
            }

            if ($utilization !== null) {
                $utilizationValues[$key] = $utilization;
            }

            $bureaus[] = [
                'key' => $key,
                'label' => $label,
                'score' => $score,
                'inquiries' => $inquiries,
                'derogatory' => $derogCount,
                'utilization' => $utilization,
            ];
        }

        $leader = null;
        $laggard = null;
        $scoreGap = null;

        if ($scoreValues !== []) {
            $leaderKey = array_keys($scoreValues, max($scoreValues))[0];
            $laggardKey = array_keys($scoreValues, min($scoreValues))[0];
            $leader = $labels[$leaderKey] ?? $leaderKey;
            $laggard = $labels[$laggardKey] ?? $laggardKey;
            $scoreGap = $scoreValues[$leaderKey] - $scoreValues[$laggardKey];
        }

        $utilizationGap = null;
        if ($utilizationValues !== []) {
            $utilizationGap = max($utilizationValues) - min($utilizationValues);
        }

        $insights = [];
        if ($scoreGap !== null && $scoreGap >= 15 && $leader && $laggard) {
            $insights[] = sprintf('%s is trailing %s by %d points. Align reporting to close the gap.', $laggard, $leader, $scoreGap);
        }

        if ($utilizationGap !== null && $utilizationGap >= 5.0) {
            $insights[] = sprintf('Utilization differs by %.1f%% across bureaus. Verify credit line updates are syncing.', $utilizationGap);
        }

        if ($insights === []) {
            $insights[] = 'Bureau data is largely aligned. Maintain consistent updates across all bureaus.';
        }

        return [
            'bureaus' => $bureaus,
            'leader' => $leader,
            'laggard' => $laggard,
            'score_gap' => $scoreGap,
            'utilization_gap' => $utilizationGap,
            'insights' => $insights,
        ];
    }

    /**
     * @param array<string, mixed> $reportData
     * @param array<string, mixed> $progressTimeline
     * @param array<string, mixed>|null $disputeWorkflow
     * @return array<string, mixed>
     */
    public function buildRecommendationScores(array $reportData, array $progressTimeline, ?array $disputeWorkflow): array
    {
        $now = new DateTimeImmutable('now');

        $creditInfo = $reportData['creditInfo'] ?? [];
        $derogatory = $reportData['derogatory'] ?? [];
        $inquiries = $reportData['inquiries'] ?? [];
        $clientData = $reportData['clientData'] ?? [];

        $recommendations = [
            $this->buildUtilizationRecommendation($creditInfo, $now),
            $this->buildDerogatoryRecommendation($derogatory, $disputeWorkflow, $now),
            $this->buildInquiryRecommendation($inquiries, $now),
        ];

        $averageScore = $this->averageScores($clientData);
        if ($averageScore !== null && $averageScore < 700) {
            $recommendations[] = $this->buildPositiveCreditRecommendation($averageScore, $now);
        }

        $averageChange = $progressTimeline['score_change']['average'] ?? null;
        if (is_numeric($averageChange) && $averageChange < 0) {
            $recommendations[] = $this->buildTrendRecommendation((float) $averageChange, $progressTimeline, $now);
        }

        $recommendations = array_values(array_filter($recommendations));
        usort($recommendations, static fn(array $a, array $b) => $b['score'] <=> $a['score']);

        $overall = null;
        if ($recommendations !== []) {
            $overall = $this->average(array_map(static fn(array $rec) => (float) $rec['score'], $recommendations));
        }

        return [
            'items' => $recommendations,
            'overall_score' => $overall,
            'summary' => $this->summarizeRecommendations($recommendations, $overall),
        ];
    }

    /**
     * @param array<string, mixed> $recommendations
     * @param array<string, mixed>|null $disputeWorkflow
     * @param array<string, mixed> $progressTimeline
     * @return array<string, mixed>
     */
    public function buildActionPlan(array $recommendations, ?array $disputeWorkflow, array $progressTimeline): array
    {
        $milestones = [];
        $priority = 1;

        foreach ($recommendations['items'] ?? [] as $item) {
            $milestones[] = [
                'priority' => sprintf('P%d', $priority++),
                'title' => $item['title'],
                'owner' => $item['owner'],
                'score' => $item['score'],
                'impact' => $item['impact'],
                'urgency' => $item['urgency'],
                'due' => $item['target_date'],
                'details' => implode(" \u2022 ", $item['next_steps']),
            ];
        }

        $openTasks = [];
        if (isset($disputeWorkflow['tasks']) && is_iterable($disputeWorkflow['tasks'])) {
            foreach ($disputeWorkflow['tasks'] as $task) {
                if (($task['status'] ?? '') === 'done') {
                    continue;
                }

                $dueAt = $task['due_at'] ?? null;
                if ($dueAt instanceof DateTimeImmutable) {
                    $due = $dueAt->format('Y-m-d');
                } elseif ($dueAt instanceof \DateTimeInterface) {
                    $due = $dueAt->format('Y-m-d');
                } elseif (is_string($dueAt) && $dueAt !== '') {
                    $due = $dueAt;
                } else {
                    $due = '—';
                }

                $owner = ($task['client_visible'] ?? false) ? 'Client' : 'Analyst';
                if (($task['assigned_to'] ?? '') !== '') {
                    $owner = $task['assigned_to'];
                }

                $openTasks[] = [
                    'title' => $task['description'] ?? 'Dispute task',
                    'owner' => $owner,
                    'due' => $due,
                    'status' => ucfirst((string) ($task['status'] ?? 'open')),
                ];
            }
        }

        return [
            'summary' => $recommendations['summary'] ?? null,
            'milestones' => $milestones,
            'open_dispute_tasks' => $openTasks,
            'progress_snapshot' => [
                'trend' => $progressTimeline['trend'] ?? null,
                'score_change' => $progressTimeline['score_change'] ?? [],
                'derogatory_change' => $progressTimeline['derogatory_change'] ?? null,
                'inquiry_change' => $progressTimeline['inquiry_change'] ?? null,
                'utilization_change' => $progressTimeline['utilization_change'] ?? null,
                'next_review' => (new DateTimeImmutable('now'))->add(new DateInterval('P30D'))->format('Y-m-d'),
            ],
        ];
    }

    private function mapPoint(DateTimeImmutable $parsedAt, array $clientData, array $derogatory, array $inquiries, array $creditInfo): array
    {
        $scores = [
            'trans_union' => $this->toInt($clientData['trans_union']['credit_score'] ?? null),
            'experian' => $this->toInt($clientData['experian']['credit_score'] ?? null),
            'equifax' => $this->toInt($clientData['equifax']['credit_score'] ?? null),
        ];

        return [
            'label' => $parsedAt->format('Y-m-d'),
            'scores' => $scores,
            'average_score' => $this->average(array_filter($scores, static fn($value) => $value !== null)),
            'derogatory_total' => $this->countDerogatory($derogatory),
            'inquiries_total' => $this->extractInt($inquiries['total'] ?? $inquiries['returned'] ?? null),
            'utilization' => $this->toFloat($creditInfo['total_percent'] ?? null),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $points
     * @return array<string, float|int|null>
     */
    private function calculateScoreChange(array $points): array
    {
        if (count($points) < 2) {
            return [
                'trans_union' => null,
                'experian' => null,
                'equifax' => null,
                'average' => null,
            ];
        }

        $first = $points[0];
        $last = $points[array_key_last($points)];

        return [
            'trans_union' => $this->diffNumeric($first['scores']['trans_union'] ?? null, $last['scores']['trans_union'] ?? null),
            'experian' => $this->diffNumeric($first['scores']['experian'] ?? null, $last['scores']['experian'] ?? null),
            'equifax' => $this->diffNumeric($first['scores']['equifax'] ?? null, $last['scores']['equifax'] ?? null),
            'average' => $this->diffNumeric($first['average_score'] ?? null, $last['average_score'] ?? null, true),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $points
     */
    private function calculateDelta(array $points, string $field, bool $float): ?float
    {
        if (count($points) < 2) {
            return null;
        }

        $first = $points[0][$field] ?? null;
        $last = $points[array_key_last($points)][$field] ?? null;

        if (!is_numeric($first) || !is_numeric($last)) {
            return null;
        }

        $delta = (float) $last - (float) $first;

        return $float ? round($delta, 2) : (float) (int) round($delta);
    }

    /**
     * @param array<string, float|int|null> $scoreChange
     */
    private function describeTrend(array $scoreChange): string
    {
        $average = $scoreChange['average'] ?? null;
        if (!is_numeric($average)) {
            return 'Stable';
        }

        if ($average > 0) {
            return 'Improving';
        }

        if ($average < 0) {
            return 'Declining';
        }

        return 'Stable';
    }

    private function diffNumeric(mixed $start, mixed $end, bool $allowFloat = false): ?float
    {
        if (!is_numeric($start) || !is_numeric($end)) {
            return null;
        }

        $delta = (float) $end - (float) $start;

        return $allowFloat ? round($delta, 2) : (float) (int) round($delta);
    }

    private function toInt(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        return null;
    }

    private function toFloat(mixed $value): ?float
    {
        if ($value === null) {
            return null;
        }

        if (is_float($value)) {
            return round($value, 2);
        }

        if (is_int($value)) {
            return (float) $value;
        }

        if (is_string($value)) {
            $normalized = str_replace([',', '%', '$'], '', $value);
            if (is_numeric($normalized)) {
                return round((float) $normalized, 2);
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $derogatory
     */
    private function countDerogatory(array $derogatory): int
    {
        if (isset($derogatory['returned']) && is_numeric($derogatory['returned'])) {
            return (int) $derogatory['returned'];
        }

        if (isset($derogatory['accounts']) && is_array($derogatory['accounts'])) {
            return count($derogatory['accounts']);
        }

        if (isset($derogatory['total']) && is_numeric($derogatory['total'])) {
            return (int) $derogatory['total'];
        }

        return 0;
    }

    private function extractInt(mixed $value): int
    {
        $int = $this->toInt($value);

        return $int ?? 0;
    }

    /**
     * @param array<int, array<string, mixed>> $accounts
     */
    private function countDerogatoryForBureau(array $accounts, string $bureauKey): int
    {
        $statusKey = $bureauKey . '_account_status';
        $paymentKey = $bureauKey . '_payment_status';
        $keywords = ['derogatory', 'collection', 'chargeoff', 'late'];
        $count = 0;

        foreach ($accounts as $account) {
            $status = strtolower((string) ($account[$statusKey] ?? ''));
            $payment = strtolower((string) ($account[$paymentKey] ?? ''));

            foreach ($keywords as $keyword) {
                if ($status !== '' && str_contains($status, $keyword)) {
                    ++$count;
                    continue 2;
                }

                if ($payment !== '' && str_contains($payment, $keyword)) {
                    ++$count;
                    continue 2;
                }
            }
        }

        return $count;
    }

    /**
     * @param array<int, int|null> $values
     */
    private function average(array $values): ?float
    {
        $filtered = array_filter($values, static fn($value) => is_numeric($value));
        if ($filtered === []) {
            return null;
        }

        return round(array_sum($filtered) / count($filtered), 2);
    }

    /**
     * @param array<string, mixed> $clientData
     */
    private function averageScores(array $clientData): ?float
    {
        $scores = [];
        foreach (['trans_union', 'experian', 'equifax'] as $key) {
            $score = $this->toInt($clientData[$key]['credit_score'] ?? null);
            if ($score !== null) {
                $scores[] = $score;
            }
        }

        return $this->average($scores);
    }

    private function buildUtilizationRecommendation(array $creditInfo, DateTimeImmutable $now): ?array
    {
        $overallPercent = $this->toFloat($creditInfo['total_percent'] ?? null);
        if ($overallPercent === null || $overallPercent <= 0) {
            return null;
        }

        $totalLimit = $this->toFloat($creditInfo['total_limit'] ?? null) ?? 0.0;
        $totalBalance = $this->toFloat($creditInfo['total_balance'] ?? null) ?? 0.0;
        $targetPercent = 30.0;
        $targetBalance = $totalLimit * ($targetPercent / 100);
        $paydown = max(0.0, $totalBalance - $targetBalance);

        $score = (int) min(100, max(40, round($overallPercent * 1.5)));
        $impact = $overallPercent >= 45 ? 'High' : ($overallPercent >= 30 ? 'Medium' : 'Low');
        $urgency = $overallPercent >= 50 ? 'Immediate' : ($overallPercent >= 30 ? 'This month' : 'Monitor');

        $actions = ['Reallocate payments toward revolving balances to bring utilization below 30% overall.'];
        if ($paydown > 0) {
            $actions[] = sprintf('Pay down approximately $%s to reach %.0f%% utilization.', number_format($paydown, 2, '.', ','), $targetPercent);
        }
        $actions[] = 'Consider requesting targeted credit line increases once balances are below 30%.';

        return [
            'id' => 'utilization',
            'title' => 'Lower revolving utilization',
            'summary' => sprintf(
                'Overall utilization is %.1f%% with $%s outstanding balances.',
                $overallPercent,
                number_format($totalBalance, 2, '.', ',')
            ),
            'score' => $score,
            'impact' => $impact,
            'urgency' => $urgency,
            'owner' => 'Client',
            'next_steps' => $actions,
            'target_date' => $now->add(new DateInterval('P21D'))->format('Y-m-d'),
        ];
    }

    private function buildDerogatoryRecommendation(array $derogatory, ?array $workflow, DateTimeImmutable $now): ?array
    {
        $total = $this->countDerogatory($derogatory);
        if ($total <= 0) {
            return null;
        }

        $score = min(100, max(55, 60 + ($total * 5)));
        $impact = $total >= 5 ? 'High' : 'Medium';

        $targets = [];
        if (isset($workflow['recommended_items']) && is_array($workflow['recommended_items'])) {
            foreach (array_slice($workflow['recommended_items'], 0, 3) as $item) {
                if (is_array($item) && isset($item['account'])) {
                    $targets[] = (string) $item['account'];
                } elseif (is_string($item)) {
                    $targets[] = $item;
                }
            }
        }

        $actions = [
            'Prioritize dispute letters for recently updated derogatory tradelines.',
            'Collect supporting documentation (ID, proof of address, statements) and upload to the workspace.',
        ];

        if ($targets !== []) {
            $actions[] = sprintf('Focus on: %s.', implode(', ', $targets));
        }

        return [
            'id' => 'derogatory',
            'title' => 'Challenge derogatory items',
            'summary' => sprintf('Detected %d derogatory tradelines across bureaus.', $total),
            'score' => $score,
            'impact' => $impact,
            'urgency' => 'Immediate',
            'owner' => 'Analyst',
            'next_steps' => $actions,
            'target_date' => $now->add(new DateInterval('P14D'))->format('Y-m-d'),
        ];
    }

    private function buildInquiryRecommendation(array $inquiries, DateTimeImmutable $now): ?array
    {
        $total = $this->extractInt($inquiries['total'] ?? $inquiries['returned'] ?? null);
        if ($total <= 0) {
            return null;
        }

        $score = min(95, max(35, 40 + ($total * 4)));
        $impact = $total >= 8 ? 'High' : 'Medium';
        $urgency = $total >= 4 ? 'This quarter' : 'Monitor';

        $actions = [
            'Freeze new credit applications for the next 90 days to prevent further score suppression.',
            'Dispute unauthorized or duplicate hard inquiries with the reporting bureaus.',
            'Document the permissible purpose for each inquiry to prepare for bureau responses.',
        ];

        return [
            'id' => 'inquiries',
            'title' => 'Control hard inquiries',
            'summary' => sprintf('%d hard inquiries reported in the last 24 months.', $total),
            'score' => $score,
            'impact' => $impact,
            'urgency' => $urgency,
            'owner' => 'Client',
            'next_steps' => $actions,
            'target_date' => $now->add(new DateInterval('P30D'))->format('Y-m-d'),
        ];
    }

    private function buildPositiveCreditRecommendation(float $averageScore, DateTimeImmutable $now): array
    {
        $score = (int) min(90, max(40, round((700 - $averageScore) * 0.2 + 45)));

        return [
            'id' => 'credit_building',
            'title' => 'Add positive credit builders',
            'summary' => sprintf('Average score %.0f suggests room to add positive tradelines.', $averageScore),
            'score' => $score,
            'impact' => 'Medium',
            'urgency' => 'This quarter',
            'owner' => 'Client',
            'next_steps' => [
                'Open a secured card or credit builder account with on-time payment automation.',
                'Keep new balances under 10% of the credit limit to maximize score gains.',
                'Review installment accounts for opportunities to accelerate payoff without harming mix.',
            ],
            'target_date' => $now->add(new DateInterval('P60D'))->format('Y-m-d'),
        ];
    }

    private function buildTrendRecommendation(float $averageChange, array $timeline, DateTimeImmutable $now): array
    {
        $score = (int) min(95, max(50, 70 + (int) abs($averageChange)));

        $derogChange = $timeline['derogatory_change'] ?? null;
        $utilizationChange = $timeline['utilization_change'] ?? null;

        $drivers = [];
        if (is_numeric($derogChange) && $derogChange > 0) {
            $drivers[] = sprintf('%d more derogatory items than the starting period', (int) $derogChange);
        }
        if (is_numeric($utilizationChange) && $utilizationChange > 0) {
            $drivers[] = sprintf('utilization up by %.1f%%', $utilizationChange);
        }

        $summary = 'Average bureau scores declined over the review period.';
        if ($drivers !== []) {
            $summary .= ' Drivers: ' . implode('; ', $drivers) . '.';
        }

        return [
            'id' => 'trend',
            'title' => 'Stabilize score trend',
            'summary' => $summary,
            'score' => $score,
            'impact' => 'High',
            'urgency' => 'Immediate',
            'owner' => 'Analyst',
            'next_steps' => [
                'Schedule a strategy session to review dispute statuses and payment plans.',
                'Monitor bureau updates weekly until scores recover.',
                'Escalate stubborn items to creditor-level interventions if no movement within 30 days.',
            ],
            'target_date' => $now->add(new DateInterval('P7D'))->format('Y-m-d'),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $recommendations
     */
    private function summarizeRecommendations(array $recommendations, ?float $overall): string
    {
        if ($recommendations === []) {
            return 'Scores are stable. Maintain current habits and monitor for bureau updates.';
        }

        $top = array_slice($recommendations, 0, 2);
        $titles = array_map(static fn(array $rec) => strtolower($rec['title']), $top);
        $focus = $this->joinWithAnd($titles);

        $improvementTarget = $overall !== null ? max(5, (int) round($overall / 8)) : 10;

        return sprintf('Focus on %s to unlock the next %d-point improvement.', $focus, $improvementTarget);
    }

    /**
     * @param array<int, string> $items
     */
    private function joinWithAnd(array $items): string
    {
        $items = array_filter($items, static fn($value) => $value !== '');
        $count = count($items);

        if ($count === 0) {
            return 'key actions';
        }

        if ($count === 1) {
            return $items[0];
        }

        $last = array_pop($items);

        return implode(', ', $items) . ' and ' . $last;
    }
}
