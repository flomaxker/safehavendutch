# General Purpose PHP CMS

This is a custom-built Content Management System (CMS) designed to be a flexible platform for various organizations, such as educational institutions, community groups, or small businesses. It is built using PHP, MySQL, and integrates with Stripe for payment processing.

Our goal is to create an intuitive and adaptable platform where administrators can manage content, users, and services. The system is being developed with a focus on reusability and compliance.

---

## 🧠 Project Overview for Codex (AI Assistant)
This project is a PHP-based CMS. It includes:
- A dual-login system for general users and admins
- A credit-based service booking/tracking system (can be adapted for lessons, appointments, etc.)
- A Stripe integration for purchasing packages/services
- A user dashboard to view credits/services and manage their account

So far, we've built:
- The database structure and migration scripts
- Credit system & Stripe checkout
- Admin login and basic dashboard
- User registration and login
- Basic user dashboard and package listing

**Current Priority: Resolving Database Connection Issues.** The dynamic content pages (e.g., `index_dynamic.php`, `about.php`, `contact.php`, `privacy-policy.php`, `terms.php`) are currently blank due to database connection errors. This is the highest priority blocking further development of dynamic features.

---

## Features (MVP)
- Dual-login system: separate accounts for general users and admins
- Service booking/tracking system with credit tracking
- Stripe integration for purchasing packages/services
- Admin dashboard for managing services/packages
- User dashboard for viewing and managing credits/services
- User registration

## Current State
- ✅ Stripe checkout is implemented and adds credits correctly
- ✅ Migration system and tests for database structure are complete
- ✅ Admin login exists with basic dashboard functionality
- ✅ User registration and login implemented
- ✅ Basic user dashboard and package listing implemented
- ✅ Centralized navigation links for easier management
- ✅ Streamlined JavaScript to only include essential functionalities
- 🚧 **Database connection issues are currently blocking dynamic content.**
- 🚧 Front-end CMS for editable content (e.g., announcements, home page) is planned

## Directory Structure
```
/
├── migrations/        # SQL files for the database schema
├── scripts/           # Helper scripts, e.g. run-migrations.php
├── tests/             # PHPUnit tests
├── index.html         # Static landing page (default)
├── index_dynamic.php  # Dynamic content page (requires database)
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

**Admin Login Credentials (for MVP):**
- **Username:** `admin`
- **Password:** `password`

Further details on the checkout flow and contact form can be found in `docs/USAGE_CONTACT_PAYMENT.md`.

## Customization roadmap

The site currently uses `index.html` as a static landing page. The `content/` and `uploads/` directories serve as placeholders for a future CMS that will allow dynamic pages and user-generated content.

---

This project is in early MVP stage and evolving with flexibility and care.