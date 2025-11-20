<?php
require_once __DIR__ . '/lib/auth.php';


$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = (string)($_POST['username'] ?? '');
    $p = (string)($_POST['password'] ?? '');
    if (auth_login($u, $p)) {
        header('Location: /');
        exit;
    }
    $error = 'Nieprawidłowe dane';
}

?><!doctype html>
<html lang="pl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Logowanie</title>
  <link rel="stylesheet" href="/assets/styles.css">
  <style> .login form{display:block;margin-bottom:1rem} .login label{display:block;margin-bottom:.25rem;font-size:.9rem;color:#334155} </style>
</head>
<body>
  <main class="login">
    <h1>Logowanie</h1>
    <?php
    if (!empty($_SESSION['flash'])) {
        echo '<div class="flash">' . htmlspecialchars($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
    if ($error !== '') {
        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
    }
    ?>

    <form method="post" action="/login.php">
      <div>
        <label for="username">Nazwa użytkownika</label>
        <input id="username" name="username" placeholder="Nazwa użytkownika" required>
      </div>
      <div>
        <label for="password">Hasło</label>
        <input id="password" name="password" type="password" placeholder="Hasło" required>
      </div>
      <div style="margin-top:.6rem">
        <button type="submit">Zaloguj</button>
      </div>
    </form>

    <div style="margin-top:1rem">
      <p>Nie masz konta? <a href="/register.php">Utwórz konto</a></p>
    </div>
  </main>
</body>
</html>
