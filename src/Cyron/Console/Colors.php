<?php

namespace Cyron\Console;

class Colors
{
    private static $enabled = null;

    public static function enable()
    {
        if (self::$enabled === null) {
            if (DIRECTORY_SEPARATOR === '\\') {
                if (function_exists('sapi_windows_vt100_support')) {
                    self::$enabled = sapi_windows_vt100_support(STDOUT, true);
                } else {
                    self::$enabled = false;
                }
                echo "\033[?1000h\033[?25h";
            } else {
                self::$enabled = true;
            }
        }

        return self::$enabled;
    }

    public static function black($text) { return self::color($text, '30'); }
    public static function red($text) { return self::color($text, '31'); }
    public static function green($text) { return self::color($text, '32'); }
    public static function yellow($text) { return self::color($text, '33'); }
    public static function blue($text) { return self::color($text, '34'); }
    public static function magenta($text) { return self::color($text, '35'); }
    public static function cyan($text) { return self::color($text, '36'); }
    public static function white($text) { return self::color($text, '37'); }

    public static function brightBlack($text) { return self::color($text, '90'); }
    public static function brightRed($text) { return self::color($text, '91'); }
    public static function brightGreen($text) { return self::color($text, '92'); }
    public static function brightYellow($text) { return self::color($text, '93'); }
    public static function brightBlue($text) { return self::color($text, '94'); }
    public static function brightMagenta($text) { return self::color($text, '95'); }
    public static function brightCyan($text) { return self::color($text, '96'); }
    public static function brightWhite($text) { return self::color($text, '97'); }

    public static function dim($text) { return self::color($text, '2'); }

    public static function red50($text) { return self::rgb($text, 254, 242, 242); }
    public static function red100($text) { return self::rgb($text, 254, 226, 226); }
    public static function red200($text) { return self::rgb($text, 254, 202, 202); }
    public static function red300($text) { return self::rgb($text, 252, 165, 165); }
    public static function red400($text) { return self::rgb($text, 248, 113, 113); }
    public static function red500($text) { return self::rgb($text, 239, 68, 68); }
    public static function red600($text) { return self::rgb($text, 220, 38, 38); }
    public static function red700($text) { return self::rgb($text, 185, 28, 28); }
    public static function red800($text) { return self::rgb($text, 153, 27, 27); }
    public static function red900($text) { return self::rgb($text, 127, 29, 29); }

    public static function orange300($text) { return self::rgb($text, 253, 186, 116); }
    public static function gray300($text) { return self::rgb($text, 209, 213, 219); }
    public static function gray500($text) { return self::rgb($text, 107, 114, 128); }
    public static function purple400($text) { return self::rgb($text, 192, 132, 252); }
    public static function pink500($text) { return self::rgb($text, 236, 72, 153); }
    public static function blue400($text) { return self::rgb($text, 96, 165, 250); }

    public static function green400($text) { return self::rgb($text, 74, 222, 128); }
    public static function blue300($text) { return self::rgb($text, 147, 197, 253); }

    public static function error($text) { return self::red($text); }
    public static function success($text) { return self::green($text); }
    public static function info($text) { return self::blue($text); }
    public static function warning($text) { return self::yellow($text); }

    private static function color($text, $code)
    {
        return self::enable() ? "\033[{$code}m{$text}\033[0m" : $text;
    }

    private static function rgb($text, $r, $g, $b)
    {
        if (!self::enable()) {
            return $text;
        }

        return "\033[38;2;{$r};{$g};{$b}m{$text}\033[0m";
    }
}

if (!class_exists('Colors', false)) {
    class_alias(__NAMESPACE__ . '\\Colors', 'Colors');
}