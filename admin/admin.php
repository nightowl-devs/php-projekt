<?php
require_once __DIR__ . '/../lib/auth.php';

auth_require_login('/login.php');
$user = auth_current_user();
if (!$user || empty($user['is_admin'])) {
    http_response_code(403);
    echo 'Brak uprawnień';
    exit;
}

$conn = db_get_conn();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_girl') {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name !== '') {
            $stmt = $conn->prepare('INSERT INTO girls (name, description) VALUES (?, ?)');
            $stmt->bind_param('ss', $name, $desc);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: /admin/admin.php'); exit;
    }

    if ($_POST['action'] === 'delete_girl' && isset($_POST['name'])) {
        $name = trim($_POST['name'] ?? '');
        if ($name !== '') {
            $stmt = $conn->prepare('DELETE FROM girls WHERE name = ?');
            $stmt->bind_param('s', $name);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: /admin/admin.php'); exit;
    }
    
    if ($_POST['action'] === 'delete_user' && isset($_POST['user_id'])) {
        $uid = (int) $_POST['user_id'];
        $stmt = $conn->prepare('DELETE FROM users WHERE id = ?');
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $stmt->close();
        header('Location: /admin/admin.php'); exit;
    }
}

$girls = $conn->query('SELECT id, name, description FROM girls ORDER BY id');
$users = $conn->query('SELECT id, username, is_admin FROM users ORDER BY id');

?>
<!doctype html>
<html lang="pl">
<head><meta charset="utf-8"><title>Admin</title></head>
<body>
<h1>Panel admina</h1>
<p><a href="/">Powrót</a></p>

<h2>Dodaj dziewczynę</h2>
<form method="post">
  <input type="hidden" name="action" value="add_girl">
  <label>Nazwa: <input name="name" required></label><br>
  <label>Opis: <input name="description"></label><br>
  <button type="submit">Dodaj</button>
</form>

<h2>Usuń dziewczynę</h2>
<form method="post">
    <input type="hidden" name="action" value="delete_girl">
    <label>Nazwa: <input name="name" required></label><br>
    <button type="submit">Usuń</button>
    
</form>

<h2>Lista dziewczyn</h2>
<ul>
<?php while ($r = $girls->fetch_assoc()) { echo '<li>'.htmlspecialchars($r['name']).' - '.htmlspecialchars($r['description']).'</li>'; } ?>
</ul>

<h2>Użytkownicy</h2>
<ul>
<?php while ($u = $users->fetch_assoc()) {
    echo '<li>'.htmlspecialchars($u['username']).' (admin: '.($u['is_admin'] ? 'tak':'nie').')'
       .'<form method="post" style="display:inline;margin-left:10px;">'
       .'<input type="hidden" name="action" value="delete_user">'
       .'<input type="hidden" name="user_id" value="'.(int)$u['id'].'">'
       .'<button type="submit">Usuń</button></form></li>';
} ?>
</ul>

</body>
</html>
