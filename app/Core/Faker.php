<?php

namespace App\Core;

class Faker
{
    protected static $names = ['علی', 'رضا', 'مهدی', 'سارا', 'نرگس', 'مریم'];
    protected static $adjectives = ['عالی', 'جذاب', 'جالب', 'خواندنی', 'علمی', 'تاریخی'];
    protected static $nouns = ['کتاب', 'مجله', 'مقاله', 'رمان', 'داستان'];

    public static function name()
    {
        return self::$names[array_rand(self::$names)] . ' ' . self::lastName();
    }
    public static function lastName()
    {
        return ['احمدی', 'محمدی', 'کریمی', 'رضایی'][array_rand(['احمدی', 'محمدی', 'کریمی', 'رضایی'])];
    }
    public static function email()
    {
        return strtolower(self::name()) . rand(1, 999) . '@example.com';
    }
    public static function phone()
    {
        return '09' . rand(100000000, 999999999);
    }
    public static function number($min = 0, $max = 100)
    {
        return rand($min, $max);
    }
    public static function word()
    {
        return self::$adjectives[array_rand(self::$adjectives)] . ' ' . self::$nouns[array_rand(self::$nouns)];
    }
    public static function sentence($words = 5)
    {
        $s = '';
        for ($i = 0; $i < $words; $i++) $s .= self::word() . ' ';
        return trim($s) . '.';
    }
    public static function slug($str)
    {
        return str_replace(' ', '-', strtolower($str));
    }
    public static function imageUrl($width = 200, $height = 200)
    {
        return "https://picsum.photos/{$width}/{$height}?random=" . rand(1, 9999);
    }
    public static function boolean()
    {
        return (bool)rand(0, 1);
    }
    public static function date($format = 'Y-m-d')
    {
        return date($format, rand(strtotime('-1 year'), time()));
    }

    public static function typeValue($type, $columnName = null)
    {
        $type = strtolower($type);

        // تاریخ و زمان
        if (strpos($type, 'timestamp') !== false || strpos($type, 'datetime') !== false) {
            return date('Y-m-d H:i:s', rand(strtotime('-1 year'), time()));
        }
        // عدد صحیح
        if (strpos($type, 'int') !== false) {
            return rand(0, 1000);
        }
        // اعشاری
        if (strpos($type, 'decimal') !== false || strpos($type, 'float') !== false) {
            return rand(0, 10000) / 100;
        }
        // بولی
        if (strpos($type, 'tinyint') !== false && $type == 'tinyint(1)') {
            return rand(0, 1);
        }
        // متن بلند
        if (strpos($type, 'text') !== false || strpos($type, 'longtext') !== false) {
            return self::sentence(rand(10, 30));
        }
        // enum
        if (strpos($type, 'enum') !== false) {
            preg_match("/enum\('(.+?)'\)/", $type, $matches);
            $options = explode("','", $matches[1]);
            return $options[array_rand($options)];
        }
        // varchar یا string
        if (strpos($type, 'varchar') !== false) {
            // فیلدهای خاص مثل email, phone, name
            if ($columnName == 'email') return self::email();
            if ($columnName == 'phone') return self::phone();
            if ($columnName == 'name') return self::name();
            if ($columnName == 'slug') return self::slug(self::word());
            if ($columnName == 'password') return password_hash('123456', PASSWORD_DEFAULT);
            if ($columnName == 'remember_token') return bin2hex(random_bytes(16));
            return self::word();
        }

        // پیش‌فرض: یک رشته تصادفی
        return self::word();
    }
}
