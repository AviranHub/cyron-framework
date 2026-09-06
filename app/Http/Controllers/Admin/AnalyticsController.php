<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Models\UserActivity;
use App\Models\AuditLog;
use Cyron\Authentication\Auth;
use Cyron\Analytics\MetricRegistry;
use Cyron\Http\Response;

class AnalyticsController extends Controller
{
    public function index()
    {
        [$from, $to] = $this->dateRange();
        $activities = $this->queryForRange($from, $to)->get();
        $daily = [];
        $activeUsers = [];
        $purchases = 0;
        $revenue = 0.0;

        foreach ($activities as $activity) {
            $day = substr((string) $activity->occurred_at, 0, 10);
            $daily[$day] = ($daily[$day] ?? 0) + 1;
            if ($activity->user_id) $activeUsers[(int) $activity->user_id] = true;
            if ($activity->category === 'purchase') {
                $purchases++;
                $revenue += (float) ($activity->amount ?? 0);
            }
        }

        $chart = [];
        $cursor = new \DateTimeImmutable($from);
        $end = new \DateTimeImmutable($to);
        $max = 1;
        while ($cursor <= $end) {
            $day = $cursor->format('Y-m-d');
            $value = $daily[$day] ?? 0;
            $max = max($max, $value);
            $chart[] = ['label' => $cursor->format('m/d'), 'value' => $value];
            $cursor = $cursor->modify('+1 day');
        }
        foreach ($chart as &$point) $point['height'] = max(4, (int) round(($point['value'] / $max) * 100));
        unset($point);

        $metrics = $this->metrics($activities);
        return view('admin.analytics.index', compact('from', 'to', 'chart', 'activeUsers', 'purchases', 'revenue', 'metrics'));
    }

    public function export()
    {
        [$from, $to] = $this->dateRange();
        $rows = [['date', 'events', 'active_users', 'purchases', 'revenue']];
        $daily = [];
        foreach ($this->queryForRange($from, $to)->get() as $activity) {
            $day = substr((string) $activity->occurred_at, 0, 10);
            $daily[$day]['events'] = ($daily[$day]['events'] ?? 0) + 1;
            if ($activity->user_id) $daily[$day]['users'][(int) $activity->user_id] = true;
            if ($activity->category === 'purchase') {
                $daily[$day]['purchases'] = ($daily[$day]['purchases'] ?? 0) + 1;
                $daily[$day]['revenue'] = ($daily[$day]['revenue'] ?? 0) + (float) ($activity->amount ?? 0);
            }
        }
        foreach ($daily as $day => $data) $rows[] = [$day, $data['events'] ?? 0, count($data['users'] ?? []), $data['purchases'] ?? 0, $data['revenue'] ?? 0];
        $this->recordExport('analytics.csv', $from, $to);
        return $this->csvResponse($rows, 'analytics-export.csv');
    }

    private function queryForRange(string $from, string $to)
    {
        return UserActivity::query()->where('occurred_at', '>=', $from . ' 00:00:00')->where('occurred_at', '<=', $to . ' 23:59:59');
    }

    private function dateRange(): array
    {
        $to = trim((string) request()->input('to', date('Y-m-d')));
        $from = trim((string) request()->input('from', date('Y-m-d', strtotime('-29 days'))));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-29 days'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) $to = date('Y-m-d');
        if ($from > $to) [$from, $to] = [$to, $from];
        $fromDate = new \DateTimeImmutable($from);
        $toDate = new \DateTimeImmutable($to);
        if ($fromDate->diff($toDate)->days > 366) $from = $toDate->modify('-366 days')->format('Y-m-d');
        return [$from, $to];
    }

    private function metrics($activities): array
    {
        $metrics = [];
        $activityRows = $activities->toArray();
        foreach (MetricRegistry::all() as $key => $definition) {
            $matching = array_filter($activityRows, static function ($activity) use ($definition) {
                return empty($definition['event']) || $activity->action === $definition['event'];
            });
            $aggregation = $definition['aggregation'] ?? 'count';
            $value = count($matching);
            if ($aggregation === 'unique_users') $value = count(array_unique(array_filter(array_map(static fn($activity) => $activity->user_id, $matching))));
            if ($aggregation === 'sum') $value = array_sum(array_map(static fn($activity) => (float) ($activity->amount ?? 0), $matching));
            $metrics[] = ['key' => $key, 'label' => $definition['label'], 'value' => $value, 'aggregation' => $aggregation];
        }
        return $metrics;
    }

    private function csvResponse(array $rows, string $filename): Response
    {
        $output = "\xEF\xBB\xBF";
        foreach ($rows as $row) $output .= implode(',', array_map(static fn($value) => '"' . str_replace('"', '""', (string) ($value ?? '')) . '"', $row)) . "\r\n";
        return new Response($output, 200, ['Content-Type' => 'text/csv; charset=utf-8', 'Content-Disposition' => 'attachment; filename="' . $filename . '"']);
    }

    private function recordExport(string $report, string $from, string $to): void
    {
        $actorId = Auth::id();
        if (!$actorId) return;
        AuditLog::create([
            'actor_id' => (int) $actorId,
            'action' => 'admin.report.exported',
            'context' => json_encode(['report' => $report, 'from' => $from, 'to' => $to], JSON_UNESCAPED_UNICODE),
            'occurred_at' => date('Y-m-d H:i:s'),
        ]);
    }
}