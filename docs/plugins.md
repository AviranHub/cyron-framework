# Modules and Plugins

## Application modules

Features that belong to the application and depend on its models, routes, or database stay in `app/Modules`:

- `app/Modules/Auth`
- `app/Modules/Admin`

They may provide publish commands because their files are application-specific.

## Local plugins

Project-local plugins live in `app/Plugins`. They are discovered by `PluginManager` and can be enabled in `app/Config/plugins.php`:

```php
return [
    'enabled' => ['TextEditor'],
];
```

## Composer plugins

A reusable plugin should be its own Composer package. Its package `composer.json` declares the plugin metadata:

```json
{
    "extra": {
        "cyron": {
            "plugin": {
                "name": "Reports",
                "class": "Vendor\\Reports\\Plugin"
            }
        }
    }
}
```

After installation with `composer require vendor/reports`, the framework discovers the package from Composer's installed package metadata. Add `Reports` to `app/Config/plugins.php` to boot it.

The package's PHP code remains in `vendor`. Publishing is optional and should be limited to application-owned assets, configuration, routes, or migrations that the project needs to customize.
