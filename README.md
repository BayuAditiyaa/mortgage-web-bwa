# Mortgage Web BWA

Mortgage Web BWA is a Laravel portfolio project for browsing properties, submitting mortgage applications, managing installment payments, and administering property data from a Filament dashboard.

## Features

- Public property catalog with category, detail, and search pages.
- Customer authentication, profile management, and mortgage request flow.
- Mortgage installment dashboard with Midtrans Snap payment integration.
- Filament admin panel for managing houses, categories, cities, banks, interests, facilities, mortgage requests, users, roles, and permissions.
- Role-based access powered by Spatie Laravel Permission, including admin, developer, and customer demo roles.
- Developer-owned house listings, where developers can manage their own houses and related bank interest options from Filament.
- Filament portfolio dashboard metrics for managed houses, mortgage requests, approved requests, and paid installments.
- Developer scoping tests to ensure developers cannot view another developer's inventory or mortgage requests.
- Automated tests for authentication, profile flows, homepage rendering, and mortgage data access protection.

## Tech Stack

- Laravel 11
- Blade, Tailwind CSS, Alpine.js, Vite
- Filament 3
- MySQL or MariaDB
- Midtrans PHP SDK
- PHPUnit

## Local Setup

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate --seed
npm run build
php artisan serve
```

Seeded demo credentials:

```text
Admin: admin@mortgage.test / password
Developer: developer@mortgage.test / password
Customer: customer@mortgage.test / password
```

Configure these values in `.env` before testing payment features:

```env
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
GOOGLE_MAPS_EMBED_KEY=
```

Payment notifications are stored in `payment_transactions` by Midtrans order ID so repeated webhook deliveries do not create duplicate installment records.

## Testing

The test suite uses SQLite in-memory through `phpunit.xml`, so it can run without a local MySQL database.

```bash
php artisan test
```

To run the same suite against local MySQL, create a dedicated test database named `tedjaweb_testing`, then run:

```bash
vendor/bin/phpunit -c phpunit.mysql.xml
```

## Deployment Notes

- Set `APP_ENV=production`, `APP_DEBUG=false`, and a real `APP_URL`.
- Run `php artisan migrate --seed` only when you intentionally want to seed demo/admin data.
- Run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache` after updating environment variables.
- Make sure the queue worker and storage symlink are configured if uploaded files or queued jobs are used.
