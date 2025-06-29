# Safe Haven Dutch Coaching Custom CMS

This is a custom-built Content Management System (CMS) designed to be a flexible platform for educational institutions, community groups, or small businesses. It is built using PHP, MySQL, and integrates with Stripe for payment processing.

Our goal is to create an intuitive and adaptable platform where administrators can manage content, users, and services, with a strong focus on reusability and GDPR compliance.

---

## 📚 Detailed Documentation
For in-depth information on the project's technical design, architecture, and detailed sprint plans, please refer to:
- [ARCHITECTURE.md](ARCHITECTURE.md)
- [PROJECT.md](PROJECT.md)

---

## 🧠 Project Overview

This project is a PHP-based CMS with a focus on managing lesson packages, bookings, and user interactions for a coaching service. Key components include:

*   **Tech Stack:** PHP 8.1, MySQL, Composer (phpdotenv, stripe-php, phpmailer, sabre/vobject), HTML/CSS/JS, TinyMCE, Chart.js.
*   **Hosting & Deployment:** Hosted on TransIP Core with GitHub Actions for SFTP deployment.
*   **AI Integration:** Utilizes OpenAI GPT-3.5 API for content assistance (e.g., lesson plan suggestions, blog post drafts) and Codex CLI for code generation.

### User Roles

*   **Admin:** Manages Users, Children, Lesson Packages, Bookings, Blog Posts, KPIs, Email Templates, and System Settings.
*   **Parent:** Accesses a personal dashboard to manage child profiles, view lesson bookings, track progress, read notes, download attachments, and use the messaging system.

### Core Modules

*   **Authentication:** Secure email & password login with roles, CSRF protection, session security, rate-limiting, and GDPR consent tracking.
*   **Lesson Packages:** Admin CRUD for managing packages, tracking user Euro balances, and handling optional expirations or subscriptions.
*   **Booking Engine:** Supports 1-on-1 and group lessons, waitlists, iCal integration, .ics calendar downloads for users, and cancellation rules.
*   **Payments:** Stripe Checkout integration with webhook handling for automatic Euro balance updates.
*   **Dashboards:** Admin (KPI charts and comprehensive CRUD views), Parent (view upcoming/past lessons, notes, attachments, messages).
*   **Blog CMS:** TinyMCE-powered editor for blog posts, with categories, tags, and AI-assisted drafting.
*   **File Uploads:** Secure file storage outside the web root, with MIME type validation and access control via a PHP proxy.
*   **Email System:** Uses PHPMailer with SMTP for transactional emails triggered by system events (e.g., booking confirmation) and cron-based reminders.
*   **Security & GDPR:** Enforces CSRF protection, input sanitization, a Content Security Policy (CSP), HTTPS-only cookies, and provides tools for user data deletion/anonymization.

---

## ✨ Features (Minimum Viable Product - MVP)

The MVP for this project is defined by a 6-week sprint plan, focusing on delivering core functionality for user management, package purchasing, and a foundational booking system. The full MVP encompasses:

*   **Dual-login system:** Separate accounts for general users (Parents) and Admins.
*   **Euro-based balance system:** Transparent pricing and balance tracking in Euros.
*   **Stripe integration:** For purchasing packages/services, with automatic Euro balance updates.
*   **Admin dashboard:** For managing services/packages, users, and bookings, with responsive design, sortable columns, and bulk actions.
*   **User dashboard:** For viewing and managing Euro balances, child profiles, and lesson bookings.
*   **User registration and editing.**
*   **Booking Engine:** Basic lesson and booking management, iCal integration for availability.
*   **Blog CMS:** Basic content creation and display.

---

## 🚀 Current State

We have completed the foundational work and are progressing through the sprint plan:

*   ✅ **Full transition to Euro-based pricing and balances.**
*   ✅ **Week 1: Environment & Configuration** - `.env` loader, PDO wrapper, migrations runner, initial schema, PHPUnit tests.
*   ✅ **Week 2: Packages & Stripe** - Packages & purchases migrations, `PackageModel` CRUD, Stripe Checkout & webhooks.
*   ✅ **Admin login** exists with basic dashboard functionality.
*   ✅ **User registration and editing** implemented.
*   ✅ **Basic user dashboard** and package listing implemented.
*   ✅ **Centralized navigation links** for easier management.
*   ✅ **Streamlined JavaScript** to only include essential functionalities.
*   ✅ **Enhanced admin panel responsiveness:** Fixed sidebar, mobile menu, horizontal table scrolling.
*   ✅ **Sortable columns** in user list.
*   ✅ **Bulk user deletion.**
*   ✅ **Bulk package deletion, activation, and deactivation.**
*   ✅ **Improved database seeding** with realistic user names.
*   🚧 **Current Priority: Database connection issues for dynamic content.** The dynamic content pages (e.g., `index_dynamic.php`, `about.php`, `contact.php`, `privacy-policy.php`, `terms.php`) are currently blank due to database connection errors. This is the highest priority blocking further development of dynamic features.
*   🚧 Front-end CMS for editable content (e.g., announcements, home page) is planned.

---

## 🗺️ Roadmap (6-Week Sprint Plan Summary)

This project follows a structured 6-week sprint plan to deliver the MVP:

*   **Week 1:** Environment & Config (Completed)
*   **Week 2:** Packages & Stripe (Completed)
*   **Week 3:** Booking Engine - Define lessons/bookings schemas, iCal parser, slot-picker UI, .ics generation, emails.
*   **Week 4:** Dashboards - Parent dashboard (lessons/children), Admin dashboard (bookings/users).
*   **Week 5:** Blog CMS - Blog schemas, TinyMCE CRUD, categories/tags, front-end filtering, AI-assist button.
*   **Week 6:** Polish & QA - Cron job reminders, rate-limiting, GDPR deletion tool, CSP, final PHPUnit tests.

---

## 📁 Directory Structure

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

---

## ⚙️ Setup

1.  **Install dependencies**
    ```bash
    composer install
    ```

2.  **Create an environment file**
    Copy `.env.example` to `.env` and fill in the required variables:
    -   `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` – database connection details
    -   `STRIPE_SECRET_KEY` – your Stripe secret key
    -   `STRIPE_WEBHOOK_SECRET` – webhook signing secret
    -   `OPENAI_API_KEY` – (optional) required only if you use features that talk to OpenAI

    The application loads these variables automatically when it runs.

3.  **Run database migrations**
    The repository includes SQL migration files under `migrations/`. Apply them using the provided script:
    ```bash
    php scripts/run-migrations.php
    ```
    This script will create a `migrations` table and run any pending `.sql` files.

4.  **Run the test suite**
    Tests use PHPUnit. After installing dependencies, run:
    ```bash
    ./vendor/bin/phpunit
    ```

    Only `DatabaseTest` has real assertions; other tests are currently placeholders.

---

## 💡 Basic Usage

Once your `.env` file is configured and migrations are applied, you can run the application via a local PHP server or integrate it with your web server. Stripe webhook handling is in `stripe_webhook.php`. Checkout sessions are created in `checkout.php`. The contact form submission logic lives in `contact-handler.php` and uses **PHPMailer** for sending HTML emails.

**Admin Login Credentials (for MVP):**
-   **Username:** `admin`
-   **Password:** `password`

Further details on the checkout flow and contact form can be found in `docs/USAGE_CONTACT_PAYMENT.md`.

---

This project is in early MVP stage and evolving with flexibility and care.