<?php
namespace Cyron\Analytics;
use Cyron\Database\ModelRegistry;
use Cyron\Authentication\Auth;
class ActivityTracker {
 public static function record(string $event, array $properties = [], ?int $userId = null, ?string $label = null): void {
  $userId ??= Auth::id();
  $parts = explode('.', $event, 2);
  $definition = EventRegistry::get($event);
  $category = self::category($definition['category'] ?? ($parts[0] ?: 'system'));
  $request = function_exists('request') ? request() : null;
  $model = ModelRegistry::get('user_activity');
  $model::create([
    'user_id' => $userId ? (int) $userId : null,
      'category' => $category,
      'action' => $event,
      'subject_type' => $properties['subject_type'] ?? null,
      'subject_id' => isset($properties['subject_id']) ? (int) $properties['subject_id'] : null,
      'amount' => $properties['amount'] ?? null,
      'ip_address' => $request && method_exists($request, 'ip') ? $request->ip() : ($_SERVER['REMOTE_ADDR'] ?? null),
      'user_agent' => $request && method_exists($request, 'userAgent') ? $request->userAgent() : ($_SERVER['HTTP_USER_AGENT'] ?? null),
      'occurred_at' => date('Y-m-d H:i:s'),
      'data' => json_encode(array_merge([
          'label' => $label ?: ($definition['label'] ?? $event),
          'actor_type' => $userId ? 'user' : 'guest',
      ], $properties), JSON_UNESCAPED_UNICODE),
  ]);
 }

 private static function category(string $category): string
 {
  $map = ['users' => 'system', 'auth' => 'authentication', 'commerce' => 'purchase', 'reading' => 'content_interaction', 'support' => 'system'];
  $category = $map[$category] ?? $category;
  return in_array($category, ['authentication', 'browsing', 'purchase', 'content_interaction', 'profile', 'system'], true) ? $category : 'system';
 }
}