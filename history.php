<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require __DIR__ . '/catalog.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$filters = [
    'all' => 'All',
    'in_progress' => 'In progress',
    'finished' => 'Finished',
];
$filter = (string) ($_GET['filter'] ?? 'all');
if (!isset($filters[$filter])) {
    $filter = 'all';
}

$history = [
    ['book' => 'Introduction to Computing', 'chapter' => 4, 'page' => 35, 'time' => '8:20 PM', 'progress' => 40, 'status' => 'in_progress'],
    ['book' => 'Introduction to Computing', 'chapter' => 1, 'page' => 10, 'time' => '10:10 AM', 'progress' => 12, 'status' => 'in_progress'],
    ['book' => 'Introduction to Computing', 'chapter' => 1, 'page' => 35, 'time' => '9:00 PM', 'progress' => 15, 'status' => 'in_progress'],
    ['book' => 'Introduction to Computing', 'chapter' => 2, 'page' => 1, 'time' => '1:55 PM', 'progress' => 25, 'status' => 'in_progress'],
];

$items = array_values(array_filter($history, fn(array $h): bool => $filter === 'all' || $h['status'] === $filter));

$coverFor = function (string $title) use ($books): array {
    foreach ($books as $book) {
        if ($book['title'] === $title) return $book;
    }
    return ['label' => $title, 'color' => '#1f5f7a'];
};

$activeNav = 'history';
?>
<!DOCTYPE html>
<html lang="en" <?= theme_attrs() ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>History</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">
  <header class="app-header">
    <h2>History</h2>
  </header>

  <div class="filter-row">
    <nav class="filters" aria-label="Filter history">
      <?php foreach ($filters as $key => $label): ?>
        <a href="history.php?filter=<?= e($key) ?>" class="filter<?= $key === $filter ? ' active' : '' ?>"><?= e($label) ?></a>
      <?php endforeach; ?>
    </nav>
    <button type="button" class="clear-btn">Clear all</button>
  </div>

  <main class="app-main history-main">
    <?php if ($items): ?>
      <?php foreach ($items as $h): ?>
        <?php $book = $coverFor($h['book']); ?>
        <article class="hist-item">
          <div class="hist-cover" style="background: linear-gradient(160deg, <?= e($book['color']) ?>, #0b1d2b);">
            <span><?= e($book['label']) ?></span>
          </div>
          <div class="hist-body">
            <h3><?= e($h['book']) ?></h3>
            <p class="hist-meta">Chapter <?= e((string) $h['chapter']) ?> · Page <?= e((string) $h['page']) ?></p>
            <p class="hist-time"><?= e($h['time']) ?></p>
            <div class="progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?= e((string) $h['progress']) ?>">
              <div class="progress-fill" style="width: <?= e((string) $h['progress']) ?>%;"></div>
            </div>
          </div>
          <a href="#" class="play-btn" aria-label="Continue reading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M10 8.5v7l5.5-3.5z" fill="currentColor"/></svg>
          </a>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="empty">No <?= e(strtolower($filters[$filter])) ?> books yet.</p>
    <?php endif; ?>
  </main>

  <?php require __DIR__ . '/partials/bottom_nav.php'; ?>
</div>
</body>
</html>