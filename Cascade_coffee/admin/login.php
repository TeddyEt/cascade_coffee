<?php
require_once __DIR__ . '/../lib/auth.php';

if (is_admin_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_text($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    if (attempt_login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Login | Cascade Coffee</title>
    <link rel="stylesheet" href="../styles/main.css" />
  </head>
  <body>
    <main class="login-page">
      <form class="auth-card" method="post">
        <img src="../assets/cascade-coffee-logo.svg" alt="Cascade Coffee" />
        <h1>Admin Login</h1>
        <?php if ($error !== ''): ?>
          <p class="notice error"><?= e($error) ?></p>
        <?php endif; ?>
        <label>
          Username
          <input type="text" name="username" autocomplete="username" required />
        </label>
        <label>
          Password
          <input type="password" name="password" autocomplete="current-password" required />
        </label>
        <button class="button" type="submit">Login</button>
        <a class="text-link" href="../index.php">Back to menu</a>
      </form>
    </main>
    <script src="../scripts/main.js"></script>
  </body>
</html>
