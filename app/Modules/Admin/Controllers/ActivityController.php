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
        $users = User::query()->orderBy('name')->limit(500)->get();
        $activityRows = [];
        foreach ($activities->items() as $activity) {
            $activityRows[] = ['activity' => $activity, 'user' => $activity->user_id ? User::find((int) $activity->user_id) : null];
        }
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
        $lastActivity = (clone $base)->orderBy('occurred_at','desc')->first();
        $activities = (clone $base)->orderBy('occurred_at','desc')->paginate(50);
        return view('admin.activities.user', compact('user','activities','totalActivities','activeDays','lastActivity'));
    }
}