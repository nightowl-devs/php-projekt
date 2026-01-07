<?php
require_once __DIR__ . '/../lib/auth.php';
auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) {
    echo "Brak dostępu";
    exit;
}
?><!doctype html>
<html lang="pl">
  <head>
  <meta charset="utf-8">
  <title>Panel Administracyjny</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="/assets/logo.svg">
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header class="site-header sticky top-0 z-50">
  <div class="container">
    <div class="logo">
      <a href="/admin/"><img src="/assets/logo.svg" alt="logo"></a>
      <div>
        <div class="brand-title">Panel Administracyjny</div>
        <div class="muted text-sm">Zarządzanie serwisem</div>
      </div>
    </div>
    <div class="text-sm nav-links">Zalogowany jako <strong class="text-blue-600"><?=htmlspecialchars($user['username'])?></strong> <a href="/logout.php" class="ml-3">Wyloguj</a></div>
  </div>
</header>

<main class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
  <div class="card p-6">
    <div class="mb-4">
      <h1 class="text-2xl font-bold">Witaj w panelu administracyjnym</h1>
      <p class="text-gray-500">Zarządzaj nauczycielami, użytkownikami i opiniami.</p>
    </div>
    <nav class="flex gap-3 mt-4">
      <a href="/admin/teachers.php" class="btn-primary">👨‍🏫 Nauczyciele</a>
      <a href="/admin/users.php" class="btn-primary">👥 Użytkownicy</a>
      <a href="/admin/opinions.php" class="btn-primary">💭 Opinie</a>
      <a href="/" class="px-4 py-2 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 transition">← Powrót</a>
    </nav>
  </div>
</main>
</body>
</html>
