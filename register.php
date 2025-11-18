<?php
require_once __DIR__ . '/lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username === '' || $password === '') {
        $error = 'Wypełnij wszystkie pola';
    } else {
        if (auth_create_user($username, $password)) {
            header('Location: /login.php'); exit;
        }
        $error = 'Rejestracja nie powiodła się (użytkownik może już istnieć)';
    }
}
?>
<!doctype html>
<html lang="pl">
<head><meta charset="utf-8"><title>Rejestracja</title></head>
<body>
<h1>Zarejestruj się</h1>
<?php if (!empty($error)) echo '<p style="color:red;">'.htmlspecialchars($error).'</p>'; ?>
<form method="post">
  <label>Username: <input name="username" required></label><br>
  <label>Password: <input name="password" type="password" required></label><br>
  <button type="submit">Zarejestruj</button>
</form>
<p>Masz już konto? <a href="/login.php">Zaloguj się</a></p>
</body>
</html>
