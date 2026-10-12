# AudioTrack

AudioTrack is an internal administration application for organizing audio equipment and coordinating its dispatch to client locations. It provides a Filament admin panel for managing kits, inventory items and categories, clients and their locations, drivers, users, and dispatches.

The admin dashboard summarizes kit inventory and today's in-transit dispatches, and lists today's scheduled dispatches with queue position, location, kit, driver, and status. Dispatches can be scheduled and managed from the Dispatches section.

## Requirements

- PHP 8.3 or newer
- Composer
- Node.js and npm
- SQLite for the default local setup, or another Laravel-supported database such as MySQL

## Installation

Clone the repository and change into its directory, then run:

```bash
composer run setup
```

The setup script installs PHP dependencies, creates `.env` from `.env.example` if needed, generates the application key, runs database migrations, installs JavaScript dependencies, and builds the frontend assets.

The default `.env.example` uses SQLite. To use another database, update the `DB_*` settings in `.env` before running migrations. Ensure the configured database exists and is accessible to the application.

To create or update the database table schema manually, run migrations before seeding or using the application:

```bash
php artisan migrate
```

`composer run setup` already runs the initial migrations. Run `php artisan migrate` again after pulling changes that add new migrations.

### Seed sample data

For a local development environment, load the sample categories, kits, items, clients, client locations, driver, and dispatch:

```bash
php artisan db:seed
```

Seeding does not create an admin login. Create one with Filament's user command:

```bash
php artisan make:filament-user --panel=admin
```

Follow the prompts to enter the admin user's name, email address, and password.

### Start the application

```bash
composer run dev
```

This starts Laravel's local web server, queue listener, log viewer when available, and Vite development server. Open [http://localhost:8000/admin](http://localhost:8000/admin) and sign in with the Filament user you created.

## Tests

Run the test suite with:

```bash
php artisan test --compact
```

Tests use an in-memory SQLite database configured in `phpunit.xml`.

## Technology

- Laravel 13
- Filament 5
- PHP 8.3+
- Tailwind CSS 4 and Vite
- SQLite by default (other Laravel-supported databases can be configured)
