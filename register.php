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
                        $_SESSION['flash'] = 'Konto utworzone! Możesz się teraz zalogować.';
                        header('Location: /login.php');
                        exit;
                } else {
                        $error = 'Nie udało się utworzyć konta (nazwa może być zajęta lub za krótka).';
                }
        }
}
?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Rejestracja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
    <main class="max-w-md mx-auto mt-20 p-8 bg-white rounded-lg shadow">
        <h1 class="text-2xl font-semibold mb-4">Utwórz konto</h1>
        <?php
        if ($error !== '') {
            echo '<p class="text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded">' . htmlspecialchars($error) . '</p>';
        }
        ?>
        <form method="post" action="/register.php" class="space-y-4">
            <div>
                <label for="username" class="text-sm font-medium text-gray-700">Nazwa użytkownika</label>
                <input id="username" name="username" placeholder="Wybierz unikalną nazwę" required class="mt-1 block w-full p-2 border rounded bg-gray-50" />
            </div>
            <div>
                <label for="password" class="text-sm font-medium text-gray-700">Hasło</label>
                <input id="password" name="password" type="password" placeholder="Wpisz bezpieczne hasło" required class="mt-1 block w-full p-2 border rounded bg-gray-50" />
            </div>
            <div>
                <input type="submit" value="Zarejestruj się" class="w-full py-2 px-4 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700" />
            </div>
        </form>
        <div class="mt-6 pt-4 border-t">
            <p class="text-sm text-center">Masz już konto? <a href="/login.php" class="text-blue-600 font-semibold">Zaloguj się tutaj</a></p>
        </div>
    </main>
</body>
</html>
