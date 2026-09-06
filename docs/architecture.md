# Cyron Architecture

Cyron currently ships as a project foundation with the Kolbe Ketab application in the same repository. The boundaries below keep the application usable while allowing the framework to become a reusable package incrementally.

## Framework foundation

The reusable foundation lives under `src/Cyron`, while application code lives under `app`. Application migrations live under the root `database/Migrations` directory. Composer maps `Cyron\\` to `src/Cyron/` and `App\\` to `app/` for application compatibility.

The extracted framework classes currently include `Cyron\\Support\\Env`, `Cyron\\Database\\SqlGuard`, `Cyron\\Database\\Db`, `Cyron\\Database\\Collection`, `Cyron\\Database\\Paginator`, `Cyron\\Database\\TableBuilder`, `Cyron\\Database\\Migration`, `Cyron\\Database\\Builder`, `Cyron\\Database\\Model`, `Cyron\\Database\\Relation`, the `Cyron\\Database\\Relations` implementations, `Cyron\\Http\\Response`, `Cyron\\Http\\Request` with `Cyron\\Http\\File`, `Cyron\\Http\\Middleware`, `Cyron\\Http\\Controller`, and `Cyron\\Routing\\Route`. Their original `App\\` classes remain compatibility wrappers while the remaining foundation is migrated incrementally. Composer is preferred by the legacy loader whenever it is available.

## Kolbe Ketab application

Website-specific behavior belongs in `routes/`, `resources/Views/`, `app/Models`, `app/Http/Controllers`, `app/Modules`, and the site configuration. The admin, user, and author panels are application features, not framework modules.

## Entrypoints

- `public/index.php` boots the web application.
- `zeno` is the supported CLI entrypoint.
- `zeno` is the supported CLI entrypoint.
- `composer install` creates the primary PSR-4 autoloader; the legacy loader remains temporarily for backward compatibility.
- `composer test` runs the complete PHP smoke/regression suite.
- `composer validate-project` validates the project manifest.

## Extraction path

Future framework extraction should move reusable classes to a dedicated `src/` namespace and leave site code in `app/`. That migration should happen package-by-package with tests, rather than moving the entire `app/` tree in one change.
