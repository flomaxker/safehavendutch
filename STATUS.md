# Safe Haven Dutch - Project Status & Context

### **Note:** This document is the single source of truth for both technical and design direction. It is to be updated manually as tasks are completed.

---

## 1. Project Overview
A custom-built, GDPR-compliant CMS for a Dutch coaching service, featuring separate dashboards for administrators and clients (parents) to manage lesson bookings and user data. The product is designed for future licensing to schools, businesses, and organizations.

The core brand values are **warmth, safety, clarity, personal growth, and cultural belonging**. The user experience should feel supportive, emotionally safe, and gently guided.

## 2. Visual & UX Inspiration
All new UI development must closely follow the style, structure, and user experience of the **Logip dashboard by Halo Lab** (see attached `logip-dashboard.jpg`).

*   **Layout:** Clean, modern, responsive, and dashboard-centric with a left sidebar for navigation, a central area for KPIs and tasks, and a right sidebar for activity/profiles.
*   **Color & Style:** A warm, emotionally supportive color palette with soft backgrounds and subtle, playful color accents for highlights and status. Use rounded corners and subtle shadows.
*   **Interactivity:** Meaningful microinteractions (hover states, gentle animations) that improve usability without being distracting.
*   **Microcopy:** Friendly, supportive, and clear language inspired by the tone of Duolingo and the clarity of Notion.
*   **Accessibility:** All development must meet WCAG 2.1 A standards, including readable contrast, keyboard navigation, and alt text.

## 3. Core Architecture
- **Dual-Login System**: Separate, secure dashboards for Admins and Parents.
- **Credit-Based Economy**: Parents purchase credits via Stripe, which are then used to book lessons.
- **Service-Oriented Backend**: Core functionalities like Database access, Mail, and Calendar services are encapsulated in the `/app` directory.
- **Migration-Managed Database**: The MySQL database schema is managed via versioned SQL migration scripts.

## 4. Tech Stack & Key Commands
- **Backend**: PHP 8.1, MySQL
- **Frontend**: HTML5, Tailwind CSS, Alpine.js
- **Dependencies**: Composer, PHPMailer, Stripe PHP SDK, sabre/vobject
- **Testing**: PHPUnit

- **Install Dependencies**: `composer install`
- **Run Migrations**: `php scripts/run-migrations.php`
- **Create Admin User**: `php scripts/create_admin.php`
- **Run Tests**: `vendor/bin/phpunit`
- **Build CSS for Production**: `npm run build:production`

## 5. Key Directory Structure
- **/app**: Contains the core application logic, including the `Database` wrapper, `Mailer`, `iCal` parser, and data `Models`.
- **/admin**: The backend administrative dashboard for managing all aspects of the site.
- **/migrations**: Holds all SQL database migration scripts in chronological order.
- **/scripts**: Contains command-line helper scripts for tasks like running migrations or creating admin users.
- **/vendor**: Composer dependencies.
- **/tests**: PHPUnit test files.

## 6. Development Status

### ✅ Completed Features
- **Core Infrastructure**: Environment configuration (`.env`), database migrations, and CLI scripts are functional.
- **User Authentication**: Secure registration, login/logout for both Admins and Parents, including role-based redirection and rate limiting.
- **Package & Payment System**: Admins can manage lesson packages. Parents can purchase credits via Stripe, with webhooks correctly updating their balance.
- **Booking Engine (Core)**: The backend logic for creating bookings is implemented, including transactional safety to prevent double-booking.
- **Admin Dashboard (Core)**: Admins can manage packages and view all bookings.
- **Parent Dashboard (Core)**: Parents can view their credit balance and a list of their upcoming lessons.
- **Dynamic Site Content**: Admin can manage the homepage, about page, blog, and site branding (logo, hero image).
- **Admin Dashboard KPIs**: Enhanced admin dashboard to display key performance indicators including total users, total bookings, and total revenue.
- **UI/UX Enhancements**: Improved admin and user dashboard aesthetics, including wider sidebars, styled main content areas, and optimized chart/activity panel layouts.

### 🚀 Next Steps (Prioritized MVP To-Do List)

#### [P0] Parent: Child Management
- **[P0.1 - Read]** Display a list of the user's children on `my_children.php`.
  - **Files:** `my_children.php`
  - **DB Table:** `children` (SELECT)
  - **Outcome:** The page should list the names and ages of children associated with the logged-in parent.
- **[P0.2 - Create]** Add a form to `my_children.php` for adding a new child.
  - **Files to Edit:** `my_children.php`
  - **File to Create:** A new handler file (e.g., `create_child_handler.php`)
  - **DB Table:** `children` (INSERT)
  - **Outcome:** A parent can fill out a form to add a new child to their profile.
