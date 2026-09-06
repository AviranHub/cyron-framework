<?php

namespace Cyron\Queue;

use Cyron\Database\Db;

class Worker
{
    public function work($queue = 'default', $maxJobs = 1, $maxAttempts = 3, $output = null)
    {
        $output = $output ?: static function ($message) {
            echo $message . PHP_EOL;
        };
        $processed = 0;

        $this->releaseExpired($queue);

        while ($processed < max(1, (int) $maxJobs)) {
            $job = $this->claim($queue);
            if ($job === null) {
                break;
            }

            $processed++;
            try {
                $instance = Queue::decode($job['payload']);

                $instance->handle();
                $this->delete($job['id']);
                $output('Job ' . $job['id'] . ' completed.');
            } catch (\Throwable $exception) {
                $this->fail($job, $exception, $maxAttempts);
                $output('Job ' . $job['id'] . ' failed: ' . $exception->getMessage());
            }
        }

        return $processed;
    }

    public function daemon($queue = 'default', $maxAttempts = 3, $sleep = 5, $output = null)
    {
        $output = $output ?: static function ($message) {
            echo $message . PHP_EOL;
        };

        while (true) {
            $processed = $this->work($queue, 1, $maxAttempts, $output);
            if ($processed === 0) {
                sleep(max(1, (int) $sleep));
            }
        }
    }

    protected function claim($queue)
    {
        $db = Db::getInstance();
        $db->begin_transaction();
        try {
            $now = time();
            $stmt = $db->prepare('SELECT id, payload, attempts FROM jobs WHERE queue = ? AND failed_at IS NULL AND reserved_at IS NULL AND available_at <= ? ORDER BY id ASC LIMIT 1 FOR UPDATE');
            $stmt->bind_param('si', $queue, $now);
            $stmt->execute();
            $job = $stmt->get_result()->fetch_assoc();
            if (!$job) {
                $db->commit();
                return null;
            }

            $reservedAt = time();
            $update = $db->prepare('UPDATE jobs SET reserved_at = ?, attempts = attempts + 1 WHERE id = ?');
            $update->bind_param('ii', $reservedAt, $job['id']);
            $update->execute();
            $db->commit();
            $job['attempts'] = (int) $job['attempts'] + 1;
            return $job;
        } catch (\Throwable $exception) {
            $db->rollback();
            throw $exception;
        }
    }

    protected function releaseExpired($queue, $timeout = 3600)
    {
        $db = Db::getInstance();
        $threshold = time() - max(60, (int) $timeout);
        $stmt = $db->prepare('UPDATE jobs SET reserved_at = NULL WHERE queue = ? AND failed_at IS NULL AND reserved_at IS NOT NULL AND reserved_at < ?');
        $stmt->bind_param('si', $queue, $threshold);
        $stmt->execute();
    }

    protected function delete($id)
    {
        $db = Db::getInstance();
        $stmt = $db->prepare('DELETE FROM jobs WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
    }

    protected function fail(array $job, \Throwable $exception, $maxAttempts)
    {
        $db = Db::getInstance();
        $error = substr($exception->getMessage(), 0, 65535);
        if ((int) $job['attempts'] >= (int) $maxAttempts) {
            $failedAt = time();
            $stmt = $db->prepare('UPDATE jobs SET reserved_at = NULL, failed_at = ?, last_error = ? WHERE id = ?');
            $stmt->bind_param('isi', $failedAt, $error, $job['id']);
        } else {
            $availableAt = time() + (int) pow(2, max(0, (int) $job['attempts'] - 1)) * 60;
            $stmt = $db->prepare('UPDATE jobs SET reserved_at = NULL, available_at = ?, last_error = ? WHERE id = ?');
            $stmt->bind_param('isi', $availableAt, $error, $job['id']);
        }
        $stmt->execute();
    }
}
