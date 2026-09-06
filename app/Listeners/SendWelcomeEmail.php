<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Cyron\Mail\Mailer;

class SendWelcomeEmail
{
    public function handle(UserRegistered $event)
    {
        return (new Mailer())
            ->to($event->email, $event->name)
            ->subject('Welcome to Cyron')
            ->body('<p>Hello ' . htmlspecialchars($event->name, ENT_QUOTES, 'UTF-8') . ', welcome to our application.</p>')
            ->send();
    }
}
