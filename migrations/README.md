# Migrations

This project uses simple SQL migrations executed by `scripts/run-migrations.php`.

## Baseline
- As of this refactor, a consolidated baseline exists at `100_baseline_schema.sql`.
- When the baseline is present, the migration runner will only apply migrations with numeric prefixes >= 100.
- Earlier historical migrations remain in the repo for reference but are not executed automatically.

## Seeds
- Default settings are seeded in `101_seed_settings.sql`. The runner is idempotent and tolerates duplicates.

## Notes
- The runner treats common "already exists" errors as non-fatal and marks such files as applied. This allows re-runs without failing.
- New migrations should be numbered `102_xxx.sql`, `103_xxx.sql`, etc.
- Keep migrations idempotent where possible (e.g., `IF NOT EXISTS`, `ON DUPLICATE KEY UPDATE`).

