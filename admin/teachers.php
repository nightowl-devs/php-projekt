<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) { echo "Brak dostępu"; exit; }

$conn = db_get_conn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['add_name'])) {
        $name = trim((string)$_POST['add_name']);
        $note = trim((string)($_POST['add_note'] ?? ''));
        $stmt = $conn->prepare('INSERT INTO teachers (name, note) VALUES (?, ?)');
        if ($stmt) { $stmt->bind_param('ss', $name, $note); $stmt->execute(); $stmt->close(); }
        header('Location: /admin/teachers.php'); exit;
    }
    if (!empty($_POST['delete_id'])) {
        $id = (int)$_POST['delete_id'];
        $stmt = $conn->prepare('DELETE FROM teachers WHERE id = ?');
        if ($stmt) { $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close(); }
        header('Location: /admin/teachers.php'); exit;
    }
}

$res = $conn->query('SELECT id, name, note, created_at FROM teachers ORDER BY id DESC');
?><!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <title>Panel Admin - Nauczyciele</title>
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
        <div class="muted text-sm">Zarządzanie nauczycielami</div>
      </div>
    </div>
    <div class="text-sm nav-links">Zalogowany jako <strong class="text-blue-600"><?=htmlspecialchars($user['username'])?></strong> <a href="/logout.php" class="ml-3">Wyloguj</a></div>
  </div>
</header>

<main class="max-w-7xl mx-auto px-4 lg:px-8 py-8">
  <div class="admin-header">
    <h1>Zarządzaj nauczycielami</h1>
    <nav>
      <a href="/admin/teachers.php">👨‍🏫 Nauczyciele</a>
      <a href="/admin/users.php">👥 Użytkownicy</a>
      <a href="/">← Powrót do listy</a>
    </nav>
  </div>

  <section class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-6 items-start">
    <div>
      <h2 class="text-lg font-semibold">Lista nauczycieli</h2>
      <?php if ($res && $res->num_rows): ?>
        <ul class="space-y-4">
        <?php while ($t = $res->fetch_assoc()): ?>
          <li class="card p-4">
            <div class="flex items-center justify-between">
              <strong class="text-gray-900"><?=htmlspecialchars($t['name'])?></strong>
              <span class="text-xs text-gray-400"><?=htmlspecialchars($t['created_at'])?></span>
            </div>
            <div class="note text-sm text-gray-600 mt-2"><?=nl2br(htmlspecialchars($t['note']))?></div>
            <div class="mt-3 flex justify-end">
              <form method="post">
                <input type="hidden" name="delete_id" value="<?= (int)$t['id'] ?>">
                <button type="submit" class="px-3 py-1 rounded bg-red-600 text-white text-sm hover:bg-red-700 transition">Usuń</button>
              </form>
            </div>
          </li>
        <?php endwhile; ?>
        </ul>
      <?php else: ?>
        <p class="empty">Brak nauczycieli.</p>
      <?php endif; ?>
    </div>

    <aside>
      <form method="post" class="card p-4 space-y-3">
        <h2 class="text-lg font-semibold">Dodaj nauczyciela</h2>
        <input name="add_name" placeholder="Imię i nazwisko" required class="w-full p-2 border rounded bg-gray-50" />
        <textarea name="add_note" placeholder="Opis lub specjalizacja..." class="w-full p-2 border rounded bg-gray-50"></textarea>
        <button type="submit" class="btn-primary w-full py-2">Dodaj nauczyciela</button>
      </form>
    </aside>
  </section>
</main>
</body>
</html>
