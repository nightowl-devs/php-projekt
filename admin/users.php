<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) { echo "Brak dostępu"; exit; }

$conn = db_get_conn();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_user'])) {
    $id = (int)$_POST['delete_user'];
    $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
    if ($stmt) { $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close(); }
    header('Location: /admin/users.php'); exit;
}

$res = $conn->query('SELECT id, username, is_admin, created_at FROM users ORDER BY id DESC');
?><!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Panel Admin - Użytkownicy</title>
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
        <div class="muted text-sm">Zarządzanie użytkownikami</div>
      </div>
    </div>
    <div class="text-sm nav-links">Zalogowany jako <strong class="text-blue-600"><?=htmlspecialchars($user['username'])?></strong> <a href="/logout.php" class="ml-3">Wyloguj</a></div>
  </div>
</header>

<main class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
  <div class="admin-header">
    <h1>Zarządzaj użytkownikami</h1>
    <nav>
      <a href="/admin/teachers.php">👨‍🏫 Nauczyciele</a>
      <a href="/admin/users.php">👥 Użytkownicy</a>
      <a href="/">← Powrót do listy</a>
    </nav>
  </div>

  <?php if ($res && $res->num_rows): ?>
    <div class="overflow-x-auto bg-white border rounded-md">
      <table class="min-w-full divide-y divide-gray-100">
      <thead class="bg-gray-50">
        <tr>
          <th class="p-3 w-16 text-left">ID</th>
          <th class="p-3 text-left">Nazwa użytkownika</th>
          <th class="p-3 w-20 text-left">Admin</th>
          <th class="p-3 text-left">Data rejestracji</th>
          <th class="p-3 w-24 text-left">Akcja</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-100">
      <?php while ($u = $res->fetch_assoc()): ?>
        <tr>
          <td class="p-3"><?= (int)$u['id'] ?></td>
          <td class="p-3">
            <strong class="text-gray-900"><?= htmlspecialchars($u['username']) ?></strong>
            <?php if ($u['is_admin']): ?>
              <span class="inline-block bg-blue-600 text-white px-2 py-0.5 text-xs font-semibold rounded ml-2">ADMIN</span>
            <?php endif; ?>
          </td>
          <td class="p-3"><?= $u['is_admin'] ? '✓' : '✗' ?></td>
          <td class="p-3"><?= htmlspecialchars($u['created_at']) ?></td>
          <td class="p-3">
            <form method="post" class="inline-block">
              <button name="delete_user" value="<?= (int)$u['id'] ?>" class="px-3 py-1 rounded bg-red-600 text-white text-sm hover:bg-red-700 transition">Usuń</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p class="empty">Brak użytkowników.</p>
  <?php endif; ?>
</main>
</body>
</html>
