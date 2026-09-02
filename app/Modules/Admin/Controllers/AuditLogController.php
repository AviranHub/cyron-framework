<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controller;
use App\Models\AuditLog;
class AuditLogController extends Controller {
 public function index(){
  $query=AuditLog::query();
  if($action=request()->input('action'))$query->where('action','=',$action);
  if($actor=request()->input('actor_id'))$query->where('actor_id','=',(int)$actor);
  if($from=request()->input('from'))$query->where('occurred_at','>=',$from.' 00:00:00');
  if($to=request()->input('to'))$query->where('occurred_at','<=',$to.' 23:59:59');
  if($q=request()->input('q'))$query->whereRaw('(action LIKE ? OR context LIKE ?)', ['%'.$q.'%','%'.$q.'%']);
  $logs=$query->orderBy('occurred_at','desc')->paginate(50);
  return view('admin.audit.index',compact('logs'));
 }
 public function show($id){$log=AuditLog::findOrFail($id);$context=$log->contextData();return view('admin.audit.show',compact('log','context'));}
}