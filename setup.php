<?php
/**
 * Database Setup Script
 * Run this script once to set up the database
 */

require_once __DIR__ . '/app/bootstrap.php';

use App\Database\Migration;

$migration = new Migration($db);

echo "Setting up database tables...\n\n";

try {
    $migration->runAll();
    echo "✓ Database tables created successfully!\n\n";
    echo "Setup complete. You can now use MyPortfolioPro.\n";
    echo "\nDefault login credentials:\n";
    echo "- Email: admin@example.com\n";
    echo "- Password: admin123\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}
