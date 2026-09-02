<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controller;
use App\Models\LoginHistory;
class LoginHistoryController extends Controller {
 public function index(){
  $query=LoginHistory::query();
  if($user=request()->input('user_id'))$query->where('user_id','=',(int)$user);
  if(($status=request()->input('status'))!==null && $status!=='')$query->where('successful','=',(int)$status);
  if($from=request()->input('from'))$query->where('occurred_at','>=',$from.' 00:00:00');
  if($to=request()->input('to'))$query->where('occurred_at','<=',$to.' 23:59:59');
  $logs=$query->orderBy('occurred_at','desc')->paginate(50);
  return view('admin.security.logins',compact('logs'));
 }
}