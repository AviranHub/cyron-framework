<?php
namespace Cyron\Authentication\Delivery;
interface VerificationChannel { public function send(string $target,string $message,array $context=[]): bool; }