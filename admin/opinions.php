<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) { echo "cwel"; exit; }

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
?><!doctype html><html lang="pl"><head><meta charset="utf-8"><title>Opinie</title><link rel="stylesheet" href="/assets/styles.css"></head><body>
<main>
  <h1>Opinie / vouches</h1>
  <?php if ($res && $res->num_rows): ?>
    <ul>
    <?php while ($o = $res->fetch_assoc()): ?>
      <li>
        <strong><?=htmlspecialchars($o['username'] ?? 'anon')?></strong> on <em><?=htmlspecialchars($o['teacher'] ?? '')?></em>: <?=nl2br(htmlspecialchars($o['opinion']))?>
        <form method="post" style="display:inline">
          <button name="delete_op" value="<?= (int)$o['id'] ?>">Usuń</button>
        </form>
      </li>
    <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p>Brak opinii.</p>
  <?php endif; ?>
</main></body></html>
