<?php
// TEMPORARY diagnostic page. Delete it when you're done.
declare(strict_types=1);
require __DIR__ . '/config.php';
header('Content-Type: text/plain; charset=utf-8');

function line(string $label, string $value): void { echo str_pad($label, 28) . $value . "\n"; }

echo "=== DATABASE CHECK ===\n\n";
line('Configured DB name:', DB_NAME);
line('Configured DB user:', DB_USER);

// 1. Can we reach MySQL at all?
try {
    $server = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    line('MySQL server:', 'OK (connected)');
} catch (PDOException $ex) {
    line('MySQL server:', 'FAILED -> ' . $ex->getMessage());
    echo "\nFix: start MySQL in the XAMPP control panel, then reload.\n";
    exit;
}

// 2. Which databases exist?
$dbs = $server->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN);
line('Databases found:', implode(', ', $dbs));
if (!in_array(DB_NAME, $dbs, true)) {
    line('Database exists:', 'NO -> "' . DB_NAME . '" is not in the list above');
    if (isset($_GET['create'])) {
        $server->exec('CREATE DATABASE `' . DB_NAME . '` CHARACTER SET utf8mb4');
        echo "\nCreated database. Reload this page (without ?create=1).\n";
    } else {
        echo "\nFix A: change DB_NAME in config.php to your real database name from the list.\n";
        echo "Fix B: open check.php?create=1 to create an empty '" . DB_NAME . "' database.\n";
    }
    exit;
}
line('Database exists:', 'YES');

// 3. Does the users table exist, and what columns does it have?
try {
    $pdo = db();
    $cols = $pdo->query('SHOW COLUMNS FROM users')->fetchAll();
} catch (PDOException $ex) {
    line('users table:', 'MISSING -> ' . $ex->getMessage());
    if (isset($_GET['create'])) {
        $pdo = db();
        $pdo->exec('CREATE TABLE users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            full_name VARCHAR(50) NOT NULL,
            student_id CHAR(8) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )');
        echo "\nCreated users table. Reload this page (without ?create=1).\n";
    } else {
        echo "\nFix: open check.php?create=1 to create the users table.\n";
    }
    exit;
}
line('users table:', 'YES');
echo "\nColumns in your users table:\n";
foreach ($cols as $c) {
    echo '  - ' . str_pad($c['Field'], 16) . str_pad($c['Type'], 16)
       . ($c['Null'] === 'NO' ? 'NOT NULL' : 'null ok')
       . ($c['Default'] === null && $c['Null'] === 'NO' && $c['Extra'] === '' ? '   <-- no default' : '')
       . "\n";
}

// 4. Try the real insert, then undo it.
echo "\nTest insert (rolled back, nothing is saved):\n";
try {
    $pdo->beginTransaction();
    $stmt = $pdo->prepare('INSERT INTO users (full_name, student_id, password_hash) VALUES (:n, :s, :h)');
    $stmt->execute([':n' => 'Test User', ':s' => '99999999', ':h' => password_hash('Test@1234', PASSWORD_DEFAULT)]);
    echo "  RESULT: OK - the insert works. The problem is not the database.\n";
} catch (PDOException $ex) {
    echo '  RESULT: FAILED -> ' . $ex->getMessage() . "\n";
}
if ($pdo->inTransaction()) $pdo->rollBack();