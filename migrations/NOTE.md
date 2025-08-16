# Migrations (folder note)

For full migration guidance (baseline rules, runner behavior, seeding), see `docs/MIGRATIONS.md`.

Quick reminder:
- When `100_baseline_schema.sql` exists, only `100+` migrations are applied.
- Run: `php scripts/run-migrations.php`
