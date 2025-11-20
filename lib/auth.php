<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php';

function auth_is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function auth_current_user(): ?array
{
    if (!auth_is_logged_in()) {
        return null;
    }
    if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
        return $_SESSION['user'];
    }
    $conn = db_get_conn();
    $id = (int) $_SESSION['user_id'];
    $stmt = $conn->prepare('SELECT id, username, IFNULL(is_admin,0) FROM users WHERE id = ? LIMIT 1');
    if (!$stmt) {
        error_log('auth_current_user prepare failed: ' . $conn->error);
        return null;
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($uid, $username, $is_admin);
    if ($stmt->fetch()) {
        $user = ['id' => $uid, 'username' => $username, 'is_admin' => (bool) $is_admin];
        $_SESSION['user'] = $user;
        $stmt->close();
        return $user;
    }
    $stmt->close();
    return null;
}

function auth_login(string $username, string $password): bool
{
    $conn = db_get_conn();
    $stmt = $conn->prepare('SELECT id, password_hash, is_admin FROM users WHERE username = ? LIMIT 1');
    if (!$stmt) {
        error_log('auth_login prepare failed: ' . $conn->error);
        return false;
    }
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->bind_result($id, $hash, $is_admin);
    if (!$stmt->fetch()) {
        $stmt->close();
        return false;
    }
    $stmt->close();
    if (empty($hash) || !password_verify($password, $hash)) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $id;
    $_SESSION['user'] = ['id' => (int) $id, 'username' => $username, 'is_admin'=> (bool) $is_admin];
    return true;
}

function auth_logout(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
    session_unset ();
}

function auth_require_login(string $redirect = '/login.php'): void
{
    if (!auth_is_logged_in()) {
        header('Location: ' . $redirect);
        exit;
    }
}

function auth_create_user(string $username, string $password): bool
{
    $conn = db_get_conn();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (username, password_hash) VALUES (?, ?)');
    if (!$stmt) {
        error_log('auth_create_user prepare failed: ' . $conn->error);
        return false;
    }
    $stmt->bind_param('ss', $username, $hash);
    $res = $stmt->execute();
    if (!$res) {
        error_log('auth_create_user execute failed: ' . $stmt->error);
        $stmt->close();
        return false;
    }
    $stmt->close();
    return true;
}

?>