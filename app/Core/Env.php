<?php

namespace App\Core;

if (!class_exists('Cyron\\Support\\Env')) {
	require_once dirname(__DIR__, 2) . '/src/Cyron/Support/Env.php';
}

/**
 * Backward-compatible alias for the framework environment reader.
 */
class Env extends \Cyron\Support\Env
{
}
