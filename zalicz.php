<?php
require_once __DIR__ . '/lib/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /'); exit;
}

auth_require_login('/login.php');
$user = auth_current_user();
if (!$user) { header('Location: /login.php'); exit; }

$girl_id = isset($_POST['girl_id']) ? (int) $_POST['girl_id'] : 0;
if ($girl_id <= 0) { header('Location: /'); exit; }

$conn = db_get_conn();
$stmt = $conn->prepare('INSERT IGNORE INTO girl_user (girl_id, user_id) VALUES (?, ?)');
if (!$stmt) { error_log('zalicz prepare failed: ' . $conn->error); header('Location: /'); exit; }
$stmt->bind_param('ii', $girl_id, $user['id']);
$stmt->execute();
$stmt->close();

header('Location: /');
exit;
