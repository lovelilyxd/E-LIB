<?php
declare(strict_types=1);
require __DIR__ . '/config.php';
require __DIR__ . '/catalog.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$categories = ['Math', 'Computer', 'PathFit', 'Science'];

$query = trim((string) ($_GET['q'] ?? ''));
$category = (string) ($_GET['cat'] ?? '');
if (!in_array($category, $categories, true)) {
    $category = '';
}

$results = array_values(array_filter($books, function (array $book) use ($query, $category): bool {
    if ($category !== '' && $book['category'] !== $category) return false;
    if ($query !== '' && mb_stripos($book['title'], $query) === false) return false;
    return true;
}));

$activeNav = 'search';
?>
<!DOCTYPE html>
<html lang="en" <?= theme_attrs() ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Search</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">
  <header class="search-header">
    <form method="get" action="search.php" class="search-bar" role="search">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><line x1="16.5" y1="16.5" x2="21" y2="21"/></svg>
      <input type="search" name="q" value="<?= e($query) ?>" placeholder="Search for books" aria-label="Search for books">
      <?php if ($category !== ''): ?>
        <input type="hidden" name="cat" value="<?= e($category) ?>">
      <?php endif; ?>
    </form>

    <nav class="chips" aria-label="Categories">
      <?php foreach ($categories as $cat): ?>
        <?php
          $params = array_filter(['q' => $query, 'cat' => $cat === $category ? '' : $cat], fn($v) => $v !== '');
          $href = 'search.php' . ($params ? '?' . http_build_query($params) : '');
        ?>
        <a href="<?= e($href) ?>" class="chip<?= $cat === $category ? ' active' : '' ?>"><?= e($cat) ?></a>
      <?php endforeach; ?>
      <a href="search.php" class="chip chip-more" aria-label="All categories">&#8964;</a>
    </nav>
  </header>

  <main class="app-main">
    <?php if ($results): ?>
      <div class="results">
        <?php foreach ($results as $book): ?>
          <a href="#" class="book">
            <div class="cover" style="background: linear-gradient(160deg, <?= e($book['color']) ?>, #0b1d2b);">
              <span><?= e($book['label']) ?></span>
            </div>
            <p><?= e($book['title']) ?></p>
          </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="empty">No books found<?= $query !== '' ? ' for "' . e($query) . '"' : '' ?>.</p>
    <?php endif; ?>
  </main>

  <?php require __DIR__ . '/partials/bottom_nav.php'; ?>
</div>
</body>
</html>