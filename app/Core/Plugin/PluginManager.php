<?php

namespace App\Core\Plugin;

use App\Core\Exceptions\PluginDependencyException;

class PluginManager
{
    protected static array $plugins = [];
    protected static array $activePlugins = [];
    protected static string $pluginsPath = '';

    public static function setPath(string $path): void
    {
        self::$pluginsPath = rtrim($path, '/\\');
    }

    public static function discover(): void
    {
        self::$plugins = [];
        if (!is_dir(self::$pluginsPath)) return;
        foreach (glob(self::$pluginsPath . '/*', GLOB_ONLYDIR) ?: [] as $dir) {
            $manifest = $dir . '/plugin.json';
            if (!is_file($manifest) || !is_readable($manifest)) continue;
            $info = json_decode(file_get_contents($manifest), true);
            if (!is_array($info) || !self::validName($info['name'] ?? '') || !preg_match('/^\d+\.\d+\.\d+(?:[-+][A-Za-z0-9.-]+)?$/', (string)($info['version'] ?? ''))) continue;
            $info['dependencies'] = is_array($info['dependencies'] ?? []) ? $info['dependencies'] : [];
            foreach ($info['dependencies'] as $dep => $constraint) {
                if (!self::validName((string)$dep) || !is_string($constraint) || !self::validConstraint($constraint)) {
                    continue 2;
                }
            }
            $info['path'] = $dir;
            self::$plugins[$info['name']] = $info;
        }
    }

    public static function activate(string $name, bool $autoEnableDeps = true): bool
    {
        if (!self::validName($name) || !isset(self::$plugins[$name])) throw new \InvalidArgumentException('Invalid or unknown plugin.');
        if (self::isActive($name)) return true;
        $info = self::$plugins[$name];

        if ($autoEnableDeps) {
            foreach (self::resolveDependencies($name) as $depName) {
                if ($depName !== $name && !self::isActive($depName)) self::activate($depName, true);
            }
        }
        foreach ($info['dependencies'] as $depName => $constraint) {
            if (!self::isActive($depName) || !self::checkVersion($depName, $constraint)) {
                throw new PluginDependencyException("Plugin dependency requirement not satisfied.");
            }
        }

        $pluginClass = self::getPluginClass($name);
        if ($pluginClass) {
            $plugin = new $pluginClass();
            if (method_exists($plugin, 'boot')) $plugin->boot();
        }
        self::$activePlugins[$name] = $info;
        $routeFile = $info['path'] . '/routes.php';
        if (is_file($routeFile) && is_readable($routeFile)) require_once $routeFile;
        return true;
    }

    public static function deactivate(string $name): bool
    {
        if (!self::isActive($name)) return true;
        foreach (self::$activePlugins as $activeName => $activeInfo) {
            if (isset(($activeInfo['dependencies'] ?? [])[$name])) throw new PluginDependencyException('Cannot deactivate a required plugin.');
        }
        $pluginClass = self::getPluginClass($name);
        if ($pluginClass) {
            $plugin = new $pluginClass();
            if (method_exists($plugin, 'disable')) $plugin->disable();
        }
        unset(self::$activePlugins[$name]);
        return true;
    }

    public static function isActive(string $name): bool { return isset(self::$activePlugins[$name]); }
    public static function all(): array { return self::$plugins; }
    public static function getActive(): array { return self::$activePlugins; }

    protected static function resolveDependencies(string $mainPlugin): array
    {
        $visited = []; $order = []; $temp = [];
        $visit = function($node) use (&$visit, &$visited, &$order, &$temp) {
            if (isset($temp[$node])) throw new PluginDependencyException('Circular plugin dependency detected.');
            if (isset($visited[$node])) return;
            if (!isset(self::$plugins[$node])) throw new PluginDependencyException('Plugin dependency not found.');
            $temp[$node] = true;
            foreach (array_keys(self::$plugins[$node]['dependencies'] ?? []) as $neighbor) $visit($neighbor);
            unset($temp[$node]); $visited[$node] = true; $order[] = $node;
        };
        $visit($mainPlugin);
        return $order;
    }

    protected static function checkVersion(string $pluginName, string $constraint): bool
    {
        if (!isset(self::$plugins[$pluginName])) return false;
        $version = self::$plugins[$pluginName]['version'] ?? '0.0.0';
        if (preg_match('/^>=([\d.]+)$/', $constraint, $m)) return version_compare($version, $m[1], '>=');
        if (preg_match('/^<=([\d.]+)$/', $constraint, $m)) return version_compare($version, $m[1], '<=');
        if (preg_match('/^==?([\d.]+)$/', $constraint, $m)) return version_compare($version, $m[1], '==');
        return version_compare($version, $constraint, '==');
    }

    protected static function validName(string $name): bool { return (bool)preg_match('/^[A-Za-z][A-Za-z0-9_-]{0,63}$/', $name); }
    protected static function validConstraint(string $constraint): bool { return (bool)preg_match('/^(?:>=|<=|==?)?\d+\.\d+\.\d+(?:[-+][A-Za-z0-9.-]+)?$/', $constraint); }

    protected static function getPluginClass(string $name): ?string
    {
        if (!isset(self::$plugins[$name])) return null;
        $path = self::$plugins[$name]['path'];
        $pluginFile = $path . '/Plugin.php';
        if (!is_file($pluginFile) || !is_readable($pluginFile)) return null;
        require_once $pluginFile;
        $className = "\\Plugins\\{$name}\\Plugin";
        return class_exists($className) ? $className : null;
    }
}
