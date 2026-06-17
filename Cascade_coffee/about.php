<?php
require_once __DIR__ . '/lib/data.php';

$about = get_about();
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>About Us | Cascade Coffee</title>
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

      <section class="about-hero">
        <div class="about-hero-content">
          <p class="eyebrow">About Us</p>
          <h1>Cascade Coffee</h1>
          <p class="about-copy"><?= e($about['description'] ?? '') ?></p>
        </div>
      </section>

      <?php if (!empty($about['images'] ?? [])): ?>
        <section class="about-gallery" aria-labelledby="galleryTitle">
          <div class="menu-section">
            <h2 id="galleryTitle">Our Cafe</h2>
            <div class="gallery-grid">
              <?php foreach ($about['images'] as $image): ?>
                <div class="gallery-item">
                  <img src="<?= e($image['url'] ?? '') ?>" alt="<?= e($image['alt'] ?? 'Cafe image') ?>" />
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </section>
      <?php endif; ?>

      <section class="about-socials" aria-labelledby="contactTitle">
        <div class="menu-section">
          <h2 id="contactTitle">Get In Touch</h2>
          
          <div class="contact-info">
            <div class="contact-item">
              <h3>Address</h3>
              <p><?= e($about['address'] ?? '') ?></p>
            </div>
            <div class="contact-item">
              <h3>Phone</h3>
              <p><a href="tel:<?= e($about['phone'] ?? '') ?>"><?= e($about['phone'] ?? '') ?></a></p>
            </div>
          </div>

          <h2 style="margin-top: 32px;">Connect With Us</h2>
          <div class="socials-grid">
            <?php foreach ($about['socials'] ?? [] as $social): ?>
              <a href="<?= e($social['url'] ?? '#') ?>" class="social-card" target="_blank" rel="noopener noreferrer">
                <span class="social-name"><?= e($social['name'] ?? '') ?></span><br>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <div style="text-align: center; padding: 32px 8vw; position: relative; z-index: 2;">
        <a href="index.php" class="button">Back to Menu</a>
      </div>
    </main>

    <script src="scripts/main.js"></script>
  </body>
</html>
