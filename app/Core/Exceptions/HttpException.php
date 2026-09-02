<?php
namespace App\Core\Exceptions;

class HttpException extends \Exception
{
    protected int $statusCode;

    public function __construct(int $statusCode, string $message = '', \Throwable $previous = null)
    {
        $this->statusCode = $statusCode;
        $defaultMessages = [
            404 => 'صفحه مورد نظر پیدا نشد',
            403 => 'دسترسی ممنوع',
            419 => 'نشست منقضی شده است',
            500 => 'خطای داخلی سرور',
        ];
        $message = $message ?: ($defaultMessages[$statusCode] ?? 'خطا');
        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}