- **[P0.3 - Update]** Add an "Edit" button for each child in the list.
  - **Files to Edit:** `my_children.php`
  - **File to Create:** `edit_child.php` (with a pre-filled form)
  - **DB Table:** `children` (UPDATE)
  - **Outcome:** A parent can click "Edit" to go to a form and update a child's information.
- **[P0.4 - Delete]** Add a "Delete" button for each child in the list.
  - **Files to Edit:** `my_children.php`
  - **File to Create:** A new handler file (e.g., `delete_child_handler.php`)
  - **DB Table:** `children` (DELETE)
  - **Outcome:** A parent can click "Delete" to remove a child from their profile after a confirmation prompt.

#### [P1] Admin: Core Lesson & Teacher Management
- **[P1.1 - CRUD for Teachers]** Build an admin interface to manage teachers.
  - **Files:** Create `admin/teachers/index.php`, `edit.php`, and handlers.
  - **DB Table:** `users` (manage users with `role` = 'teacher').
  - **Outcome:** Admins can create, view, update, and delete teacher accounts.
- **[P1.2 - CRUD for Lessons]** Build an admin interface for managing lesson slots.
  - **Files:** Create `admin/lessons/index.php`, `create.php`, `edit.php`, and handlers.
  - **DB Table:** `lessons` (INSERT, SELECT, UPDATE, DELETE).
  - **Outcome:** Admins can define lesson times, assign teachers, and set capacity/cost.

#### [P2] Parent: Lesson Booking & Dashboard
- **[P2.1 - Booking UI]** Develop the front-end for parents to book lessons.
  - **Files:** `book_lesson.php`.
  - **DB Tables:** `lessons` (SELECT), `children` (SELECT), `bookings` (INSERT), `users` (UPDATE `euro_balance`).
  - **Outcome:** A parent can select a child, view available lessons, book a spot, and have the correct number of credits deducted.
- **[P2.2 - Lesson History]** Display past lessons on the parent dashboard.
  - **Files:** `my_bookings.php` or `dashboard.php`.
  - **DB Table:** `bookings` (SELECT WHERE lesson date is in the past).
  - **Outcome:** Parents can see a historical record of their child's completed lessons.
- **[P2.3 - View Teacher Notes & Attachments]** Show notes and files from a teacher for a completed lesson.
  - **Files:** `my_bookings.php` (in the lesson history section).
  - **DB Table:** `lesson_feedback` (SELECT).
  - **Outcome:** Parents can see feedback and download any files the teacher attached for a past lesson.

#### [P3] Teacher: Basic Workflow
- **[P3.1 - View Schedule]** Create a simple view for teachers to see their upcoming lessons.
  - **Files:** A new page, e.g., `teacher_dashboard.php`.
  - **DB Tables:** `lessons`, `bookings` (SELECT where `teacher_id` matches).
  - **Outcome:** A logged-in teacher can view their personal teaching schedule.
- **[P3.2 - Add Notes & Upload Attachments]** Allow teachers to add notes and upload files for a completed lesson.
  - **Files:** The `teacher_dashboard.php` or a dedicated `add_feedback.php` page.
  - **DB Table:** `lesson_feedback` (INSERT/UPDATE).
  - **Outcome:** Teachers can provide written feedback and upload a file for a student after a lesson is complete.

#### [P4] System: Communications
- **[P4.1 - Email Notifications]** Implement automated emails for key events.
  - **Library:** PHPMailer.
  - **Files:** Integrate into booking handlers; create new scripts for reminders and password resets.
  - **Outcome:** Users receive emails for Booking Confirmations, Lesson Reminders, and Password Resets.

#### [P5] System: Security & Polish
- **[P5.1 - CSRF Protection]** Implement CSRF tokens on all state-changing forms.
- **[P5.2 - Content Security Policy]** Implement a strict CSP.
- **[P5.3 - GDPR Tooling]** Finalize the GDPR data anonymization script.
- **[P5.4 - Expand Test Coverage]** Write PHPUnit tests for new features.

#### [P6] UI/UX & Visual Polish
- **[P6.1 - Responsive Layout Audit]** Ensure all pages are fully responsive and align with the design reference for mobile, tablet, and desktop.
- **[P6.2 - Dashboard Layout]** Implement the full dashboard layout from the design reference, including the left sidebar, central KPI/task area, and right activity sidebar.
- **[P6.3 - Consistent UI Elements]** Ensure consistent use of icons, color accents, and status badges as seen in the design reference.
- **[P6.4 - Microinteractions]** Implement subtle hover states and animations for key interactive elements (buttons, navigation links, form inputs).
- **[P6.5 - Accessibility Basics]** Audit all pages for readable contrast, font sizes, keyboard navigation, and necessary `alt` text.
- **[P6.6 - Friendly Microcopy]** Review and update all UI text (labels, buttons, empty states) to be friendly, supportive, and clear.

