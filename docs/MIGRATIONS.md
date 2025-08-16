# Migrations

This project uses simple SQL migrations executed by `scripts/run-migrations.php`.

## Baseline
- A consolidated baseline exists at `migrations/100_baseline_schema.sql`.
- When the baseline is present, the runner applies only migrations with numeric prefixes >= 100.
- Earlier historical migrations are archived under `migrations/_archived/` and are not executed automatically.

## Seeds
- Default settings are seeded in `migrations/101_seed_settings.sql`. The runner is idempotent and tolerates duplicates.

## Runner Behavior
- Treats common "already exists" errors as non-fatal and marks such files as applied (idempotent reruns).
- Baseline-aware: filters to 100+ when baseline exists.

## Authoring New Migrations
- Number new files `102_*`, `103_*`, etc.
- Prefer idempotent SQL where reasonable (e.g., `IF NOT EXISTS`, `ON DUPLICATE KEY UPDATE`).

## Seeding & Dev Setup
- Pages: `php create_default_pages.php` (use `--update` to overwrite existing defaults)
- Users/Packages/Children: `php scripts/seed-database.php`
- Lessons + Bookings: `php scripts/seed-lessons-and-bookings.php`
- All-in-one: `php scripts/seed-all.php` (accepts `--update`)

