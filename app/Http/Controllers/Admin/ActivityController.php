<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\AuditLog;
use Cyron\Authentication\Auth;
use Cyron\Http\Response;

class ActivityController extends Controller
{
    public function index()
    {
        $query = $this->filteredQuery();

        $activities = $query->orderBy('occurred_at', 'desc')->paginate(50);
        $users = User::query()->orderBy('name')->limit(500)->get();
        $activityRows = [];
        $userIds = [];
        foreach ($activities->items() as $activity) if ($activity->user_id) $userIds[(int) $activity->user_id] = true;
        $activityUsers = [];
        if ($userIds) foreach (User::query()->whereIn('id', array_keys($userIds))->get() as $user) $activityUsers[(int) $user->id] = $user;
        foreach ($activities->items() as $activity) $activityRows[] = ['activity' => $activity, 'user' => $activityUsers[(int) $activity->user_id] ?? null];

        return view('admin.activities.index', compact('activities', 'users', 'activityRows'));
    }

    public function user(int $id)
    {
        $user = User::find($id);
        if (!$user) abort(404);

        $base = UserActivity::query()->where('user_id', '=', $id);
        $totalActivities = (clone $base)->count();
        $activeDays = [];
        foreach ((clone $base)->select(['occurred_at'])->get() as $activity) {
            $activeDays[substr((string) $activity->occurred_at, 0, 10)] = true;
        }
        $activeDays = count($activeDays);
        $lastActivity = (clone $base)->orderBy('occurred_at', 'desc')->first();
        $activities = (clone $base)->orderBy('occurred_at', 'desc')->paginate(50);

        return view('admin.activities.user', compact(
            'user',
            'activities',
            'totalActivities',
            'activeDays',
            'lastActivity'
        ));
    }

    public function export()
    {
        $rows = [['user_id', 'action', 'category', 'subject_type', 'subject_id', 'amount', 'occurred_at']];
        foreach ($this->filteredQuery()->orderBy('occurred_at', 'desc')->limit(10000)->get() as $activity) {
            $rows[] = [$activity->user_id, $activity->action, $activity->category, $activity->subject_type, $activity->subject_id, $activity->amount, $activity->occurred_at];
        }
        $this->recordExport('activity.csv');
        return $this->csvResponse($rows, 'activity-export.csv');
    }

    private function filteredQuery()
    {
        $query = UserActivity::query();
        if ($userId = request()->input('user_id')) $query->where('user_id', '=', (int) $userId);
        if ($category = request()->input('category')) $query->where('category', '=', trim($category));
        if ($action = request()->input('action')) $query->where('action', '=', trim($action));
        [$from, $to] = $this->dateRange();
        $query->where('occurred_at', '>=', $from . ' 00:00:00')->where('occurred_at', '<=', $to . ' 23:59:59');
        return $query;
    }

    private function dateRange(): array
    {
        $to = trim((string) request()->input('to', date('Y-m-d')));
        $from = trim((string) request()->input('from', date('Y-m-d', strtotime('-30 days'))));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-d', strtotime('-30 days'));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) $to = date('Y-m-d');
        if ($from > $to) [$from, $to] = [$to, $from];
        $toDate = new \DateTimeImmutable($to);
        if ((new \DateTimeImmutable($from))->diff($toDate)->days > 366) $from = $toDate->modify('-366 days')->format('Y-m-d');
        return [$from, $to];
    }

    private function recordExport(string $report): void
    {
        $actorId = Auth::id();
        if (!$actorId) return;
        AuditLog::create([
            'actor_id' => (int) $actorId,
            'action' => 'admin.report.exported',
            'context' => json_encode(['report' => $report, 'from' => request()->input('from'), 'to' => request()->input('to')], JSON_UNESCAPED_UNICODE),
            'occurred_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function csvResponse(array $rows, string $filename): Response
    {
        $output = "\xEF\xBB\xBF";
        foreach ($rows as $row) {
            $output .= implode(',', array_map(static function ($value) {
                return '"' . str_replace('"', '""', (string) ($value ?? '')) . '"';
            }, $row)) . "\r\n";
        }
        return new Response($output, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
