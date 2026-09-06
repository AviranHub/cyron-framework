<?php

namespace App\Services;

use App\Models\View;
use Cyron\Authentication\Auth;

class ViewTracker
{
    public static function record(string $subjectType, int $subjectId, ?int $userId = null): bool
    {
        $userId ??= Auth::id();
        $sessionId = session_id() ?: null;
        $fingerprint = $userId ? 'user:' . $userId : 'session:' . ($sessionId ?: ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        $recent = View::where('subject_type', $subjectType)
            ->where('subject_id', $subjectId)
            ->where('session_id', $fingerprint)
            ->where('viewed_at', '>=', date('Y-m-d H:i:s', time() - 1800))
            ->first();
        if ($recent) return false;

        View::create([
            'user_id' => $userId,
            'session_id' => $fingerprint,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'viewed_at' => date('Y-m-d H:i:s'),
        ]);
        return true;
    }
}