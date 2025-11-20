<?php
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/database.php';

if (session_status() === PHP_SESSION_NONE) {
        session_start();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $u = trim((string)($_POST['username'] ?? ''));
        $p = (string)($_POST['password'] ?? '');
        if ($u === '' || $p === '') {
                $error = 'Proszę podać nazwę użytkownika i hasło.';
        } else {
                $ok = auth_create_user($u, $p);
                if ($ok) {
                        $_SESSION['flash'] = 'Konto utworzone. Możesz się teraz zalogować.';
                        header('Location: /login.php');
                        exit;
                } else {
                        $error = 'Nie udało się utworzyć konta (nazwa może być zajęta).';
                }
        }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
    <main class="login">
        <h1>Zarejestruj się</h1>
        <?php
        if ($error !== '') {
                echo '<p class="error">' . htmlspecialchars($error) . '</p>';
        }
        ?>
        <form method="post" action="/register.php">
            <div>
                <label for="username">Nazwa użytkownika</label><br>
                <input id="username" name="username" required>
            </div>
            <div>
                <label for="password">Hasło</label><br>
                <input id="password" name="password" type="password" required>
            </div>
            <div style="margin-top:.5rem">
                <button type="submit">Utwórz konto</button>
            </div>
        </form>
        <p><a href="/login.php">Powrót do logowania</a></p>
    </main>
</body>
</html>