#### [P7] Admin: Analytics
- **[P7.1 - Basic KPIs]** Enhance the admin dashboard to display key performance indicators.
    - **Files:** `admin/index.php` (and potentially new helper functions).
    - **DB Tables:** `users`, `bookings`, `purchases` (SELECT, COUNT, SUM).
    - **Outcome:** The admin dashboard will show at-a-glance statistics for total users, total bookings, and total revenue, as inspired by the design reference.

### Future Steps (Post-MVP / Summer 2026 Roadmap)

#### [P8] Gamification
-   **[P8.1 - Progress System]** Implement a visual progress system (e.g., progress bars or rings) for lesson completion.
    -   **Files:** `dashboard.php`, and a new UI component.
    -   **DB Table:** May require adding a column like `lessons_completed` to the `children` table or a new `progress` table.
    -   **Outcome:** Users see a visual representation of progress, encouraging continued engagement.
-   **[P8.2 - Badges & Achievements]** Create a system for awarding badges for milestones.
    -   **Files:** A new `badges.php` page to view earned badges. New admin interface to manage available badges.
    -   **DB Tables:** Requires new `badges` table (id, name, description, icon_url) and a `user_badges` pivot table (user_id, badge_id, date_earned).
    -   **Outcome:** Users are awarded badges for achievements like "5 Lessons Completed" or "Perfect Attendance," which appear on their profile.
-   **[P8.3 - Celebratory Animations]** Add tasteful animations for key positive events.
    -   **Library:** Alpine.js or a CSS animation library.
    -   **Files:** Triggered from handlers for booking, lesson completion, or earning a badge.
    -   **Outcome:** A confetti animation or other small visual celebration appears when a user completes a significant, positive action.

#### [P9] Advanced Feedback Loops
-   **[P9.1 - Lesson Ratings]** Implement emoji-based or star ratings after a lesson.
    -   **Files:** Add to the `my_bookings.php` (past lessons) view.
    -   **DB Table:** Add a `rating` column to the `lesson_feedback` table.
    -   **Outcome:** Parents can provide quick, simple feedback on a lesson, and data can be aggregated for admins.
-   **[P9.2 - Reflection Prompts & Mood Check-ins]** Add prompts for users to reflect on their learning.
    -   **Files:** New UI modal or page that appears post-lesson or on login.
    -   **DB Table:** Requires a new `reflections` table (id, user_id, lesson_id, prompt_text, response_text, timestamp).
    -   **Outcome:** Users are prompted with questions like "What was one thing you learned today?" to foster metacognition.

#### [P10] iCal Integration
-   **[P10.1 - iCal Import for Teachers]** Add a feature to import lessons from a teacher's iCal URL.
    -   **Files:** A new script in `scripts/` or a feature in the `admin/teachers/` panel.
    -   **Library:** `sabre/vobject`.
    -   **DB Table:** `lessons` (INSERT).
    -   **Outcome:** Admins can automatically populate lesson slots from a teacher's external calendar feed, reducing manual entry.

#### [P11] Internal Messaging System
-   **[P11.1 - Direct Messaging]** Implement a simple direct messaging feature.
    -   **Files:** New pages for an inbox, viewing conversations, and sending messages.
    -   **DB Table:** Requires a new `messages` table (`id`, `sender_id`, `recipient_id`, `subject`, `body`, `read_status`, `timestamp`).
    -   **Outcome:** Parents and Admins/Teachers can communicate securely within the platform.

#### [P12] Community Features
-   **[P12.1 - Resource Sharing]** Build a section for admins to share curated resources.
    -   **Files:** A new front-end section (`resources.php`) and an admin interface (`admin/resources/`).
    -   **DB Table:** Could use the existing `posts` table with a specific category, or a new `resources` table (id, title, url, description).
    -   **Outcome:** A dedicated area where parents can find helpful articles, tips, and other curated content.

#### [P13] Customization
-   **[P13.1 - Customizable Avatars]** Allow users to select a pre-defined avatar.
    -   **Files:** Add an avatar selection UI to the user profile page.
    -   **DB Table:** Add an `avatar_url` column to the `users` table.
    -   **Outcome:** Users can personalize their profile with a playful avatar, increasing engagement.

#### [P14] Advanced Technical
-   **[P14.1 - Advanced Accessibility]** Audit and refactor the entire application to meet WCAG 2.1 AA/AAA standards.
    -   **Files:** All view files.
    -   **Outcome:** The application is highly accessible to users with a wide range of disabilities.
-   **[P14.2 - Comprehensive Testing]** Implement integration and end-to-end tests.
    -   **Files:** New test suites in the `/tests` directory.
    -   **Outcome:** Increased application stability and confidence in deployments.

