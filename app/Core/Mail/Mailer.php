<?php
// app/Core/Mail/Mailer.php

namespace App\Core\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    protected PHPMailer $mailer;
    protected array $config;
    protected string $to = '';
    protected string $toName = '';
    protected string $subject = '';
    protected string $body = '';
    protected string $altBody = '';
    protected array $attachments = [];
    protected array $cc = [];
    protected array $bcc = [];

    public function __construct()
    {
        $this->config = config('mail');
        $this->mailer = new PHPMailer(true);
        $this->setup();
    }

    /**
     * تنظیمات اولیه PHPMailer
     */
    protected function setup(): void
    {
        $driver = $this->config['default'] ?? 'smtp';

        if ($driver === 'smtp') {
            $this->mailer->isSMTP();
            $this->mailer->Host = $this->config['smtp']['host'] ?? '';
            $this->mailer->Port = $this->config['smtp']['port'] ?? 587;
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $this->config['smtp']['username'] ?? '';
            $this->mailer->Password = $this->config['smtp']['password'] ?? '';
            $this->mailer->SMTPSecure = $this->config['smtp']['encryption'] ?? 'tls';
            $this->mailer->Timeout = $this->config['smtp']['timeout'] ?? 30;
            
            // دیباگ (برای محیط توسعه)
            if (env('APP_ENV') === 'development') {
                // $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
            }
        } elseif ($driver === 'sendmail') {
            $this->mailer->isSendmail();
            $this->mailer->Sendmail = $this->config['sendmail']['path'] ?? '/usr/sbin/sendmail -bs';
        } else {
            $this->mailer->isMail();
        }

        // تنظیمات زبان و کاراکتر
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setLanguage('fa');

        // تنظیم فرستنده پیش‌فرض
        $from = $this->config['from'] ?? [];
        if (!empty($from['address'])) {
            $this->mailer->setFrom($from['address'], $from['name'] ?? '');
        }
    }

    /**
     * تنظیم گیرنده
     */
    public function to(string $email, string $name = ''): self
    {
        $this->to = $email;
        $this->toName = $name;
        return $this;
    }

    /**
     * تنظیم موضوع
     */
    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * تنظیم بدنه (HTML)
     */
    public function body(string $html): self
    {
        $this->body = $html;
        return $this;
    }

    /**
     * تنظیم بدنه‌ی متنی (برای ایمیل‌کلاینت‌های قدیمی)
     */
    public function altBody(string $text): self
    {
        $this->altBody = $text;
        return $this;
    }

    /**
     * اضافه کردن فایل پیوست
     */
    public function attach(string $filePath, string $name = ''): self
    {
        $this->attachments[] = ['path' => $filePath, 'name' => $name];
        return $this;
    }

    /**
     * اضافه کردن CC
     */
    public function cc(string $email, string $name = ''): self
    {
        $this->cc[] = ['email' => $email, 'name' => $name];
        return $this;
    }

    /**
     * اضافه کردن BCC
     */
    public function bcc(string $email, string $name = ''): self
    {
        $this->bcc[] = ['email' => $email, 'name' => $name];
        return $this;
    }

    /**
     * ارسال ایمیل
     */
    public function send(): bool
    {
        try {
            // تنظیم گیرنده
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->to, $this->toName);

            // تنظیم CC
            foreach ($this->cc as $cc) {
                $this->mailer->addCC($cc['email'], $cc['name'] ?? '');
            }

            // تنظیم BCC
            foreach ($this->bcc as $bcc) {
                $this->mailer->addBCC($bcc['email'], $bcc['name'] ?? '');
            }

            // تنظیم موضوع و بدنه
            $this->mailer->Subject = $this->subject;
            $this->mailer->isHTML(!empty($this->body));
            $this->mailer->Body = $this->body;
            $this->mailer->AltBody = $this->altBody ?: strip_tags($this->body);

            // تنظیم فایل‌های پیوست
            foreach ($this->attachments as $attachment) {
                $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
            }

            // ارسال
            return $this->mailer->send();
        } catch (\Exception $e) {
            // لاگ خطا
            error_log("Mail Error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * متد کمکی برای ارسال سریع یک ایمیل ساده
     */
    public static function quick(string $to, string $subject, string $body, string $from = null): bool
    {
        $mailer = new self();
        if ($from) {
            $mailer->mailer->setFrom($from);
        }
        return $mailer->to($to)
            ->subject($subject)
            ->body($body)
            ->send();
    }
}