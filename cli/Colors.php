<?php

class Colors {
    private static $enabled = null;
    
    // public static function enable() {
    //     if (self::$enabled === null) {
    //         if (DIRECTORY_SEPARATOR === '\\') {
    //             if (function_exists('sapi_windows_vt100_support')) {
    //                 self::$enabled = sapi_windows_vt100_support(STDOUT, true);
    //             } else {
    //                 self::$enabled = false;
    //             }
    //         } else {
    //             self::$enabled = true;
    //         }
    //     }
    //     return self::$enabled;
    // }
    
    public static function enable() {
        if (self::$enabled === null) {
            if (DIRECTORY_SEPARATOR === '\\') {
                // فعال‌سازی ANSI در ویندوز
                if (function_exists('sapi_windows_vt100_support')) {
                    self::$enabled = sapi_windows_vt100_support(STDOUT, true);
                } else {
                    self::$enabled = false;
                }
                // تنظیم کردن صفحه برای پشتیبانی از رنگ
                echo "\033[?1000h\033[?25h";
            } else {
                self::$enabled = true;
            }
        }
        return self::$enabled;
    }
    
    
    // ========== رنگ‌های پایه (8 رنگ) ==========
    public static function black($text)   { return self::color($text, '30'); }
    public static function red($text)     { return self::color($text, '31'); }
    public static function green($text)   { return self::color($text, '32'); }
    public static function yellow($text)  { return self::color($text, '33'); }
    public static function blue($text)    { return self::color($text, '34'); }
    public static function magenta($text) { return self::color($text, '35'); }
    public static function cyan($text)    { return self::color($text, '36'); }
    public static function white($text)   { return self::color($text, '37'); }
    
    // ========== رنگ‌های پررنگ (8 رنگ) ==========
    public static function brightBlack($text)   { return self::color($text, '90'); }
    public static function brightRed($text)     { return self::color($text, '91'); }
    public static function brightGreen($text)   { return self::color($text, '92'); }
    public static function brightYellow($text)  { return self::color($text, '93'); }
    public static function brightBlue($text)    { return self::color($text, '94'); }
    public static function brightMagenta($text) { return self::color($text, '95'); }
    public static function brightCyan($text)    { return self::color($text, '96'); }
    public static function brightWhite($text)   { return self::color($text, '97'); }
    
    // ========== طیف قرمز (Red) مثل Tailwind ==========
    public static function red50($text)   { return self::rgb($text, 254, 242, 242); }   // #FEF2F2
    public static function red100($text)  { return self::rgb($text, 254, 226, 226); }   // #FEE2E2
    public static function red200($text)  { return self::rgb($text, 254, 202, 202); }   // #FECACA
    public static function red300($text)  { return self::rgb($text, 252, 165, 165); }   // #FCA5A5
    public static function red400($text)  { return self::rgb($text, 248, 113, 113); }   // #F87171
    public static function red500($text)  { return self::rgb($text, 239, 68, 68); }     // #EF4444
    public static function red600($text)  { return self::rgb($text, 220, 38, 38); }     // #DC2626
    public static function red700($text)  { return self::rgb($text, 185, 28, 28); }     // #B91C1C
    public static function red800($text)  { return self::rgb($text, 153, 27, 27); }     // #991B1B
    public static function red900($text)  { return self::rgb($text, 127, 29, 29); }     // #7F1D1D
    
    // ========== طیف نارنجی (Orange) ==========
    public static function orange50($text)   { return self::rgb($text, 255, 247, 237); }  // #FFF7ED
    public static function orange100($text)  { return self::rgb($text, 255, 237, 213); }  // #FFEDD5
    public static function orange200($text)  { return self::rgb($text, 254, 215, 170); }  // #FED7AA
    public static function orange300($text)  { return self::rgb($text, 253, 186, 116); }  // #FDBA74
    public static function orange400($text)  { return self::rgb($text, 251, 146, 60); }   // #FB923C
    public static function orange500($text)  { return self::rgb($text, 249, 115, 22); }   // #F97316
    public static function orange600($text)  { return self::rgb($text, 234, 88, 12); }    // #EA580C
    public static function orange700($text)  { return self::rgb($text, 194, 65, 12); }    // #C2410C
    public static function orange800($text)  { return self::rgb($text, 154, 52, 18); }    // #9A3412
    public static function orange900($text)  { return self::rgb($text, 124, 45, 18); }    // #7C2D12
    
