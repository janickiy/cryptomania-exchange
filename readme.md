# Cryptomania Exchange

Cryptomania Exchange is a Laravel-based cryptocurrency exchange project with a public frontend, trader workflows, reports, wallet operations, ICO purchase flow, and an administrative panel.

## Stack

- PHP 8.3
- Laravel 13
- Apache 2
- MySQL 8.4
- Composer 2
- AdminLTE 4 for the administrative panel
- Blade, jQuery, DataTables, Bootstrap-based legacy frontend assets

## Architecture

The project follows a layered structure:

- `app/Http/Controllers` - thin HTTP layer. Controllers should validate input through Form Requests, call services/repositories, and return a response.
- `app/Http/Requests/Admin` - admin Form Request validation classes.
- `app/DTO` - typed data objects for create/update flows and other structured input.
- `app/Repositories` - database access contracts and Eloquent implementations.
- `app/Services` - business logic that is not direct database access.
- `resources/views/backend` - admin panel views.
- `resources/views/frontend` and `resources/views/home.blade.php` - public frontend views.
- `public/frontend/liquid-glass.css` - current public frontend styling.
- `docker` - Docker files for local development.

AdminLTE is used only for the administrative panel. The public frontend keeps the project-specific liquid glass design.

## Local Docker Setup

Docker is the recommended way to run the project locally.

```bash
docker compose -f docker/docker-compose.yml up -d --build
```

The app container entrypoint will:

- create `.env` from `.env.example` if it does not exist;
- install Composer dependencies if `vendor` is missing;
- wait for MySQL;
- generate `APP_KEY` if needed;
- create the `public/storage` symlink if needed.

Open the site:

- Frontend: http://localhost:8081
- Admin/login: http://localhost:8081/login
- MySQL from host: `127.0.0.1:33060`
- MySQL inside Docker network: `mysql:3306`

Default Docker database credentials:

```dotenv
DB_DATABASE=cryptomania
DB_USERNAME=cryptomania
DB_PASSWORD=cryptomania
```

## Database

Run migrations and seed demo data inside the app container:

```bash
docker exec cryptomania-app php artisan migrate:fresh --seed
```

Seeded users:

| Role | Email | Password |
| --- | --- | --- |
| Super Admin | `superadmin@codemen.org` | `superadmin` |
| Trader | `trader@codemen.org` | `trader` |
| Trader | `trader2@codemen.org` | `trader2` |
| Trade Analyst | `tradeanalyst@codemen.org` | `tradeanalyst` |

## Useful Commands

```bash
# Start containers
docker compose -f docker/docker-compose.yml up -d

# Stop containers
docker compose -f docker/docker-compose.yml down

# Rebuild app image
docker compose -f docker/docker-compose.yml up -d --build

# Run Artisan
docker exec cryptomania-app php artisan <command>

# Clear Laravel caches
docker exec cryptomania-app php artisan optimize:clear

# Install/update PHP dependencies
docker exec cryptomania-app composer install
docker exec cryptomania-app composer update
```

## Tests

Run tests inside Docker:

```bash
docker exec cryptomania-app php artisan test
```

Important: the current test suite contains tests that use `RefreshDatabase`. With the current local Docker configuration, this can refresh the configured MySQL database. Use a separate testing database before running destructive test flows against important local data.

## Development Notes

- Keep controllers thin: move business rules to services and database access to repositories.
- Use Form Requests for validation, especially under `App\Http\Requests\Admin` for admin flows.
- Use DTOs for create/update payloads where data crosses from requests into services/repositories.
- Prefer constructor injection over `app(...)` calls in controllers.
- Keep public frontend changes separate from AdminLTE admin views.
- After changing routes, services, or providers, run:

```bash
docker exec cryptomania-app php artisan optimize:clear
docker exec cryptomania-app php artisan route:list
```

## Current Local Ports

| Service | Host URL |
| --- | --- |
| App | http://localhost:8081 |
| MySQL | `127.0.0.1:33060` |
