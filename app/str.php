<?php

namespace App;

class Str {
    private static $transliterations = array(
        'ا' => 'a',
        'ب' => 'b',
        'پ' => 'p',
        'ت' => 't',
        'ث' => 's',
        'ج' => 'j',
        'چ' => 'ch',
        'ح' => 'h',
        'خ' => 'kh',
        'د' => 'd',
        'ذ' => 'z',
        'ر' => 'r',
        'ز' => 'z',
        'ژ' => 'zh',
        'س' => 's',
        'ش' => 'sh',
        'ص' => 's',
        'ض' => 'z',
        'ط' => 't',
        'ظ' => 'z',
        'ع' => 'a',
        'غ' => 'gh',
        'ف' => 'f',
        'ق' => 'q',
        'ک' => 'k',
        'گ' => 'g',
        'ل' => 'l',
        'م' => 'm',
        'ن' => 'n',
        'و' => 'v',
        'ه' => 'h',
        'ی' => 'y',
    );

    public static function slug($string) {
        // Keep Persian characters and convert to lowercase
        $string = preg_replace('/[^ا-یA-Za-z0-9-]/u', '-', $string);
        $string = trim($string, '-');
        $string = strtolower($string);
    
        // Transliterate Persian characters to English
        $string = self::transliterate($string);
    
        return $string;
    }

    private static function transliterate($string) {
        foreach (self::$transliterations as $persian => $english) {
            $string = str_replace($persian, $english, $string);
        }
        return $string;
    }
}

