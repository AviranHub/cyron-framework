<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controller;
use App\Models\User;
use App\Models\UserActivity;

class ActivityController extends Controller
{
    public function index()
    {
        $query = UserActivity::query();
        if ($userId = request()->input('user_id')) $query->where('user_id', '=', (int)$userId);
        if ($category = request()->input('category')) $query->where('category', '=', $category);
        if ($action = request()->input('action')) $query->where('action', '=', $action);
        if ($from = request()->input('from')) $query->where('occurred_at', '>=', $from . ' 00:00:00');
        if ($to = request()->input('to')) $query->where('occurred_at', '<=', $to . ' 23:59:59');
        $activities = $query->orderBy('occurred_at', 'desc')->paginate(50);
        return view('admin.activities.index', compact('activities'));
    }

    public function user(int $id)
    {
        $user = User::find($id);
        if (!$user) abort(404);
        $base = UserActivity::query()->where('user_id', '=', $id);
        $totalActivities = (clone $base)->count();
        $activeDays = (clone $base)->selectRaw('DATE(occurred_at) as activity_day')->groupBy('activity_day')->get()->count();
        $lastActivity = (clone $base)->orderBy('occurred_at','desc')->first();
        $topEvents = (clone $base)->selectRaw('event, label, COUNT(*) as total')->whereNotNull('event')->groupBy('event','label')->orderBy('total','desc')->limit(5)->get();
        $activities = (clone $base)->orderBy('occurred_at','desc')->paginate(50);
        return view('admin.activities.user', compact('user','activities','totalActivities','activeDays','lastActivity','topEvents'));
    }
}