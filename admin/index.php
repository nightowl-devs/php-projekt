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
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header class="bg-white border-b sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
    <h1 class="text-xl font-semibold">Panel Administracyjny</h1>
    <div class="text-sm text-gray-500">Zalogowany jako <strong class="text-blue-600"><?=htmlspecialchars($user['username'])?></strong> <a href="/logout.php" class="text-gray-700 ml-3">Wyloguj</a></div>
  </div>
</header>

<main class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
  <div class="bg-white rounded-md shadow p-6">
    <div class="mb-4">
      <h1 class="text-2xl font-bold">Witaj w panelu administracyjnym</h1>
      <p class="text-gray-500">Zarządzaj nauczycielami, użytkownikami i opiniami.</p>
    </div>
    <nav class="flex gap-3 mt-4">
      <a href="/admin/teachers.php" class="px-4 py-2 rounded bg-gray-100 text-gray-700">👨‍🏫 Nauczyciele</a>
      <a href="/admin/users.php" class="px-4 py-2 rounded bg-gray-100 text-gray-700">👥 Użytkownicy</a>
      <a href="/admin/opinions.php" class="px-4 py-2 rounded bg-gray-100 text-gray-700">💭 Opinie</a>
      <a href="/" class="px-4 py-2 rounded bg-gray-50 text-gray-700">← Powrót</a>
    </nav>
  </div>
</main>
</body>
</html>
