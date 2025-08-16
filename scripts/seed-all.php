<?php
// Run migrations + seed pages + seed users/packages/children + seed lessons/bookings
// Usage: php scripts/seed-all.php [--update]

declare(strict_types=1);

define('PROJECT_ROOT', dirname(__DIR__));

$php = escapeshellcmd(PHP_BINARY);
$updateFlag = (in_array('--update', $argv ?? [], true) || in_array('-u', $argv ?? [], true)) ? ' --update' : '';

function run_cmd(string $cmd): void {
    echo "\n> $cmd\n";
    passthru($cmd, $code);
    if ($code !== 0) {
        fwrite(STDERR, "Command failed with exit code $code: $cmd\n");
        exit($code);
    }
}

// Ensure .env is present and DB creds are set
if (!file_exists(PROJECT_ROOT . '/.env')) {
    fwrite(STDERR, "Missing .env in project root. Copy .env.example to .env and fill DB creds.\n");
    exit(1);
}

// Run migrations (baseline-aware)
run_cmd($php . ' ' . escapeshellarg(PROJECT_ROOT . '/scripts/run-migrations.php'));

// Seed pages (optionally update existing)
run_cmd($php . ' ' . escapeshellarg(PROJECT_ROOT . '/create_default_pages.php') . $updateFlag);

// Seed users/packages/children
run_cmd($php . ' ' . escapeshellarg(PROJECT_ROOT . '/scripts/seed-database.php'));

// Seed lessons + bookings
run_cmd($php . ' ' . escapeshellarg(PROJECT_ROOT . '/scripts/seed-lessons-and-bookings.php'));

echo "\nAll seeding complete.\n";

