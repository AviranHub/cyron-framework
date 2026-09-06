<?php

namespace App\Events;

class UserRegistered
{
    public function __construct(
        public int $userId,
        public string $email,
        public string $name
    ) {
    }
}
