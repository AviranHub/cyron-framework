<?php

namespace Cyron\Exceptions;

class HttpException extends \Exception
{
    protected int $statusCode;

    public function __construct(int $statusCode, string $message = '', ?\Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        $defaultMessages = [
            404 => 'Page Not Found',
            403 => 'Forbidden',
            419 => 'Session Expired',
            500 => 'Server Error',
        ];
        $message = $message !== '' ? $message : ($defaultMessages[$statusCode] ?? 'Error');
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
