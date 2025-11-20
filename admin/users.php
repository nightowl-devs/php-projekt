<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) { echo "gryz go golden"; exit; }

$conn = db_get_conn();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete_user'])) {
    $id = (int)$_POST['delete_user'];
    $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
    if ($stmt) { $stmt->bind_param('i', $id); $stmt->execute(); $stmt->close(); }
    header('Location: /admin/users.php'); exit;
}

$res = $conn->query('SELECT id, username, is_admin, created_at FROM users ORDER BY id DESC');
?><!doctype html><html lang="pl"><head><meta charset="utf-8"><title>Users</title><link rel="stylesheet" href="../assets/styles.css">

</head><body>
<main>
  <h1>Użytkownicy</h1>
  <?php if ($res && $res->num_rows): ?>
    <table>
      <thead><tr><th>ID</th><th>username</th><th>admin</th><th>created</th><th></th></tr></thead>
      <tbody>
      <?php while ($u = $res->fetch_assoc()): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= $u['is_admin'] ? 'pewnie taaaaaa' : 'pewnie nieeeee' ?></td>
          <td><?= htmlspecialchars($u['created_at']) ?></td>
          <td>
            <form method="post" style="display:inline">
              <button name="delete_user" value="<?= (int)$u['id'] ?>">Usuń</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>Brak użytkowników.</p>
  <?php endif; ?>
</main></body></html>
