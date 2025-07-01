Safe Haven Dutch Coaching Custom CMS
Overview
Tech Stack: PHP 8.1, MySQL, Composer (phpdotenv, stripe-php, phpmailer, sabre/vobject), HTML/CSS/JS, TinyMCE, Chart.js.
Hosting: TransIP Core (with phpMyAdmin, 20 GB storage, cron support).
Version Control: GitHub repository with GitHub Actions for SFTP deployment.
AI Tools: OpenAI GPT-3.5 API (for content assistance), Codex CLI (for code generation).
User Roles
Admin: Manages Users, Children, Lesson Packages, Bookings, Blog Posts, KPIs, Email Templates, and System Settings.
Parent: Accesses a personal dashboard to manage child profiles, view lesson bookings, track progress, read notes, download attachments, and use the messaging system.
Core Modules
Authentication: Secure email & password login with roles, CSRF protection, session security, rate-limiting, and GDPR consent tracking.
Lesson Packages: Admin CRUD for managing packages, tracking user credit balances, and handling optional expirations or subscriptions.
Booking Engine: Supports 1-on-1 and group lessons, waitlists, availability via iCal feeds, .ics calendar downloads for users, and cancellation rules.
Payments: Stripe Checkout integration with webhook handling for automatic credit updates.
Dashboards:
Admin: KPI charts and comprehensive CRUD views for all data.
Parent: View upcoming/past lessons, notes, attachments, and messages.
Blog CMS: TinyMCE-powered editor for blog posts, with categories, tags, and AI-assisted drafting.
AI Integration: Utilizes GPT-3.5 for generating lesson plan suggestions and blog post drafts, with mandatory human review.
File Uploads: Secure file storage outside the web root, with MIME type validation and access control via a PHP proxy.
Email System: Uses PHPMailer with SMTP for transactional emails triggered by system events (e.g., booking confirmation) and cron-based reminders.
Security & GDPR: Enforces CSRF protection, input sanitization, a Content Security Policy (CSP), HTTPS-only cookies, and provides tools for user data deletion/anonymization.
Developer Workflow: Sublime Text + Codex CLI, GitHub Actions for CI/CD, PHPUnit for testing, and weekly sprints.
6-Week Sprint Plan
Week	Focus	Deliverables
1	Env & Config	.env loader, PDO wrapper, migrations runner, initial schema, PHPUnit tests.
2	Packages & Stripe	packages & purchases migrations, PackageModel CRUD, Stripe Checkout & webhooks.
3	Booking Engine	lessons & bookings migrations, iCal parser, slot-picker UI, .ics generation, emails.
4	Dashboards	Parent dashboard (lessons/children), Admin dashboard (bookings/users).
5	Blog CMS	Blog schemas, TinyMCE CRUD, categories/tags, front-end filtering, AI-assist button.
6	Polish & QA	Cron job reminders, rate-limiting, GDPR deletion tool, CSP, final PHPUnit tests.