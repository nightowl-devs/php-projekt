<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) { echo "nie"; exit; }

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
?><!doctype html><html lang="pl"><head><meta charset="utf-8"><title>Teachers</title><link rel="stylesheet" href="/assets/styles.css"></head><body>
<main>
  <h1>Nauczyciele</h1>
  <form method="post" class="add-case">
    <input name="add_name" placeholder="Imię i nazwisko" required>
    <textarea name="add_note" placeholder="Notatka"></textarea>
    <button type="submit">Dodaj nauczyciela</button>
  </form>
  <h2>Lista</h2>
  <?php if ($res && $res->num_rows): ?>
    <ul>
    <?php while ($t = $res->fetch_assoc()): ?>
      <li>
        <strong><?=htmlspecialchars($t['name'])?></strong> — <?=htmlspecialchars($t['created_at'])?>
        <form method="post" style="display:inline">
          <input type="hidden" name="delete_id" value="<?= (int)$t['id'] ?>">
          <button type="submit">Usuń</button>
        </form>
        <div class="note"><?=nl2br(htmlspecialchars($t['note']))?></div>
      </li>
    <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p>Brak nauczycieli.</p>
  <?php endif; ?>
</main></body></html>
