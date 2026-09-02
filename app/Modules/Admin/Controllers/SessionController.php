<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controller;use App\Models\AuthSession;use App\Auth\SessionRegistry;use App\Audit\Audit;
class SessionController extends Controller{
 public function index(){
  $query=AuthSession::query()->where('revoked_at','=',null);
  if($user=request()->input('user_id')) $query->where('user_id','=',(int)$user);
  $sessions=$query->orderBy('last_seen_at','desc')->paginate(50);
  $summary=['active'=>0,'users'=>[],'stale'=>0];
  foreach(($sessions['data']??[]) as $session){$summary['active']++;$summary['users'][$session->user_id]=true;if(!empty($session->last_seen_at)&&strtotime($session->last_seen_at)<strtotime('-24 hours'))$summary['stale']++;}
  $summary['users']=count($summary['users']);
  return view('admin.security.sessions',compact('sessions','summary'));
}
 public function revoke($id){$s=AuthSession::findOrFail($id);SessionRegistry::revoke($s->id);Audit::record('auth.session_revoked',['target_user_id'=>$s->user_id,'session_id'=>$s->id]);return redirect()->route('admin.security.sessions');}
 public function revokeUser($userId){SessionRegistry::revokeUser((int)$userId);Audit::record('auth.user_sessions_revoked',['target_user_id'=>(int)$userId]);return redirect()->route('admin.security.sessions');}
}