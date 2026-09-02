<?php

namespace App\Http;

class Controller {
    protected $middleware = [];

    // متد برای اضافه کردن میدلور
    public function middleware($middleware, $options = []) {
        // اگر میدلور به صورت آرایه باشد، آن را به لیست میدلورها اضافه می‌کنیم
        if (is_array($middleware)) {
            foreach ($middleware as $m) {
                $this->middleware[] = $m;
            }
        } else {
            // در غیر این صورت، میدلور را به لیست اضافه می‌کنیم
            $this->middleware[] = $middleware;
        }

        // می‌توانید از $options برای تنظیمات اضافی استفاده کنید
        // به عنوان مثال، می‌توانید اولویت یا شرایط خاصی را تعیین کنید
    }

    // متد برای اجرای میدلورها
    public function handleMiddleware() {
        foreach ($this->middleware as $m) {
            // فرض می‌کنیم که میدلور یک متد handle دارد
            $middlewareInstance = new $m();
            $response = $middlewareInstance->handle();

            // اگر پاسخ میدلور چیزی غیر از null باشد، آن را برمی‌گردانیم
            if ($response !== null) {
                return $response;
            }
        }

        // اگر هیچ میدلوری پاسخی نداد، ادامه می‌دهیم
        return null;
    }
}