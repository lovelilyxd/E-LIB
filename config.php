<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_NAME = 'auth_demo';
const DB_USER = 'root';
const HOME_PAGE = 'library.php';
const DB_PASS = '';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure'   => !empty($_SERVER['HTTPS']),
]);
session_start();

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && hash_equals($_SESSION['csrf'], (string) $_POST['csrf']);
}

function theme_attrs(): string
{
    $theme = (string) ($_COOKIE['theme'] ?? 'dark');
    if (!in_array($theme, ['light', 'dark'], true)) {
        $theme = 'dark';
    }
    if (($_COOKIE['time_adaptive'] ?? '') === '1') {
        $hour = (int) date('G');
        $theme = ($hour >= 18 || $hour < 6) ? 'dark' : 'light';
    } elseif (($_COOKIE['light_adaptive'] ?? '') === '1') {
        $theme = 'auto';
    }
    $pure = $theme === 'dark' && ($_COOKIE['pure_black'] ?? '') === '1';
    return 'data-theme="' . $theme . '"' . ($pure ? ' data-pure="1"' : '');
}

function brand(): string
{
    return '<div class="brand">'
        . '<div class="logo"><svg viewBox="0 0 24 24" fill="none" stroke="#08202f" stroke-width="1.6" '
        . 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . '<path d="M12 6.5C10.3 5.3 8 4.8 4 5v13c4-.2 6.3.3 8 1.5 1.7-1.2 4-1.7 8-1.5V5c-4-.2-6.3.3-8 1.5z"/>'
        . '<path d="M12 6.5v13"/></svg></div>'
        . '<h1>WELCOME</h1>'
        . '<p class="tagline">READ, LEARN, VISUALIZE</p>'
        . '</div>';
}