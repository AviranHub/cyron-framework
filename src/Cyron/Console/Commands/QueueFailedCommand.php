<?php

namespace Cyron\Console\Commands;

use Cyron\Console\Colors;
use Cyron\Database\Db;

class QueueFailedCommand
{
    protected $input;

    public function __construct($input)
    {
        $this->input = $input;
    }

    public static function getDescription()
    {
        return 'List failed queued jobs';
    }

    public function execute()
    {
        $db = Db::getInstance();
        $result = $db->query('SELECT id, queue, attempts, last_error, failed_at FROM jobs WHERE failed_at IS NOT NULL ORDER BY id DESC');
        if (!$result || $result->num_rows === 0) {
            echo Colors::yellow("No failed jobs.\n");
            return;
        }

        while ($job = $result->fetch_assoc()) {
            echo sprintf("#%d [%s] attempts=%d %s\n", $job['id'], $job['queue'], $job['attempts'], $job['last_error']);
        }
    }
}
