# Cyron Architecture

Cyron currently ships as a project foundation with the Kolbe Ketab application in the same repository. The boundaries below keep the application usable while allowing the framework to become a reusable package incrementally.

## Framework foundation

The reusable foundation lives primarily under `app/Core`, `app/Http`, `app/database`, `app/Request.php`, `app/Response.php`, and the routing/view infrastructure. Composer maps the current `App\\` namespace to `app/` so existing applications remain compatible.

The extracted framework classes currently include `Cyron\\Support\\Env`, `Cyron\\Database\\SqlGuard`, `Cyron\\Database\\Collection`, `Cyron\\Database\\Paginator`, and `Cyron\\Database\\TableBuilder`. Their original `App\\` classes remain compatibility wrappers while the remaining foundation is migrated incrementally.

## Kolbe Ketab application

Website-specific behavior belongs in `routes/`, `resources/Views/`, `app/Models`, `app/Http/Controllers`, `app/Modules`, and the site configuration. The admin, user, and author panels are application features, not framework modules.

## Entrypoints

- `public/index.php` boots the web application.
- `zeno` is the supported CLI entrypoint.
- `zeno.php` is retained as a compatibility entrypoint for older scripts.
- `composer install` creates the primary PSR-4 autoloader; the legacy loader remains temporarily for backward compatibility.

## Extraction path

Future framework extraction should move reusable classes to a dedicated `src/` namespace and leave site code in `app/`. That migration should happen package-by-package with tests, rather than moving the entire `app/` tree in one change.
