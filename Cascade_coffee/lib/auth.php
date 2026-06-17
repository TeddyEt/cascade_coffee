<?php
declare(strict_types=1);

require_once __DIR__ . '/data.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin_logged_in(): bool
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function require_admin(): void
{
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function attempt_login(string $username, string $password): bool
{
    $admin = get_admin();

    if (($admin['username'] ?? '') !== $username) {
        return false;
    }

    if (!password_verify($password, $admin['password_hash'] ?? '')) {
        return false;
    }

    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;

    return true;
}

function logout_admin(): void
{
    $_SESSION = [];
    session_destroy();
}
