<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;
use Cyron\Database\Db;

class QueueRetryCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public static function getDescription()
    {
        return 'Retry a failed job by id, or retry all failed jobs';
    }

    public function execute()
    {
        $id = $this->input->getArgument(1);
        $db = Db::getInstance();
        if ($id === 'all') {
            $stmt = $db->prepare('UPDATE jobs SET failed_at = NULL, reserved_at = NULL, attempts = 0, available_at = ?, last_error = NULL WHERE failed_at IS NOT NULL');
            $now = time();
            $stmt->bind_param('i', $now);
            $stmt->execute();
            echo Colors::green("Retried failed jobs: {$stmt->affected_rows}\n");
            return;
        }

        if (!ctype_digit((string) $id)) {
            echo Colors::error("Usage: php zeno queue:retry <id|all>\n");
            return;
        }

        $stmt = $db->prepare('UPDATE jobs SET failed_at = NULL, reserved_at = NULL, attempts = 0, available_at = ?, last_error = NULL WHERE id = ? AND failed_at IS NOT NULL');
        $now = time();
        $stmt->bind_param('ii', $now, $id);
        $stmt->execute();
        echo Colors::green($stmt->affected_rows ? "Job {$id} queued for retry.\n" : "Failed job {$id} not found.\n");
    }
}
