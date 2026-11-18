<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

// Only logged-in users can see this page.
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Sign out (handled here, so no separate logout file is needed).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_valid()) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    header('Location: login.php');
    exit;
}

$userName = (string) ($_SESSION['user_name'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>
<link rel="stylesheet" href="assets/style.css?v=2">
</head>
<body>
<div class="card">
  <?= brand() ?>

  <p class="center">You are signed in as</p>
  <h2 class="center"><?= e($userName) ?></h2>

  <form method="post" action="dashboard.php">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <button class="submit" type="submit">SIGN OUT</button>
  </form>
</div>
</body>
</html>