<?php

namespace Cyron\Mail;

use PHPMailer\PHPMailer\PHPMailer;

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

    public function __construct(?array $config = null)
    {
        $this->config = $config ?? (function_exists('config') ? (array) config('mail') : []);
        $this->mailer = new PHPMailer(true);
        $this->setup();
    }

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
        } elseif ($driver === 'sendmail') {
            $this->mailer->isSendmail();
            $this->mailer->Sendmail = $this->config['sendmail']['path'] ?? '/usr/sbin/sendmail -bs';
        } else {
            $this->mailer->isMail();
        }

        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->setLanguage('fa');
        $from = $this->config['from'] ?? [];
        if (!empty($from['address'])) $this->mailer->setFrom($from['address'], $from['name'] ?? '');
    }

    public function to(string $email, string $name = ''): self
    {
        $this->to = $email;
        $this->toName = $name;
        return $this;
    }

    public function subject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function body(string $html): self
    {
        $this->body = $html;
        return $this;
    }

    public function altBody(string $text): self
    {
        $this->altBody = $text;
        return $this;
    }

    public function attach(string $filePath, string $name = ''): self
    {
        $this->attachments[] = ['path' => $filePath, 'name' => $name];
        return $this;
    }

    public function cc(string $email, string $name = ''): self
    {
        $this->cc[] = ['email' => $email, 'name' => $name];
        return $this;
    }

    public function bcc(string $email, string $name = ''): self
    {
        $this->bcc[] = ['email' => $email, 'name' => $name];
        return $this;
    }

    public function send(): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($this->to, $this->toName);
            foreach ($this->cc as $cc) $this->mailer->addCC($cc['email'], $cc['name'] ?? '');
            foreach ($this->bcc as $bcc) $this->mailer->addBCC($bcc['email'], $bcc['name'] ?? '');
            $this->mailer->Subject = $this->subject;
            $this->mailer->isHTML(!empty($this->body));
            $this->mailer->Body = $this->body;
            $this->mailer->AltBody = $this->altBody ?: strip_tags($this->body);
            foreach ($this->attachments as $attachment) $this->mailer->addAttachment($attachment['path'], $attachment['name'] ?? '');
            return $this->mailer->send();
        } catch (\Exception $e) {
            error_log('Mail Error: ' . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public static function quick(string $to, string $subject, string $body, ?string $from = null): bool
    {
        $mailer = new self();
        if ($from) $mailer->mailer->setFrom($from);
        return $mailer->to($to)->subject($subject)->body($body)->send();
    }
}