    // ========== طیف زرد (Yellow) ==========
    public static function yellow50($text)   { return self::rgb($text, 254, 252, 232); }  // #FEFCE8
    public static function yellow100($text)  { return self::rgb($text, 254, 249, 195); }  // #FEF9C3
    public static function yellow200($text)  { return self::rgb($text, 254, 240, 138); }  // #FEF08A
    public static function yellow300($text)  { return self::rgb($text, 253, 224, 71); }   // #FDE047
    public static function yellow400($text)  { return self::rgb($text, 250, 204, 21); }   // #FACC15
    public static function yellow500($text)  { return self::rgb($text, 234, 179, 8); }    // #EAB308
    public static function yellow600($text)  { return self::rgb($text, 202, 138, 4); }    // #CA8A04
    public static function yellow700($text)  { return self::rgb($text, 161, 98, 7); }     // #A16207
    public static function yellow800($text)  { return self::rgb($text, 133, 77, 14); }    // #854D0E
    public static function yellow900($text)  { return self::rgb($text, 113, 63, 18); }    // #713F12
    
    // ========== طیف سبز (Green) ==========
    public static function green50($text)   { return self::rgb($text, 240, 253, 244); }   // #F0FDF4
    public static function green100($text)  { return self::rgb($text, 220, 252, 231); }   // #DCFCE7
    public static function green200($text)  { return self::rgb($text, 187, 247, 208); }   // #BBF7D0
    public static function green300($text)  { return self::rgb($text, 134, 239, 172); }   // #86EFAC
    public static function green400($text)  { return self::rgb($text, 74, 222, 128); }    // #4ADE80
    public static function green500($text)  { return self::rgb($text, 34, 197, 94); }     // #22C55E
    public static function green600($text)  { return self::rgb($text, 22, 163, 74); }     // #16A34A
    public static function green700($text)  { return self::rgb($text, 21, 128, 61); }     // #15803D
    public static function green800($text)  { return self::rgb($text, 22, 101, 52); }     // #166534
    public static function green900($text)  { return self::rgb($text, 20, 83, 45); }      // #14532D
    
    // ========== طیف آبی (Blue) ==========
    public static function blue50($text)   { return self::rgb($text, 239, 246, 255); }    // #EFF6FF
    public static function blue100($text)  { return self::rgb($text, 219, 234, 254); }    // #DBEAFE
    public static function blue200($text)  { return self::rgb($text, 191, 219, 254); }    // #BFDBFE
    public static function blue300($text)  { return self::rgb($text, 147, 197, 253); }    // #93C5FD
    public static function blue400($text)  { return self::rgb($text, 96, 165, 250); }     // #60A5FA
    public static function blue500($text)  { return self::rgb($text, 59, 130, 246); }     // #3B82F6
    public static function blue600($text)  { return self::rgb($text, 37, 99, 235); }      // #2563EB
    public static function blue700($text)  { return self::rgb($text, 29, 78, 216); }      // #1D4ED8
    public static function blue800($text)  { return self::rgb($text, 30, 64, 175); }      // #1E40AF
    public static function blue900($text)  { return self::rgb($text, 30, 58, 138); }      // #1E3A8A
    
    // ========== طیف بنفش (Purple) ==========
    public static function purple50($text)   { return self::rgb($text, 250, 245, 255); }  // #FAF5FF
    public static function purple100($text)  { return self::rgb($text, 243, 232, 255); }  // #F3E8FF
    public static function purple200($text)  { return self::rgb($text, 233, 213, 255); }  // #E9D5FF
    public static function purple300($text)  { return self::rgb($text, 216, 180, 254); }  // #D8B4FE
    public static function purple400($text)  { return self::rgb($text, 192, 132, 252); }  // #C084FC
    public static function purple500($text)  { return self::rgb($text, 168, 85, 247); }   // #A855F7
    public static function purple600($text)  { return self::rgb($text, 147, 51, 234); }   // #9333EA
    public static function purple700($text)  { return self::rgb($text, 126, 34, 206); }   // #7E22CE
    public static function purple800($text)  { return self::rgb($text, 107, 33, 168); }   // #6B21A8
    public static function purple900($text)  { return self::rgb($text, 88, 28, 135); }    // #581C87
    
