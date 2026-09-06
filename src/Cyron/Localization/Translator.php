<?php

namespace Cyron\Localization;

class Translator
{
    protected static string $locale = 'fa';
    protected static array $translations = [];
    protected static string $langPath = '';

    public static function init(?string $langPath = null): void
    {
        self::$langPath = $langPath ?: (defined('RESOURCES_PATH') ? RESOURCES_PATH . '/Lang' : dirname(__DIR__, 3) . '/resources/Lang');

        if (isset($_SESSION['locale'])) {
            self::$locale = $_SESSION['locale'];
        } elseif (isset($_COOKIE['locale'])) {
            self::$locale = $_COOKIE['locale'];
        } else {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'fa', 0, 2);
            self::$locale = in_array($browserLang, ['fa', 'en']) ? $browserLang : 'fa';
        }

        self::loadTranslations();
    }

    public static function loadTranslations(): void
    {
        $file = self::$langPath . '/' . self::$locale . '/messages.php';
        self::$translations = file_exists($file) ? require $file : [];
    }

    public static function get(string $key, array $replace = [], ?string $locale = null): string
    {
        $locale = $locale ?: self::$locale;

        if (empty(self::$translations)) {
            self::loadTranslations();
        }

        $text = self::$translations[$key] ?? $key;

        foreach ($replace as $search => $replaceText) {
            $text = str_replace(':' . $search, $replaceText, $text);
        }

        return $text;
    }

    public static function setLocale(string $locale): void
    {
        if (in_array($locale, ['fa', 'en'])) {
            self::$locale = $locale;
            $_SESSION['locale'] = $locale;
            setcookie('locale', $locale, time() + (86400 * 30), '/');
            self::loadTranslations();
        }
    }

    public static function getLocale(): string
    {
        return self::$locale;
    }

    public static function isRtl(): bool
    {
        return self::$locale === 'fa';
    }
}