#### [P15] Expanded Analytics
-   **[P15.1 - Pedagogical Dashboard]** Build a dedicated analytics dashboard for admins.
    -   **Files:** New `admin/analytics.php` page.
    -   **DB Tables:** Heavy use of `bookings`, `lesson_feedback`, `reflections` (SELECT, JOIN, GROUP BY).
    -   **Outcome:** Admins can view charts and reports on pedagogical insights, such as most popular lessons, teacher performance metrics based on feedback, and overall user sentiment trends.

---

## 7. User Roles
-   **Admin**: Manages all site content, users, children, lessons, packages, bookings, and system settings.
-   **Parent**: Manages their own profile and their children's profiles, purchases credits, and books lessons.
-   **Teacher**: Views their schedule and adds feedback/notes for completed lessons.

## 8. Database Schema Overview
- **users**: `id`, `name`, `email`, `password`, `role` (admin, parent, teacher), `euro_balance`, `last_login_at`, `ical_url` (Post-MVP), `quick_actions_order` (JSON), `avatar_url` (Post-MVP)
- **children**: `id`, `user_id` (FK to users), `name`, `date_of_birth`, `notes`
- **packages**: `id`, `name`, `description`, `euro_value`, `price_cents`, `active`
- **purchases**: `id`, `user_id` (FK to users), `package_id` (FK to packages), `stripe_session_id`, `status`
- **lessons**: `id`, `title`, `teacher_id` (FK to users), `start_time`, `end_time`, `capacity`, `credit_cost`
- **bookings**: `id`, `user_id` (FK to users), `lesson_id` (FK to lessons), `status` (pending, confirmed, etc.)
- **lesson_feedback**: `id`, `booking_id`, `teacher_id`, `notes`, `attachment_path`, `rating` (Post-MVP)
- **messages**: (Post-MVP) `id`, `sender_id`, `recipient_id`, `subject`, `body`, `read_status`, `timestamp`
- **reflections**: (Post-MVP) `id`, `user_id`, `lesson_id`, `prompt_text`, `response_text`, `timestamp`
- **badges**: (Post-MVP) `id`, `name`, `description`, `icon_url`
- **user_badges**: (Post-MVP) `user_id`, `badge_id`, `date_earned`
- **pages**: `id`, `slug`, `title`, `page_type`, `content`
- **posts**: `id`, `user_id` (FK to users), `title`, `slug`, `content`, `status`
- **categories**: `id`, `name`, `slug`
- **post_categories**: `post_id`, `category_id`
- **settings**: `setting_key`, `setting_value`
- **login_attempts**: `id`, `ip_address`, `user_id`, `successful`

## 9. Key File Reference
- **Core Logic**: `/app/Database/Database.php`, `/app/Container.php`, `/bootstrap.php`
- **User Auth**: `/login.php`, `/login-handler.php`, `/register.php`, `/register-handler.php`, `/logout.php`
- **Admin UI**: `/admin/header.php`, `/admin/footer.php`, `/admin/index.php`
- **Parent UI**: `/user_header.php`, `/dashboard.php`
- **Stripe**: `/checkout.php`, `/stripe_webhook.php`
- **Booking**: `/book_lesson.php`, `/my_bookings.php`
- **Migrations**: `/scripts/run-migrations.php`

## 10. Coding Conventions
- **PHP (OOP)**: `PascalCase` for classes, `camelCase` for methods (e.g., `findUserById`).
- **PHP (Procedural)**: `snake_case` for variables and functions (e.g., `$user_id`, `get_children()`).
- **Database**: Table and column names are in `snake_case`.
- **Style**: Adhere to PSR-12 for PHP code formatting.

## 11. External Library Policy
- **Principle**: For any task involving an external library (`stripe-php`, `PHPMailer`, `sabre/vobject`), the first step is to consult its current documentation to ensure modern, secure, and non-deprecated code.
- **Workflow**:
    1.  Before writing code, identify the library and the specific feature required.
    2.  Use `resolve_library_id` to get the library's unique ID.
    3.  Use `get_library_docs` with a focused `topic` to retrieve the latest, most relevant examples and API usage.
    4.  Base the implementation on this retrieved, up-to-date documentation.

## 12. Project Workflow
- **Task Management**: The detailed "Next Steps" and "Future Steps" lists in this `STATUS.md` document serve as our primary task list.
- **GitHub Issues**: For official tracking, each major task (e.g., `[P0] Parent: Child Management`) should correspond to a GitHub Issue. Sub-tasks can be checklists within the issue.
- **Commit Messages**: Reference the relevant GitHub Issue in commit messages (e.g., `feat: Implement child creation form (closes #1)`).
- **Branching**: Create a new branch for each task (e.g., `feature/P0.2-create-child-form`).
- **Pull Requests**: Use Pull Requests for code review and merging changes. Ensure PRs reference the relevant issues.
- **`STATUS.md` Updates**: This document will be updated manually. It is excluded from Git tracking via `.gitignore`.