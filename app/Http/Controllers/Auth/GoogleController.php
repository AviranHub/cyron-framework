<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controller;
use App\Models\User;
use Cyron\Authentication\Auth;
use Cyron\Http\Response;

class GoogleController extends Controller
{
    public function redirect()
    {
        $clientId = trim((string) \Cyron\Support\Env::get('GOOGLE_CLIENT_ID', ''));
        $redirectUri = $this->redirectUri();
        if ($clientId === '' || $redirectUri === '') {
            return redirect()->route('login')->with('error', 'ورود با گوگل هنوز تنظیم نشده است.');
        }

        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $state = bin2hex(random_bytes(32));
        $_SESSION['google_oauth_state'] = $state;
        $_SESSION['google_oauth_created_at'] = time();

        $query = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'access_type' => 'online',
            'prompt' => 'select_account',
        ]);

        return Response::redirect('https://accounts.google.com/o/oauth2/v2/auth?' . $query);
    }

    public function callback()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $state = (string) ($_GET['state'] ?? '');
        $expectedState = (string) ($_SESSION['google_oauth_state'] ?? '');
        $createdAt = (int) ($_SESSION['google_oauth_created_at'] ?? 0);
        unset($_SESSION['google_oauth_state'], $_SESSION['google_oauth_created_at']);

        if ($state === '' || $expectedState === '' || !hash_equals($expectedState, $state) || time() - $createdAt > 600) {
            return redirect()->route('login')->with('error', 'درخواست ورود با گوگل معتبر یا فعال نیست.');
        }
        if (!empty($_GET['error']) || empty($_GET['code'])) {
            return redirect()->route('login')->with('error', 'ورود با گوگل لغو شد.');
        }

        $clientId = trim((string) \Cyron\Support\Env::get('GOOGLE_CLIENT_ID', ''));
        $clientSecret = trim((string) \Cyron\Support\Env::get('GOOGLE_CLIENT_SECRET', ''));
        if ($clientId === '' || $clientSecret === '') {
            return redirect()->route('login')->with('error', 'تنظیمات ورود با گوگل کامل نیست.');
        }

        try {
            $token = $this->requestJson('https://oauth2.googleapis.com/token', [
                'code' => (string) $_GET['code'],
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $this->redirectUri(),
                'grant_type' => 'authorization_code',
            ]);
            $accessToken = (string) ($token['access_token'] ?? '');
            if ($accessToken === '') throw new \RuntimeException('Google token was not returned.');

            $profile = $this->requestJson('https://openidconnect.googleapis.com/v1/userinfo', [], [
                'Authorization: Bearer ' . $accessToken,
            ]);
            $email = strtolower(trim((string) ($profile['email'] ?? '')));
            if ($email === '' || filter_var($email, FILTER_VALIDATE_EMAIL) === false || ($profile['email_verified'] ?? false) !== true) {
                throw new \RuntimeException('Google email is not verified.');
            }

            $user = User::where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'name' => trim((string) ($profile['name'] ?? $email)),
                    'email' => $email,
                    'password' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
                    'status' => 'active',
                    'email_verified_at' => date('Y-m-d H:i:s'),
                ]);
            }
            if (!$user || ($user->status ?? 'active') !== 'active') throw new \RuntimeException('User is not active.');

            Auth::login($user, true);
            Auth::markLogin($user);
            return redirect()->route('user.dashboard');
        } catch (\Throwable $exception) {
            error_log('Google OAuth failed: ' . $exception->getMessage());
            return redirect()->route('login')->with('error', 'ورود با گوگل انجام نشد. دوباره تلاش کنید.');
        }
    }

    private function redirectUri(): string
    {
        $configured = trim((string) \Cyron\Support\Env::get('GOOGLE_REDIRECT_URI', ''));
        if ($configured !== '') return $configured;
        return rtrim((string) \Cyron\Support\Env::get('APP_URL', ''), '/') . '/auth/google/callback';
    }

    private function requestJson(string $url, array $form = [], array $headers = []): array
    {
        $handle = curl_init($url);
        if ($handle === false) throw new \RuntimeException('HTTP client is unavailable.');
        curl_setopt_array($handle, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_HTTPHEADER => $headers,
        ]);
        if ($form !== []) {
            curl_setopt($handle, CURLOPT_POST, true);
            curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($form));
            curl_setopt($handle, CURLOPT_HTTPHEADER, array_merge(['Content-Type: application/x-www-form-urlencoded'], $headers));
        }
        $body = curl_exec($handle);
        $status = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
        curl_close($handle);
        if (!is_string($body) || $status < 200 || $status >= 300) throw new \RuntimeException('Google HTTP request failed.');
        $data = json_decode($body, true);
        if (!is_array($data)) throw new \RuntimeException('Invalid Google response.');
        return $data;
    }
}