# Safe Haven Dutch - Custom Coaching CMS

This is a custom-built Content Management System (CMS) for "Safe Haven Dutch," a coaching service for expats and internationals in the Netherlands. It is built with a modern tech stack and features a user-friendly front-end and a powerful admin panel.

---

## 📚 Key Features

*   **Modern, Responsive Front-End:** A beautiful, mobile-first design built with Tailwind CSS.
*   **Dynamic Page Management:** The homepage, about page, and other key pages are fully editable from within the admin panel.
*   **Blog CMS:** A complete blog system with posts and categories, powered by a TinyMCE WYSIWYG editor.
*   **User & Admin Roles:** A unified login system that intelligently redirects users and admins to their respective dashboards.
*   **Dynamic Site Branding:** The site logo and homepage hero image can be easily updated from a dedicated settings page in the admin panel.
*   **Secure & Modern Backend:** Built with PHP 8.1, a robust database migration system, and a focus on security best practices like Content Security Policy (CSP).

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

3.  **Run Database Migrations:**
    This will set up all the necessary tables in your database.
    ```bash
    php scripts/run-migrations.php
    ```

4.  **Create an Admin User:**
    To access the admin panel, run the following script to create a default admin account.
    ```bash
    php scripts/create_admin.php
    ```
    (Note: You can change the default credentials in the script itself.)

5.  **Run the Application:**
    Use a local PHP server or configure a virtual host to point to the project's root directory.

---

## 💡 Basic Usage

*   **Login:** Access the site and use the main "Login" button for all users. If you log in with the admin credentials, you will be automatically redirected to the admin dashboard.
*   **Admin Panel:** Once logged in as an admin, you can manage pages, blog posts, users, site settings, and more.

---

This project is under active development, with a focus on creating a beautiful, functional, and secure platform.
