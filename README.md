# Safe Haven Dutch

This project powers the Safe Haven Dutch website. It uses PHP with Composer dependencies and a MySQL database. Stripe is used for payments.

## Setup

1. **Install dependencies**
   ```bash
   composer install
   ```

2. **Create an environment file**
   Copy `.env.example` to `.env` and fill in the required variables:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` – database connection details
   - `STRIPE_SECRET_KEY` – your Stripe secret key
   - `STRIPE_WEBHOOK_SECRET` – webhook signing secret
   - `OPENAI_API_KEY` – (optional) required only if you use features that talk to OpenAI

   The application loads these variables automatically when it runs.

3. **Run database migrations**
   The repository includes SQL migration files under `migrations/`. Apply them using the provided script:
   ```bash
   php scripts/run-migrations.php
   ```
   This script will create a `migrations` table and run any pending `.sql` files.

4. **Run the test suite**
   Tests use PHPUnit. After installing dependencies, run:
   ```bash
   ./vendor/bin/phpunit
   ```

   Only `DatabaseTest` has real assertions; other tests are currently placeholders.

## Directory structure

- `app/` – application classes
- `migrations/` – SQL files for the database schema
- `scripts/run-migrations.php` – helper script to apply migrations
- `tests/` – PHPUnit tests

## Basic usage

Once your `.env` file is configured and migrations are applied, you can run the application via a local PHP server or integrate it with your web server. Stripe webhook handling is in `stripe_webhook.php`. Checkout sessions are created in `checkout.php`.


## Customization roadmap

The site currently uses static HTML files for pages like `index.html` and `announcement.html`. These provide the default layout for Safe Haven Dutch Coaching. The `content/` and `uploads/` directories serve as placeholders for a future CMS that will allow dynamic pages and user-generated content. Until that system is built, the static HTML remains in place.
