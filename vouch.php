<?php
require_once __DIR__ . '/lib/auth.php';
require_once __DIR__ . '/lib/database.php';

if (!auth_is_logged_in()) {
    header('Location: /login.php');
    exit;
}

$user = auth_current_user();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit;
}

$teacher_id = isset($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : 0;
$opinion = trim((string)($_POST['opinion'] ?? ''));

if ($teacher_id <= 0) {
    header('Location: /');
    exit;
}
if ($opinion === '') {
    header('Location: /#teacher-' . $teacher_id);
    $_SESSION['flash'] = 'Opinia nie może być pusta.';
    exit;
}

$conn = db_get_conn();
$stmt = $conn->prepare('INSERT INTO vouches (teacher_id, user_id, opinion, is_anonymous) VALUES (?, ?, ?, ?)');
if ($stmt) {
    $uid = (int)$user['id'];
    $is_anonymous = isset($_POST['is_anonymous']) ? (int)$_POST['is_anonymous'] : 0;
    $stmt->bind_param('iisi', $teacher_id, $uid, $opinion, $is_anonymous);
    $stmt->execute();   
    $stmt->close();
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = 'Dziękujemy za opinię.';
}

header('Location: /#teacher-' . $teacher_id);
exit;
