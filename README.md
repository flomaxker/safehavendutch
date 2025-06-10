# Safe Haven Dutch CMS

This is a custom-built CMS for Safe Haven Dutch Coaching, a Dutch language and integration coaching service for children and families. The system is built using PHP, MySQL, and Stripe, and is gradually evolving into a flexible platform that may be used by other educational or community organizations.

Our goal is to create a safe, welcoming, and intuitive platform where parents can manage Dutch lessons for their children, and where administrators can coordinate bookings, credits, and content — all within a GDPR-compliant, lovingly designed space.

---

## 🧠 Project Overview for Codex (AI Assistant)
This project is a PHP-based CMS for Safe Haven Dutch Coaching. It includes:
- A dual-login system for parents and admins (in progress)
- A credit-based lesson booking system
- A Stripe integration for purchasing packages
- A parent dashboard to view credits and eventually book lessons

So far, we've built:
- The database structure and migration scripts
- Credit system & Stripe checkout
- Admin login and basic dashboard

Next up: building `login.php` and the parent dashboard. Eventually, we’ll allow editable content and optional AI-assisted page generation.

---

## Features (MVP)
- Dual-login system: separate accounts for parents and admins
- Lesson booking system with lesson credit tracking
- Stripe integration for purchasing packages of lessons
- Admin dashboard for managing lesson packages
- Parent dashboard for viewing and managing lesson credits

## Current State
- ✅ Stripe checkout is implemented and adds lesson credits correctly
- ✅ Migration system and tests for database structure are complete
- ✅ Admin login exists with basic dashboard functionality
- 🚧 Login system for parents and user dashboard still in progress
- 🚧 Front-end CMS for editable content (e.g., announcements, home page) is planned

## Directory Structure
```
/
├── migrations/        # SQL files for the database schema
├── scripts/           # Helper scripts, e.g. run-migrations.php
├── tests/             # PHPUnit tests
├── public_html/       # Static and public-facing pages (index, about, contact, etc.)
├── src/               # Application classes and core PHP logic
├── content/           # CMS content (coming soon)
├── uploads/           # Uploaded files/media
├── .env               # Environment variables
└── README.md
```

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

## Basic usage

Once your `.env` file is configured and migrations are applied, you can run the application via a local PHP server or integrate it with your web server. Stripe webhook handling is in `stripe_webhook.php`. Checkout sessions are created in `checkout.php`. The contact form submission logic lives in `contact-handler.php` and uses **PHPMailer** for sending HTML emails.

Further details on the checkout flow and contact form can be found in `docs/USAGE_CONTACT_PAYMENT.md`.

## Customization roadmap

The site currently uses static HTML files for pages like `index.html` and `announcement.html`. These provide the default layout for Safe Haven Dutch Coaching. The `content/` and `uploads/` directories serve as placeholders for a future CMS that will allow dynamic pages and user-generated content. Until that system is built, the static HTML remains in place.

---

This project is in early MVP stage and evolving with flexibility and care.