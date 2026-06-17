<?php
require_once __DIR__ . '/lib/data.php';

$items = get_menu_items();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cascade Coffee Menu</title>
    <link rel="stylesheet" href="styles/main.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="settings-menu">
        <button class="icon-button" type="button" id="settingsToggle" aria-label="Open settings" aria-expanded="false" title="Settings"></button>
        <div class="settings-panel" id="settingsPanel" hidden>
          <label class="theme-switch">
            <span>Theme Switcher</span>
            <input type="checkbox" id="themeToggle" aria-label="Toggle day and night mode" />
          </label>
          <a class="admin-link" href="admin/login.php">Admin Login</a>
        </div>
      </div>

      <a class="brand" href="index.php" aria-label="Cascade Coffee home">
        <img src="assets/cascade-coffee-logo.svg" alt="Cascade Coffee" />
        <span>Cascade Coffee</span>
        <img src="assets/cascade-coffee-logo.svg" alt="" aria-hidden="true" />
      </a>
    </header>

    <main>
      <div class="page-stickers" aria-hidden="true">
        <img src="assets/stickers/coffee-beans.svg" alt="" />
        <img src="assets/stickers/african-pattern-cup.svg" alt="" />
        <img src="assets/stickers/vintage-camera.svg" alt="" />
        <img src="assets/stickers/rotary-phone.svg" alt="" />
        <img src="assets/stickers/chess-cards.svg" alt="" />
      </div>

      <section class="hero">
        <div class="hero-content">
          <p class="eyebrow">Fresh today</p>
          <h1>Cascade Coffee</h1>
          <p class="hero-copy">Browse our available foods and drinks before you order.</p>
        </div>
      </section>

      <section class="menu-section" aria-labelledby="menuTitle">
        <div class="section-heading">
          <p class="eyebrow">Menu</p>
          <h2 id="menuTitle">Available Food & Drinks</h2>
        </div>

        <div class="filters" aria-label="Menu filters">
          <button class="filter-button active" type="button" data-filter="all">All</button>
          <button class="filter-button" type="button" data-filter="Drink">Drinks</button>
          <button class="filter-button" type="button" data-filter="Food">Foods</button>
          <button class="filter-button" type="button" data-filter="Specials">Specials</button>
          <a class="filter-button" href="about.php">About Us</a>
        </div>

        <?php if (count($items) > 0): ?>
          <div class="menu-grid">
            <?php foreach ($items as $item): ?>
              <?php $isAvailable = ($item['available'] ?? true) === true; ?>
              <article class="menu-card <?= !$isAvailable ? 'unavailable' : '' ?>" data-category="<?= e($item['category'] ?? '') ?>">
                <div>
                  <?php if (!$isAvailable): ?>
                    <span class="availability-badge">Not Available</span>
                  <?php endif; ?>
                  <span class="item-category"><?= e($item['category'] ?? 'Item') ?></span>
                  <h3><?= e($item['name'] ?? '') ?></h3>
                  <p><?= e($item['description'] ?? '') ?></p>
                </div>
                <strong><?= e($item['price'] ?? '') ?> ETB</strong>
              </article>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="empty-state">No menu items right now.</p>
        <?php endif; ?>
      </section>
    </main>

    <script src="scripts/main.js"></script>
  </body>
</html>
