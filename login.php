<?php
require_once __DIR__ . '/lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (auth_login($username, $password)) {
        header('Location: /'); exit;
    }
    $error = 'Nieprawidłowe dane logowania';
}
?>
<!doctype html>
<html lang="pl">
<head><meta charset="utf-8"><title>Login</title></head>
<body>
<h1>Zaloguj się</h1>
<?php if (!empty($error)) echo '<p style="color:red;">'.htmlspecialchars($error).'</p>'; ?>
<form method="post">
  <label>Username: <input name="username" required></label><br>
  <label>Password: <input name="password" type="password" required></label><br>
  <button type="submit">Zaloguj</button>
</form>
<p>Nie masz konta? <a href="/register.php">Zarejestruj się</a></p>
</body>
</html>
