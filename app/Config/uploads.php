<?php
return [
 'default_max_size' => 10 * 1024 * 1024,
 'allowed_mime' => [
   'image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','application/pdf'=>'pdf',
 ],
 'rules' => [
   // 'covers' => ['max_size'=>5*1024*1024,'allowed_mime'=>['image/jpeg'=>'jpg','image/webp'=>'webp']],
 ],
];
