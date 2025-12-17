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
    $error = 'Nieprawidłowe dane logowania';
}

?><!doctype html>
<html lang="pl">
  <head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Logowanie</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
  <main class="max-w-md mx-auto mt-20 p-8 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4">Logowanie</h1>
    <?php
    if (!empty($_SESSION['flash'])) {
      echo '<div class="bg-green-50 border border-green-200 text-green-700 px-3 py-2 rounded mb-4">' . htmlspecialchars($_SESSION['flash']) . '</div>';
        unset($_SESSION['flash']);
    }
    if ($error !== '') {
      echo '<p class="text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded">' . htmlspecialchars($error) . '</p>';
    }
    ?>

    <form method="post" action="/login.php" class="space-y-4">
      <div>
        <label for="username" class="text-sm font-medium text-gray-700">Nazwa użytkownika</label>
        <input id="username" name="username" placeholder="Wpisz nazwę użytkownika" required class="mt-1 block w-full p-2 border rounded bg-gray-50" />
      </div>
      <div>
        <label for="password" class="text-sm font-medium text-gray-700">Hasło</label>
        <input id="password" name="password" type="password" placeholder="Wpisz hasło" required class="mt-1 block w-full p-2 border rounded bg-gray-50" />
      </div>
      <div>
        <input type="submit" value="Zaloguj się" class="w-full py-2 px-4 bg-blue-600 text-white rounded font-semibold hover:bg-blue-700" />
      </div>
    </form>

    <div class="mt-6 pt-4 border-t">
      <p class="text-sm text-center">Nie masz konta? <a href="/register.php" class="text-blue-600 font-semibold">Zarejestruj się tutaj</a></p>
    </div>
  </main>
</body>
</html>
