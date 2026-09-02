<?php
use App\Analytics\SegmentRegistry;

SegmentRegistry::registerMany([
 'recently_active'=>[
  'label'=>'کاربران فعال اخیر',
  'description'=>'تعریف نمونه؛ برنامه می‌تواند هر منطق دلخواهی داشته باشد.',
  'resolver'=>function(){ return 0; },
 ],
]);