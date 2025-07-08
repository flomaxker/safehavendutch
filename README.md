# Safe Haven Dutch - Custom Coaching CMS

This is a custom-built Content Management System (CMS) for "Safe Haven Dutch," a coaching service for expats and internationals in the Netherlands. It is built with a modern tech stack and features a user-friendly front-end and a powerful admin panel.

---

## 📚 Key Features

*   **Modern, Responsive Front-End:** A beautiful, mobile-first design built with Tailwind CSS.
*   **Dynamic Page Management:** The homepage, about page, and other key pages are fully editable from within the admin panel.
*   **Blog CMS:** A complete blog system with posts and categories, powered by a TinyMCE WYSIWYG editor.
*   **User & Admin Roles:** A unified login system that intelligently redirects users and admins to their respective dashboards.
*   **Rearrangeable Quick Actions:** Admins can customize their dashboard by rearranging the order of quick action buttons, and selecting which actions are visible, via a drag-and-drop modal.
*   **Persistent Admin Menu:** The collapsible sidebar menu in the admin panel remembers its open/closed state across page loads for a smoother user experience.
*   **Dynamic Site Branding:** The site logo and homepage hero image can be easily updated from a dedicated settings page in the admin panel.
*   **Rate Limiting:** Protects against brute-force login attacks by temporarily locking accounts after multiple failed attempts.
*   **GDPR Compliance Tools:** Includes a tool for anonymizing user data upon request and automated reminders for reviewing inactive accounts.
*   **Secure & Modern Backend:** Built with PHP 8.1, a robust database migration system, and a focus on security best practices like Content Security Policy (CSP).

---

## ✅ Completed MVP Features

*   **User Registration and Login:** Implemented a secure system for users to register and log in, with role-based redirection.
*   **Lesson Packages Listing:** A dedicated page to display available lesson packages to users.
*   **Package Purchase Flow:** Integrated Stripe Checkout for seamless package purchases, including webhook handling for automatic credit updates.
*   **User Dashboard:** Revamped user dashboard with a consistent UI/UX, including:
    *   A left-hand navigation menu with collapsible, grouped categories.
    *   A personalized top-right user dropdown for profile and logout.
    *   Dynamic user avatars based on initials.
    *   A 'Quick Actions' section for immediate access to key functionalities.
    *   An enhanced 'Euro Balance' card with improved visual appeal and a 'Top Up' call to action.
    *   Dedicated sections for user-specific information (My Children, Upcoming Lessons, etc.).
*   **Robust Payment Testing:** Comprehensive unit tests for the payment processing logic, ensuring reliability and stability.
*   **Secure Booking Engine:** Implemented transactional booking logic to prevent double-bookings and ensure data consistency, including capacity checks and credit deduction.
*   **Enhanced Parent Dashboard:** Updated the parent dashboard to display detailed upcoming lesson information.
*   **Admin Booking Management:** Developed a dedicated admin page for viewing and managing all bookings, including authentication and bulk deletion.
*   **UI Enhancements:** Increased the hero image size and improved spacing on the homepage for a more balanced and visually appealing layout.

---

## ⚙️ Tech Stack

*   **Backend:** PHP 8.1, MySQL
*   **Frontend:** HTML5, Tailwind CSS, Alpine.js
*   **Dependencies:** Composer, PHPMailer, Stripe PHP SDK
*   **Development:** Git for version control, PHPUnit for testing

---

## Tailwind CSS Production Build

For production deployments, it is recommended to build Tailwind CSS locally rather than relying on the CDN. This provides better performance and removes external dependencies.

To generate a minified production-ready CSS file, run the following command:

```bash
npm run build:production
```

This will create a `style.css` file in your project root. When deploying to production, ensure that your `header.php`, `admin/header.php`, and `user_header.php` files are updated to link to this local `style.css` file instead of the Tailwind CSS CDN.

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

## 🧪 Testing

To run the test suite, use the following command:

```bash
vendor/bin/phpunit
```

---

This project is under active development, with a focus on creating a beautiful, functional, and secure platform.