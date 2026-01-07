<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) { echo "Brak dostępu"; exit; }

$conn = db_get_conn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['delete_op'])) {
        $id = (int)$_POST['delete_op'];
        $stmt = $conn->prepare('DELETE FROM vouches WHERE id = ?');
        if ($stmt) { $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close(); }
        header('Location: /admin/opinions.php'); exit;
    }
}

$res = $conn->query('SELECT v.id, v.opinion, v.created_at, u.username, t.name AS teacher FROM vouches v LEFT JOIN users u ON u.id = v.user_id LEFT JOIN teachers t ON t.id = v.teacher_id ORDER BY v.created_at DESC');
?><!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Panel Admin - Opinie</title>
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
        <div class="muted text-sm">Zarządzanie opiniami</div>
      </div>
    </div>
    <div class="text-sm nav-links">Zalogowany jako <strong class="text-blue-600"><?=htmlspecialchars($user['username'])?></strong> <a href="/logout.php" class="ml-3">Wyloguj</a></div>
  </div>
</header>

<main class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
  <div class="admin-header">
    <h1>Zarządzaj opiniami</h1>
    <nav>
      <a href="/admin/teachers.php">👨‍🏫 Nauczyciele</a>
      <a href="/admin/users.php">👥 Użytkownicy</a>
      <a href="/admin/opinions.php">💭 Opinie</a>
      <a href="/">← Powrót do listy</a>
    </nav>
  </div>

  <?php if ($res && $res->num_rows): ?>
    <ul class="space-y-4">
    <?php while ($o = $res->fetch_assoc()): ?>
      <li class="card p-4">
        <div class="flex justify-between items-start gap-4">
          <div class="flex-1">
            <div class="flex items-center gap-2">
              <strong class="text-gray-900"><?=htmlspecialchars($o['username'] ?? 'Anonimowy')?></strong>
              <span class="text-gray-400 text-sm"> o </span>
              <em class="text-gray-700"><?=htmlspecialchars($o['teacher'] ?? 'Brak')?></em>
            </div>
            <p class="text-sm text-gray-600 mt-2"><?=nl2br(htmlspecialchars($o['opinion']))?></p>
            <div class="text-xs text-gray-400 mt-2"><?=htmlspecialchars($o['created_at'])?></div>
          </div>
          <form method="post" class="inline-block flex-shrink-0">
            <button name="delete_op" value="<?= (int)$o['id'] ?>" class="px-3 py-1 rounded bg-red-600 text-white text-sm hover:bg-red-700 transition">Usuń</button>
          </form>
        </div>
      </li>
    <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="empty">Brak opinii.</p>
  <?php endif; ?>
</main>
</body>
</html>
