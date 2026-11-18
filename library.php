<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$tabs = [
    'current' => 'Current Reads',
    'archives' => 'Archives',
    'saves' => 'Saves',
];
$tab = (string) ($_GET['tab'] ?? 'saves');
if (!isset($tabs[$tab])) {
    $tab = 'saves';
}

$saves = [
    [
        'title' => 'Introduction to Computing & ICT 1',
        'label' => 'Introduction to Computing & ICT1',
        'color' => '#1f5f7a',
        'page' => 35,
        'description' => 'Computer science studies how to describe, predict properties of, and efficiently implement information processes. This book introduces the most important ideas in computing using the Scheme and Python ...',
    ],
    [
        'title' => 'Mathematics In The Modern World',
        'label' => 'Mathematics in the Modern World',
        'color' => '#2b4a7a',
        'page' => 40,
        'description' => 'Mathematics is the study of patterns and structure. It is a useful tool for understanding nature and predicting phenomena in the world. Mathematics helps organize many natural patterns, including the movement of stars and weather cycles, patterns on animals...',
    ],
    [
        'title' => 'Discrete Mathematics',
        'label' => 'Discrete and Combinatorial Mathematics',
        'color' => '#1d3557',
        'page' => 40,
        'description' => 'Discrete mathematics is a branch of mathematics that is concerned with "discrete" mathematical structures instead of "continuous" ones. Its mathematical structures include objects with distinct values, like graphs, integers, logic-based statements, etc.',
    ],
    [
        'title' => 'Understanding The Self',
        'label' => 'Understanding The Self',
        'color' => '#7a3b1d',
        'page' => 45,
        'description' => 'This document provides details about a new required General Education course called "Understanding the Self" in the Philippines. The course aims to help students understand concepts of personality, self, and identity by introducing major personality theories...',
    ],
    [
        'title' => 'Data Structure',
        'label' => 'Data Structure',
        'color' => '#2a5a4a',
        'page' => null,
        'description' => 'It provides a comprehensive coverage of all the fundamental data structures: stacks, queues and linked lists, trees, graphs, and the algorithms that operate on them...',
    ],
];

$activeNav = 'library';
?>
<!DOCTYPE html>
<html lang="en" <?= theme_attrs() ?>>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Library</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="app">
  <header class="app-header library-header">
    <h2>Library</h2>
    <div class="app-header-actions">
      <button type="button" class="icon-btn" aria-label="Help">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M9.5 9a2.5 2.5 0 1 1 3.5 2.3c-.7.3-1 .9-1 1.7"/><line x1="12" y1="17" x2="12" y2="17.01"/></svg>
      </button>
      <details class="menu">
        <summary class="icon-btn" aria-label="More options">
          <svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="5" r="2"/><circle cx="12" cy="12" r="2"/><circle cx="12" cy="19" r="2"/></svg>
        </summary>
        <div class="menu-panel">
          <p class="menu-user"><?= e($_SESSION['user_name']) ?></p>
          <form method="post" action="logout.php">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <button type="submit" class="menu-item">Sign out</button>
          </form>
        </div>
      </details>
    </div>
  </header>

  <nav class="lib-tabs" aria-label="Library sections">
    <?php foreach ($tabs as $key => $label): ?>
      <a href="library.php?tab=<?= e($key) ?>" class="lib-tab<?= $key === $tab ? ' active' : '' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </nav>

  <main class="app-main lib-main">
    <?php if ($tab === 'saves'): ?>
      <?php foreach ($saves as $item): ?>
        <a href="#" class="lib-item">
          <div class="lib-cover" style="background: linear-gradient(160deg, <?= e($item['color']) ?>, #0b1d2b);">
            <span><?= e($item['label']) ?></span>
          </div>
          <div class="lib-body">
            <h3><?= e($item['title']) ?></h3>
            <p><?= e($item['description']) ?></p>
            <div class="lib-meta">
              <?php if ($item['page'] !== null): ?>
                <span class="page-badge">Page <?= e((string) $item['page']) ?></span>
                <svg class="bookmark" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M6 3h12v18l-6-4-6 4z"/></svg>
              <?php endif; ?>
            </div>
          </div>
        </a>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="empty">No books in <?= e(strtolower($tabs[$tab])) ?> yet.</p>
    <?php endif; ?>
  </main>

  <?php require __DIR__ . '/partials/bottom_nav.php'; ?>
</div>
</body>
</html>