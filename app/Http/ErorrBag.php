<?php

namespace App\Http;

class ErrorBag
{
    protected $errors = [];
    protected $globalErrors = [];

    public function add($field, $message)
    {
        $this->errors[$field][] = $message;
    }

    public function addArray($errs)
    {
        foreach ($errs as $err => $msg) {
            $this->errors[$err][] = $msg;
        }
    }

    public function addGlobal($message)
    {
        $this->globalErrors[] = $message;
    }

    /**
     * آیا خطایی وجود دارد؟
     */
    public function any()
    {
        return !empty($this->errors) || !empty($this->globalErrors);
    }

    /**
     * بازگرداندن تمام پیام‌های خطا به صورت یک آرایه ساده (برای نمایش در @errors)
     */
    public function all()
    {
        $messages = [];
        foreach ($this->errors as $fieldErrors) {
            $messages = array_merge($messages, $fieldErrors);
        }
        return array_merge($messages, $this->globalErrors);
    }

    /**
     * بازگرداندن خطاهای فیلددار برای استفاده در کنار هر فیلد
     */
    public function fieldErrors()
    {
        return $this->errors;
    }

    public function globalErrors()
    {
        return $this->globalErrors;
    }

    public function has($field)
    {
        return isset($this->errors[$field]);
    }

    public function get($field)
    {
        return $this->errors[$field] ?? [];
    }

    public function getGlobal()
    {
        return $this->globalErrors;
    }
}