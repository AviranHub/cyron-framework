<?php

namespace App\Services;

use Cyron\Support\Env;

class ZarinpalGateway
{
    public function request(int $amount, string $description, string $callbackUrl): array
    {
        $merchantId = trim((string) Env::get('ZARINPAL_MERCHANT_ID', ''));
        if ($merchantId === '') return ['success' => false, 'message' => 'کلید درگاه پرداخت تنظیم نشده است.'];

        $response = $this->post('/pg/v4/payment/request.json', [
            'merchant_id' => $merchantId,
            'amount' => $amount,
            'currency' => Env::get('ZARINPAL_CURRENCY', 'IRT'),
            'callback_url' => $callbackUrl,
            'description' => $description,
        ]);

        $data = $response['data'] ?? [];
        if (($data['code'] ?? null) !== 100 || empty($data['authority'])) {
            return ['success' => false, 'message' => $data['message'] ?? 'درخواست پرداخت از درگاه ناموفق بود.'];
        }

        return ['success' => true, 'authority' => (string) $data['authority']];
    }

    public function verify(int $amount, string $authority): array
    {
        $merchantId = trim((string) Env::get('ZARINPAL_MERCHANT_ID', ''));
        if ($merchantId === '') return ['success' => false, 'message' => 'کلید درگاه پرداخت تنظیم نشده است.'];

        $response = $this->post('/pg/v4/payment/verify.json', [
            'merchant_id' => $merchantId,
            'amount' => $amount,
            'authority' => $authority,
        ]);

        $data = $response['data'] ?? [];
        return [
            'success' => in_array($data['code'] ?? null, [100, 101], true),
            'ref_id' => $data['ref_id'] ?? null,
            'response' => $response,
            'message' => $data['message'] ?? 'تایید پرداخت ناموفق بود.',
        ];
    }

    public function startUrl(string $authority): string
    {
        $base = filter_var(Env::get('ZARINPAL_SANDBOX', false), FILTER_VALIDATE_BOOLEAN)
            ? 'https://sandbox.zarinpal.com'
            : 'https://www.zarinpal.com';
        return $base . '/pg/StartPay/' . rawurlencode($authority);
    }

    private function post(string $path, array $payload): array
    {
        $base = filter_var(Env::get('ZARINPAL_SANDBOX', false), FILTER_VALIDATE_BOOLEAN)
            ? 'https://sandbox.zarinpal.com'
            : 'https://api.zarinpal.com';
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\nAccept: application/json\r\n",
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout' => 15,
                'ignore_errors' => true,
            ],
        ]);
        $body = @file_get_contents($base . $path, false, $context);
        if ($body === false) return [];
        $decoded = json_decode($body, true);
        return is_array($decoded) ? $decoded : [];
    }
}