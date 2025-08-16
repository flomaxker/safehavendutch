# CONTENT_STRUCTURE.md — Safe Haven Dutch CMS (Content & Routes)

_Last updated: 2025-08-16_

## 1) Purpose & Scope
This document explains **where content lives**, **which files render it**, and **which paths are writable** at runtime. It replaces any static-site assumptions and reflects the current PHP CMS architecture.

---

## 2) Public PHP Pages & Routes (read-only code)
These are versioned in Git and should not be written to at runtime.

- **Auth & entry**
  - `/login.php`, `/register.php`, `/logout.php`
  - `/dashboard.php` (parent dashboard)
- **Parent flows (read-only children)**
  - `/my_children.php` (lists a parent’s children; **no CRUD**)
  - `/book_lesson.php`, `/my_bookings.php`
- **Content pages**
  - `/index.php` (home), `/about.php` (if present)
  - Blog: `/blog.php` (list), `/post.php?slug=...` (detail)
- **Stripe / Webhooks**
  - `/checkout.php`, `/stripe_webhook.php`

> All pages must `require_once __DIR__ . '/bootstrap.php';` and follow project conventions (PSR-12, Database wrapper).

---

## 3) Admin Area (read-only code; writes via handlers)
Located under `/admin/`. Admin-only access is enforced per page.

- **Children (intake-driven)**
  - `admin/children/index.php` — list/search/paginate
  - `admin/children/create.php` + `create_handler.php`
  - `admin/children/edit.php` + `update_handler.php`
  - `admin/children/archive_handler.php` (soft-archive)
- **Lessons & Teachers (Roadmap P1)**
  - `admin/lessons/*`, `admin/teachers/*`
- **Dashboard & Analytics**
  - `admin/index.php` (KPIs), future analytics pages

---

## 4) Source of Truth (Database)
Primary content is **database-driven**. Key tables (excerpt):

- `users (id, name, email, role, euro_balance, quick_actions_order JSON, ical_url, last_login_at, ...)`
- `children (id, user_id, name, date_of_birth, notes, audit_log, [status])`
- `lessons (id, teacher_id, title, start_time, end_time, capacity, credit_cost)`
- `bookings (id, user_id, lesson_id, child_id, status)`
- `lesson_feedback (id, booking_id, teacher_id, notes, attachment_path)`
- `pages` — rich CMS fields: `slug`, `title`, `content`, hero/about/feature fields, `show_contact_form`, `show_packages`, `page_type`, etc.
- `posts (id, user_id, title, slug, content, status)`
- `categories (id, name, slug)` + `post_categories (post_id, category_id)`
- `packages (id, name, euro_value, price_cents, active)`, `purchases (user_id, package_id, stripe_session_id, amount_cents, status)`
- `login_attempts (id, ip_address, attempted_at, successful, user_id)`

> **Blog & pages are rendered from the DB.**  
> Any legacy files under `content/` are considered **seed/legacy** only and are not the live source.

---

## 5) Writable vs Read-Only Paths

### Writable at runtime (by PHP)
- `uploads/`  
  - Stores user/teacher attachments (e.g., `lesson_feedback.attachment_path`) and other uploaded assets.
  - **Security hardening:**
    - Add webserver rule to **deny script execution** (e.g., `.htaccess` with `php_flag engine off` or equivalent server config).
    - Generate **randomized filenames**; store original name in DB if needed.
    - **MIME/type validation** and size limits; reject executables.
    - Keep `uploads/` in `.gitignore`.

*(If you use subfolders like `uploads/lesson_files/`, document them here.)*

### Read-only (versioned)
- `/app/` (Database wrapper, services)
- `/admin/` (admin UI & handlers)
- Root PHP pages (`/dashboard.php`, etc.)
- `/assets/` (static assets), `/vendor/`, `/tests/`, `/docs/`

---

## 6) Parent/Child Data Policy
- **Children are created/edited by Admin only** (intake).  
- **Parents see a read-only list** on `/my_children.php` and can submit a **change request** (email or request queue).
- **No child PII in filesystem content folders.** All child data resides in the DB.

---

## 7) Blog Content Pipeline
- **Authoring:** Admins/editors create posts via CMS (TinyMCE → `posts.content`).  
- **Storage:** Posts, categories, and mappings live in DB tables listed above.  
- **Legacy:** Any files under `content/blog/` serve as optional seeds or imports; they are **not** the live source unless explicitly imported.

---

## 8) Conventions & Includes
- All pages include `bootstrap.php` (sessions, `$db`, config).  
- Use the `App\Database\Database` wrapper (prepared statements only).  
- Follow PSR-12; procedural page scripts use `snake_case` variables.  
- Admin pages should include `admin/header.php` and `admin/footer.php` (or current admin layout includes).

---

## 9) Deployment & Permissions
- Webserver user must have **write** access to `uploads/`; all other code paths remain **read-only**.  
- Environment config via `.env` (copy from `.env.example`), not committed.  
- Run database migrations via `php scripts/run-migrations.php`.
  - Baseline: when `migrations/100_baseline_schema.sql` exists, runner applies only `100+` migrations and tolerates common duplicate/exists errors (idempotent).
- Seed demo content (optional):
  - Pages: `php create_default_pages.php`
  - Users/Packages/Children: `php scripts/seed-database.php`
  - Lessons + Bookings: `php scripts/seed-lessons-and-bookings.php`

---

## 10) Related Documentation
- See `/docs/PROJECT_OVERVIEW.md` for architecture & roles.  
- See `/docs/STATUS.md` for the active roadmap (P0–P… tasks).  
- See `/docs/DEVELOPMENT_SESSION.md` for the current coding session plan.

---

## 11) Change Log (brief)
- **2025-08-16:** Convert document to PHP CMS model; clarify DB-first blog, admin-only child CRUD, uploads hardening, and read-only parent children page.
- **2025-08-16:** Update DB excerpts (children.audit_log, bookings.child_id, pages rich fields) and document baseline migrations + seed scripts.