    // ========== طیف صورتی (Pink) ==========
    public static function pink50($text)   { return self::rgb($text, 253, 242, 248); }    // #FDF2F8
    public static function pink100($text)  { return self::rgb($text, 252, 231, 243); }    // #FCE7F3
    public static function pink200($text)  { return self::rgb($text, 251, 207, 232); }    // #FBCFE8
    public static function pink300($text)  { return self::rgb($text, 249, 168, 212); }    // #F9A8D4
    public static function pink400($text)  { return self::rgb($text, 244, 114, 182); }    // #F472B6
    public static function pink500($text)  { return self::rgb($text, 236, 72, 153); }     // #EC4899
    public static function pink600($text)  { return self::rgb($text, 219, 39, 119); }     // #DB2777
    public static function pink700($text)  { return self::rgb($text, 190, 24, 93); }      // #BE185D
    public static function pink800($text)  { return self::rgb($text, 157, 23, 77); }      // #9D174D
    public static function pink900($text)  { return self::rgb($text, 131, 24, 67); }      // #831843
    
    // ========== طیف خاکستری (Gray) ==========
    public static function gray50($text)   { return self::rgb($text, 249, 250, 251); }    // #F9FAFB
    public static function gray100($text)  { return self::rgb($text, 243, 244, 246); }    // #F3F4F6
    public static function gray200($text)  { return self::rgb($text, 229, 231, 235); }    // #E5E7EB
    public static function gray300($text)  { return self::rgb($text, 209, 213, 219); }    // #D1D5DB
    public static function gray400($text)  { return self::rgb($text, 156, 163, 175); }    // #9CA3AF
    public static function gray500($text)  { return self::rgb($text, 107, 114, 128); }    // #6B7280
    public static function gray600($text)  { return self::rgb($text, 75, 85, 99); }       // #4B5563
    public static function gray700($text)  { return self::rgb($text, 55, 65, 81); }       // #374151
    public static function gray800($text)  { return self::rgb($text, 31, 41, 55); }       // #1F2937
    public static function gray900($text)  { return self::rgb($text, 17, 24, 39); }       // #111827
    
    // ========== طیف سفارشی (Custom) ==========
    public static function primary($text)   { return self::rgb($text, 255, 74, 63); }     // رنگ اصلی #FF4A3F
    public static function primaryDark($text) { return self::rgb($text, 237, 42, 30); }   // #ED2A1E
    public static function cyron($text, $shade = 500) {
        $cyronColors = [
            50  => [255, 241, 240],
            100 => [255, 224, 222],
            200 => [255, 199, 196],
            300 => [255, 160, 155],
            400 => [255, 110, 102],
            500 => [255, 74, 63],
            600 => [237, 42, 30],
            700 => [200, 30, 20],
            800 => [165, 27, 18],
            900 => [136, 29, 22],
        ];
        $rgb = $cyronColors[$shade] ?? $cyronColors[500];
        return self::rgb($text, $rgb[0], $rgb[1], $rgb[2]);
    }
    
    // ========== متدهای اصلی ==========
    private static function color($text, $code) {
        return self::enable() ? "\033[{$code}m{$text}\033[0m" : $text;
    }
    
    private static function rgb($text, $r, $g, $b) {
        if (!self::enable()) return $text;
        // true color (24-bit) پشتیبانی می‌شه
        return "\033[38;2;{$r};{$g};{$b}m{$text}\033[0m";
    }
    
    private static function bgRgb($text, $r, $g, $b) {
        if (!self::enable()) return $text;
        return "\033[48;2;{$r};{$g};{$b}m{$text}\033[0m";
    }
    
    // پس‌زمینه با RGB (true color)
    public static function bgRgbRed500($text) { return self::bgRgb($text, 239, 68, 68); }
    public static function bgRgbBlue500($text) { return self::bgRgb($text, 59, 130, 246); }
    
    // ========== گرادینت با 256 رنگ ==========
    
