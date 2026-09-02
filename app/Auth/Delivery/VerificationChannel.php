<?php
namespace App\Auth\Delivery;
interface VerificationChannel { public function send(string $target,string $message,array $context=[]): bool; }