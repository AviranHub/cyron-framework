<?php

use App\Events\UserRegistered;
use App\Listeners\SendWelcomeEmail;
use Cyron\Events\EventDispatcher;

return static function (EventDispatcher $events) {
    $events->listen(UserRegistered::class, SendWelcomeEmail::class, true);
};