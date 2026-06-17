<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();

$admin = get_admin();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_text($_POST['username'] ?? '');
    $currentPassword = (string) ($_POST['current_password'] ?? '');
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($username === '') {
        $error = 'Username is required.';
    } elseif (!password_verify($currentPassword, $admin['password_hash'] ?? '')) {
        $error = 'Current password is incorrect.';
    } elseif ($newPassword !== '' && $newPassword !== $confirmPassword) {
        $error = 'New passwords do not match.';
    } else {
        $admin['username'] = $username;
        if ($newPassword !== '') {
            $admin['password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        save_admin($admin);
        $_SESSION['admin_username'] = $username;
        $message = 'Account updated.';
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Account Settings | Cascade Coffee</title>
    <link rel="stylesheet" href="../styles/main.css" />
  </head>
  <body>
    <header class="admin-header">
      <a class="mini-brand" href="../index.php">
        <img src="../assets/cascade-coffee-logo.svg" alt="Cascade Coffee" />
      </a>
      <nav>
        <a href="dashboard.php">Menu</a>
        <a href="about.php">About Us</a>
        <a href="account.php">Account</a>
        <a href="logout.php">Logout</a>
      </nav>
    </header>

    <main class="admin-shell">
      <div class="admin-title">
        <p class="eyebrow">Admin</p>
        <h1>Manage Account</h1>
        <p>Change your username or password for the admin area.</p>
      </div>

      <section class="admin-panel narrow" aria-labelledby="accountTitle">
        <h2 id="accountTitle">Account Settings</h2>
        <?php if ($message !== ''): ?>
          <p class="notice success"><?= e($message) ?></p>
        <?php endif; ?>
        <?php if ($error !== ''): ?>
          <p class="notice error"><?= e($error) ?></p>
        <?php endif; ?>
        <form class="item-form single" method="post">
          <label>
            Username
            <input type="text" name="username" value="<?= e($admin['username'] ?? '') ?>" required />
          </label>
          <label>
            Current password
            <input type="password" name="current_password" autocomplete="current-password" required />
          </label>
          <label>
            New password
            <input type="password" name="new_password" autocomplete="new-password" />
          </label>
          <label>
            Confirm new password
            <input type="password" name="confirm_password" autocomplete="new-password" />
          </label>
          <button class="button" type="submit">Save Account</button>
        </form>
      </section>
    </main>
    <script src="../scripts/main.js"></script>
  </body>
</html>
