<?php
namespace App\Analytics;
use App\Models\UserActivity;
class ActivityTracker {
 public static function record(string $event, array $properties = [], ?int $userId = null, ?string $label = null): void {
  $parts=explode('.', $event, 2);
  $definition = EventRegistry::get($event);
  UserActivity::create(['user_id'=>$userId,'category'=>$definition['category'] ?? ($parts[0] ?: 'app'),'action'=>$parts[1] ?? 'occurred','event'=>$event,'label'=>$label ?: ($definition['label'] ?? $event),'properties'=>json_encode($properties, JSON_UNESCAPED_UNICODE),'occurred_at'=>date('Y-m-d H:i:s')]);
 }
}