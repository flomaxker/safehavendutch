# Safe Haven Dutch - Custom Coaching CMS

This is a custom-built Content Management System (CMS) for "Safe Haven Dutch," a coaching service for expats and internationals in the Netherlands. It is built with a modern tech stack and features a user-friendly front-end and a powerful admin panel.

For detailed project status, development roadmap, and design guidelines, please refer to `STATUS.md`. For detailed project documentation, see the [`/docs`](./docs) folder.

---

## 📚 Key Features

*   **Modern, Responsive Front-End:** A beautiful, mobile-first design built with Tailwind CSS.
*   **Dynamic Page Management:** The homepage, about page, and other key pages are fully editable from within the admin panel.
*   **Blog CMS:** A complete blog system with posts and categories, powered by a TinyMCE WYSIWYG editor.
*   **User & Admin Roles:** A unified login system that intelligently redirects users and admins to their respective dashboards.
*   **Credit-Based Booking:** Parents can purchase credits via Stripe and use them to book lessons for their children.
*   **Parent Dashboard:** A dedicated area for parents to manage their children, view upcoming lessons, and track their credit balance.
*   **Admin Panel:** A comprehensive dashboard for managing users, lessons, packages, bookings, and all site content.
*   **Secure & Modern Backend:** Built with PHP 8.1, a robust database migration system, and a focus on security best practices.

---

## ⚙️ Tech Stack

*   **Backend:** PHP 8.1, MySQL
*   **Frontend:** HTML5, Tailwind CSS, Alpine.js
*   **Dependencies:** Composer, PHPMailer, Stripe PHP SDK
*   **Development:** Git for version control, PHPUnit for testing

---

## 🚀 Getting Started

1.  **Install Dependencies:**
    ```bash
    composer install
    ```

2.  **Configure Environment:**
    Copy `.env.example` to `.env` and fill in your database credentials and any other required API keys (Stripe, etc.).

3.  **Run Database Migrations (Idempotent + Baseline):**
    This sets up all necessary tables. When a baseline exists (`migrations/100_baseline_schema.sql`), only 100+ migrations run.
    ```bash
    php scripts/run-migrations.php
    ```

4.  **Create an Admin User:**
    To access the admin panel, run the following script to create a default admin account.
    ```bash
    php scripts/create_admin.php
    ```
    (Note: You can change the default credentials in the script itself.)

5.  **Seed Demo Content (optional):**
    - Seed pages (Home/About/Packages/Contact/Terms/Privacy):
      ```bash
      php create_default_pages.php
      ```
    - Seed users, packages, balances, children:
      ```bash
      php scripts/seed-database.php
      ```
    - Seed demo lessons and auto-book children:
      ```bash
      php scripts/seed-lessons-and-bookings.php
      ```

    - Or run everything in one go (migrations + all seeders):
      ```bash
      php scripts/seed-all.php --update
      ```

6.  **Run the Application:**
    Use a local PHP server or configure a virtual host to point to the project's root directory.

---

## 👥 Roles & Access

- Current roles in the database are `admin` and `member`. Treat `member` as “parent” throughout the app. An explicit `teacher` role (and `parent`) will be introduced during the P1/P3 roadmap items. See `docs/PROJECT_OVERVIEW.md` for details.

---

## 🔐 Security Notes

- All database access uses prepared statements via the project database wrapper/`PDO`.
- Admin forms use CSRF tokens (stored in session and/or set from a per-request nonce) which are validated by their handlers before processing.

---

## 🧪 Testing

To run the test suite, use the following command:

```bash
vendor/bin/phpunit
```

---

This project is under active development, with a focus on creating a beautiful, functional, and secure platform.
