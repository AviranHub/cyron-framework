<?php
// app/Actions/BaseAction.php

namespace App\Actions;

use App\Response;
use App\Core\Authentication\Auth;
use App\Request;
use App\Http\ErrorBag;

abstract class BaseAction
{
    /**
     * اجرای اکشن
     */
    abstract public function execute(array $data = []);

    /**
     * دسترسی به کاربر جاری
     */
    protected function user()
    {
        return Auth::user();
    }

    /**
     * بررسی احراز هویت
     */
    protected function check()
    {
        return Auth::check();
    }

    /**
     * پاسخ موفقیت
     */
    protected function success($data = null, $message = 'عملیات موفق')
    {
        return Response::success($data, $message);
    }

    /**
     * پاسخ خطا
     */
    protected function error($message = 'خطا', $code = 400)
    {
        return Response::error($message, $code);
    }

    /**
     * پاسخ خطای احراز هویت
     */
    protected function unauthorized($message = 'لطفاً وارد شوید')
    {
        return Response::unauthorized($message);
    }

    /**
     * پاسخ خطای اعتبارسنجی
     */
    protected function validationError($errors)
    {
        if ($errors instanceof ErrorBag) {
            return Response::validationError($errors->all());
        }
        return Response::validationError($errors);
    }

    /**
     * اعتبارسنجی داده‌ها (با استفاده از Validator موجود)
     */
    protected function validate($data, $rules, $messages = [])
    {
        $validator = new \App\Core\Validation\Validator($data, $rules, $messages);
        $validator->validate();
        if ($validator->fails()) {
            return $validator->errors();
        }
        return null;
    }

    /**
     * دریافت مقدار از داده‌ها با پشتیبانی از نقطه‌دار
     */
    protected function get($data, $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = $data;
        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}