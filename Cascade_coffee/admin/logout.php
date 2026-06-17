<?php
require_once __DIR__ . '/../lib/auth.php';

logout_admin();
header('Location: login.php');
exit;
