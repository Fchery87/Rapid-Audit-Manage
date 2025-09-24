<?php

namespace App\Service\Report;

class ActionPlanExporter
{
    /**
     * @param array<string, mixed> $actionPlan
     */
    public function toCsv(array $actionPlan): string
    {
        $rows = [];

        if (!empty($actionPlan['summary'])) {
            $rows[] = ['Summary', $this->stringify($actionPlan['summary'])];
            $rows[] = [];
        }

        $rows[] = ['Priority', 'Owner', 'Title', 'Due', 'Score', 'Impact', 'Urgency', 'Details'];
        foreach ($actionPlan['milestones'] ?? [] as $milestone) {
            $rows[] = [
                $this->stringify($milestone['priority'] ?? ''),
                $this->stringify($milestone['owner'] ?? ''),
                $this->stringify($milestone['title'] ?? ''),
                $this->stringify($milestone['due'] ?? ''),
                $this->stringify($milestone['score'] ?? ''),
                $this->stringify($milestone['impact'] ?? ''),
                $this->stringify($milestone['urgency'] ?? ''),
                $this->stringify($milestone['details'] ?? ''),
            ];
        }

        if (!empty($actionPlan['open_dispute_tasks'])) {
            $rows[] = [];
            $rows[] = ['Open Dispute Tasks'];
            $rows[] = ['Owner', 'Task', 'Due', 'Status'];
            foreach ($actionPlan['open_dispute_tasks'] as $task) {
                $rows[] = [
                    $this->stringify($task['owner'] ?? ''),
                    $this->stringify($task['title'] ?? ''),
                    $this->stringify($task['due'] ?? ''),
                    $this->stringify($task['status'] ?? ''),
                ];
            }
        }

        if (!empty($actionPlan['progress_snapshot']) && is_array($actionPlan['progress_snapshot'])) {
            $rows[] = [];
            $rows[] = ['Progress Snapshot'];
            foreach ($actionPlan['progress_snapshot'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        $rows[] = [
                            $this->humanizeKey($key . '_' . (string) $subKey),
                            $this->stringify($subValue),
                        ];
                    }
                    continue;
                }

                $rows[] = [
                    $this->humanizeKey((string) $key),
                    $this->stringify($value),
                ];
            }
        }

        return $this->rowsToCsv($rows);
    }

    /**
     * @param array<int, array<int, string>> $rows
     */
    private function rowsToCsv(array $rows): string
    {
        $lines = [];

        foreach ($rows as $row) {
            if ($row === []) {
                $lines[] = '';
                continue;
            }

            $lines[] = implode(',', array_map([$this, 'escapeValue'], $row));
        }

        return implode("\n", $lines);
    }

    private function escapeValue(string $value): string
    {
        $needsQuotes = str_contains($value, '"') || str_contains($value, ',') || str_contains($value, "\n");

        if ($needsQuotes) {
            $value = '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }

    private function humanizeKey(string $key): string
    {
        $key = str_replace(['_', '-'], ' ', strtolower($key));
        $parts = array_filter(explode(' ', $key));

        $parts = array_map(static function (string $part): string {
            if ($part === 'tu') {
                return 'TransUnion';
            }

            if ($part === 'eq') {
                return 'Equifax';
            }

            if ($part === 'ex') {
                return 'Experian';
            }

            return ucfirst($part);
        }, $parts);

        return $parts === [] ? '' : implode(' ', $parts);
    }

    private function stringify(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if (is_numeric($value)) {
            $numeric = (float) $value;
            if (floor($numeric) === $numeric) {
                return (string) (int) $numeric;
            }

            return number_format($numeric, 2, '.', '');
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }
}
