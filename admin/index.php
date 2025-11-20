<?php
require_once __DIR__ . '/../lib/auth.php';
auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) {
    echo "<h1>wypindalaj cwl</h1>";
    exit;
}
?><!doctype html>
<html lang="pl"><head><meta charset="utf-8"><title>Admin</title><link rel="stylesheet" href="/assets/styles.css"></head><body>
<main class="admin">
  <h1>Admin panel</h1>
  <ul>
    <li><a href="/admin/teachers.php">Zarządzaj nauczycielami</a></li>
    <li><a href="/admin/users.php">Zarządzaj użytkownikami</a></li>
    <li><a href="/admin/opinions.php">Zarządzaj opiniami</a></li>
  </ul>
</main>
</body></html>
