<?php
// TEMPORARY setup page. Run it once, then delete it.
declare(strict_types=1);
require __DIR__ . '/config.php';
header('Content-Type: text/plain; charset=utf-8');

try {
    // 1. Connect to MySQL and create the database if it is missing.
    $server = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $server->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4');
    echo "Database '" . DB_NAME . "': OK\n";

    // 2. Recreate the users table with the exact columns the site uses.
    $pdo = db();
    $pdo->exec('DROP TABLE IF EXISTS users');
    $pdo->exec('CREATE TABLE users (
        id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        full_name     VARCHAR(50)  NOT NULL,
        student_id    CHAR(8)      NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
    )');
    echo "Table 'users': created\n\nColumns:\n";

    // 3. Show the columns so you can see they exist.
    foreach ($pdo->query('SHOW COLUMNS FROM users') as $c) {
        echo '  - ' . $c['Field'] . ' (' . $c['Type'] . ")\n";
    }

    echo "\nDONE. Now go to signup.php and create an account.\n";
    echo "Then delete setup.php and check.php.\n";
} catch (PDOException $ex) {
    echo 'FAILED: ' . $ex->getMessage() . "\n";
}