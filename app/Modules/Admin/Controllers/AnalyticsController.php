<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Models\UserActivity;

class AnalyticsController extends Controller
{
    public function index()
    {
        $base = UserActivity::query();
        $today = date('Y-m-d');
        $eventsToday = (clone $base)->where('occurred_at','>=',$today.' 00:00:00')->count();
        $activeUsers = (clone $base)->where('occurred_at','>=',$today.' 00:00:00')->whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $topEvents = (clone $base)->selectRaw('event, label, COUNT(*) as total')->whereNotNull('event')->groupBy('event','label')->orderBy('total','desc')->limit(10)->get();
        $metrics = [];
        foreach (MetricRegistry::all() as $key => $definition) {
            $query = UserActivity::query();
            if ($definition['event']) $query->where('event', '=', $definition['event']);
            $aggregation = $definition['aggregation'] ?? 'count';
            $value = 0;
            if ($aggregation === 'count') $value = $query->count();
            elseif ($aggregation === 'unique_users') $value = $query->whereNotNull('user_id')->distinct('user_id')->count('user_id');
            elseif (in_array($aggregation, ['sum','average'], true)) {
                $property = $definition['property'] ?? null;
                if ($property) {
                    $values = $query->get()->map(function ($row) use ($property) {
                        $data = json_decode($row->properties ?? '{}', true) ?: [];
                        return isset($data[$property]) && is_numeric($data[$property]) ? (float)$data[$property] : null;
                    })->filter(fn($v) => $v !== null)->all();
                    $value = $aggregation === 'sum' ? array_sum($values) : (count($values) ? array_sum($values) / count($values) : 0);
                }
            }
            $metrics[] = ['key'=>$key, 'label'=>$definition['label'], 'value'=>$value, 'aggregation'=>$aggregation];
        }
        return view('admin.analytics.index', compact('eventsToday','activeUsers','topEvents','metrics'));
    }
}