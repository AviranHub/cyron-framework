<?php

namespace App;

class View
{
    protected $layout = null;
    protected $data = [];
    public $sections = []; // برای ذخیره بخش‌ها

    public function extend($layout = 'app')
    {
        $get_layout = explode('.', $layout);
        $layout = implode('/', $get_layout);
        $this->layout = $layout;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    // public function section($name, $content = null)
    // {
    //     // echo $content ?? 'null';

    //     if ($content === null) {
    //         return $this->sections[$name] ?? null; // برگرداندن محتوای بخش
    //     }

    //     $this->sections[$name] = $content; // ذخیره محتوای بخش
    // }

    public function section($name, $content = null)
    {
        if ($content === null) {
            return $this->sections[$name] ?? null;
        }

        // آرایه برای ذخیره کدهای PHP
        $phpCodes = [];
        $shortPhpCodes = [];

        // تابع callback برای شناسایی و ذخیره کد PHP
        $content = preg_replace_callback('/<\?php(.*?)\?>/s', function ($matches) use (&$phpCodes) {
            // ذخیره کد PHP در آرایه
            $phpCodes[] = trim($matches[1]);
            // جایگزینی با یک placeholder
            return '{{php_placeholder_' . (count($phpCodes) - 1) . '}}';
        }, $content);

        // تابع callback برای شناسایی و ذخیره کد کوتاه PHP
        $content = preg_replace_callback('/<\?=(.*?)\?>/s', function ($matches) use (&$shortPhpCodes) {
            // ذخیره کد کوتاه PHP در آرایه
            $shortPhpCodes[] = trim($matches[1]);
            // جایگزینی با یک placeholder
            return '{{short_php_placeholder_' . (count($shortPhpCodes) - 1) . '}}';
        }, $content);

        // ذخیره کدهای PHP و کدهای کوتاه در بخش
        $this->sections[$name] = [
            'content' => $content,
            'phpCodes' => $phpCodes,
            'shortPhpCodes' => $shortPhpCodes
        ];
    }


    public function render($template)
    {
        // ... کد قبلی
        
        if ($this->data) {
            extract($this->data);
        }
        // echo json_encode($this->sections);
        // بارگذاری layout
        if ($this->layout) {
            // شروع بافر خروجی برای لایه
            ob_start();
            include_once './resources/app/' . $this->layout . '.lady.php'; // بارگذاری فایل لایه
            $layoutContent = ob_get_clean(); // دریافت محتوای لایه
            foreach ($this->sections as $name => $content) {
                if (isset($this->sections[$name])) {
                    $content = $this->sections[$name]['content'];
                    $phpCodes = $this->sections[$name]['phpCodes'];
                    $shortPhpCodes = $this->sections[$name]['shortPhpCodes'];

                    // اجرای کدهای PHP
                    foreach ($phpCodes as $index => $phpCode) {
                        ob_start();
                        eval('?>' . $phpCode); // اجرای کد PHP
                        $output = ob_get_clean();
                        $content = str_replace("{{php_placeholder_$index}}", $output, $content); // جایگزینی خروجی
                    }

                    // اجرای کدهای کوتاه PHP
                    foreach ($shortPhpCodes as $index => $shortPhpCode) {
                        ob_start();
                        eval('?>' . $shortPhpCode); // اجرای کد کوتاه PHP
                        $output = ob_get_clean();
                        $content = str_replace("{{short_php_placeholder_$index}}", $output, $content); // جایگزینی خروجی
                    }

                    echo $content; // نمایش محتوای نهایی
                }
            }
            $layoutContent = preg_replace('/{{\s*(.+?)\s*}}/', '', $layoutContent);

            // جایگزینی محتوای بخش‌ها در لایه
            echo $layoutContent; // نمایش محتوای نهایی
        } else {
            // شروع بافر خروجی
            ob_start();
            // بارگذاری تمپلیت
            include_once './resources/app/Views/' . $template . '.php'; // بارگذاری فایل ویو
            $content = ob_get_clean(); // دریافت محتوای بارگذاری شده

            echo $content; // اگر لایه وجود نداشت، فقط محتوای ویو را نمایش دهید
        }
    }


    // public function render($template)
    // {
    //     // استخراج داده‌ها برای استفاده در لایه
    //     if($this->data){
    //         extract($this->data);
    //     }
    //     // echo json_encode($this->sections);
    //     // بارگذاری layout
    //     if ($this->layout) {
    //         // شروع بافر خروجی برای لایه
    //         ob_start();
    //         include_once './resources/app/' . $this->layout . '.lady.php'; // بارگذاری فایل لایه
    //         $layoutContent = ob_get_clean(); // دریافت محتوای لایه
    //         foreach ($this->sections as $name => $content) {
    //             $layoutContent = str_replace("{{ $name }}", $content, $layoutContent);
    //         }
    //         $layoutContent = preg_replace('/{{\s*(.+?)\s*}}/', '', $layoutContent);

    //         // جایگزینی محتوای بخش‌ها در لایه
    //         echo $layoutContent; // نمایش محتوای نهایی
    //     } else {
    //         // شروع بافر خروجی
    //         ob_start();
    //         // بارگذاری تمپلیت
    //         include_once './resources/app/Views/' . $template . '.php'; // بارگذاری فایل ویو
    //         $content = ob_get_clean(); // دریافت محتوای بارگذاری شده

    //         echo $content; // اگر لایه وجود نداشت، فقط محتوای ویو را نمایش دهید
    //     }
    // }
    // app/views/View.php

    public function yield($name)
    {
        return $this->sections[$name] ?? ''; // برگرداندن محتوای بخش یا رشته خالی
    }
}