    // گرادینت از رنگ A به رنگ B
    public static function gradient($text, $from, $to) {
        $len = strlen($text);
        if ($len == 0) return '';
        
        $result = '';
        $fromRGB = self::getColorRGB($from);
        $toRGB = self::getColorRGB($to);
        
        for ($i = 0; $i < $len; $i++) {
            $ratio = $i / $len;
            
            // محاسبه رنگ میانی
            $r = $fromRGB['r'] + ($toRGB['r'] - $fromRGB['r']) * $ratio;
            $g = $fromRGB['g'] + ($toRGB['g'] - $fromRGB['g']) * $ratio;
            $b = $fromRGB['b'] + ($toRGB['b'] - $fromRGB['b']) * $ratio;
            
            // تبدیل به کد 256 رنگ
            $colorCode = self::rgbTo256($r, $g, $b);
            $result .= self::color256($text[$i], $colorCode);
        }
        
        return $result;
    }
    
    // گرادینت رنگین‌کمانی
    public static function rainbow($text) {
        $len = strlen($text);
        $result = '';
        
        $rainbowColors = [
            [255, 0, 0],     // قرمز
            [255, 165, 0],   // نارنجی
            [255, 255, 0],   // زرد
            [0, 255, 0],     // سبز
            [0, 255, 255],   // فیروزه‌ای
            [0, 0, 255],     // آبی
            [128, 0, 128],   // بنفش
            [255, 0, 255],   // صورتی
        ];
        
        for ($i = 0; $i < $len; $i++) {
            $colorIndex = floor(($i / $len) * count($rainbowColors));
            $rgb = $rainbowColors[$colorIndex % count($rainbowColors)];
            $colorCode = self::rgbTo256($rgb[0], $rgb[1], $rgb[2]);
            $result .= self::color256($text[$i], $colorCode);
        }
        
        return $result;
    }
    
    // رنگ 256
    public static function color256($text, $code) {
        return self::enable() ? "\033[38;5;{$code}m{$text}\033[0m" : $text;
    }
    
    // تبدیل RGB به رنگ 256
    private static function rgbTo256($r, $g, $b) {
        // نرمال‌سازی به 0-5
        $r = round($r / 51);
        $g = round($g / 51);
        $b = round($b / 51);
        
        // فرمول رنگ 256: 16 + 36*r + 6*g + b
        return 16 + (36 * $r) + (6 * $g) + $b;
    }
    
    // دریافت RGB معروف‌ترین رنگ‌ها
    private static function getColorRGB($colorName) {
        $colors = [
            'black'   => [0, 0, 0],
            'red'     => [255, 0, 0],
            'green'   => [0, 255, 0],
            'yellow'  => [255, 255, 0],
            'blue'    => [0, 0, 255],
            'magenta' => [255, 0, 255],
            'cyan'    => [0, 255, 255],
            'white'   => [255, 255, 255],
            'orange'  => [255, 165, 0],
            'purple'  => [128, 0, 128],
            'pink'    => [255, 192, 203],
        ];
        
        if (isset($colors[$colorName])) {
            return ['r' => $colors[$colorName][0], 'g' => $colors[$colorName][1], 'b' => $colors[$colorName][2]];
        }
        
        return ['r' => 255, 'g' => 255, 'b' => 255];
    }
    
    // استایل‌ها
    public static function bold($text)   { return self::style($text, '1'); }
    public static function dim($text)    { return self::style($text, '2'); }
    public static function underline($text) { return self::style($text, '4'); }
    public static function blink($text)  { return self::style($text, '5'); }
    
    
    private static function style($text, $code) {
        return self::enable() ? "\033[{$code}m{$text}\033[0m" : $text;
    }
    
    // پس‌زمینه
    public static function bgRed($text)   { return self::color($text, '41'); }
    public static function bgGreen($text) { return self::color($text, '42'); }
    public static function bgYellow($text){ return self::color($text, '43'); }
    public static function bgBlue($text)  { return self::color($text, '44'); }
    
    // پیام‌های آماده
    public static function success($text) { return self::green("✓ {$text}"); }
    public static function error($text)   { return self::red("✗ {$text}"); }
    public static function warning($text) { return self::yellow("⚠ {$text}"); }
    public static function info($text)    { return self::cyan("ℹ {$text}"); }
}