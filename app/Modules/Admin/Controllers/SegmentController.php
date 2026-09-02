<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controller;
use App\Models\User;
use App\Analytics\SegmentRegistry;

class SegmentController extends Controller {
 public function index(){
  $segments=[];
  foreach(SegmentRegistry::all() as $key=>$definition){
   $resolver=$definition['resolver']??null;
   $count=is_callable($resolver)?(int)$resolver():0;
   $segments[]=['key'=>$key,'label'=>$definition['label'],'description'=>$definition['description']??null,'count'=>$count];
  }
  return view('admin.analytics.segments',compact('segments'));
 }
}