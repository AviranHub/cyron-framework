<?php

namespace Cyron\Queue;

use Cyron\Database\Db;

class Queue
{
    public static function push(Job $job, $delay = 0, $queue = 'default')
    {
        $db = Db::getInstance();
        $payload = self::encode($job);
        $availableAt = time() + max(0, (int) $delay);
        $createdAt = time();
        $stmt = $db->prepare('INSERT INTO jobs (queue, payload, attempts, available_at, created_at) VALUES (?, ?, 0, ?, ?)');
        if (!$stmt) {
            throw new \RuntimeException('Unable to prepare queue insert: ' . $db->error);
        }

        $stmt->bind_param('ssii', $queue, $payload, $availableAt, $createdAt);
        if (!$stmt->execute()) {
            throw new \RuntimeException('Unable to enqueue job: ' . $stmt->error);
        }

        return $stmt->insert_id;
    }

    public static function encode(Job $job)
    {
        return base64_encode(serialize($job));
    }

    public static function decode($payload)
    {
        $serialized = base64_decode($payload, true);
        if ($serialized === false || !preg_match('/^O:\d+:"([^"]+)":/', $serialized, $matches)) {
            throw new \RuntimeException('Invalid queued job payload.');
        }

        $className = $matches[1];
        if (!class_exists($className) || !is_a($className, Job::class, true)) {
            throw new \RuntimeException('Queued payload class is not an allowed Job.');
        }

        $job = unserialize($serialized, ['allowed_classes' => [$className]]);
        if (!$job instanceof Job) {
            throw new \RuntimeException('Queued payload must implement Cyron\\Queue\\Job.');
        }

        return $job;
    }

    public static function size($queue = 'default')
    {
        $db = Db::getInstance();
        $stmt = $db->prepare('SELECT COUNT(*) AS total FROM jobs WHERE queue = ? AND failed_at IS NULL AND reserved_at IS NULL AND available_at <= ?');
        $now = time();
        $stmt->bind_param('si', $queue, $now);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return (int) ($result['total'] ?? 0);
    }
}
