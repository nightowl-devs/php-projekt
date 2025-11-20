<?php

$host = getenv('DB_HOST') ?: 'db';
$db   = (getenv('DB_NAME') ?: getenv('MYSQL_DATABASE')) ?: 'myapp';
$user = (getenv('DB_USER') ?: getenv('MYSQL_USER')) ?: 'myuser';
$pass = (getenv('DB_PASS') ?: getenv('MYSQL_PASSWORD')) ?: 'mypassword';

function db_get_conn()
{
    static $conn = null;
    if ($conn instanceof mysqli) {
        if ($conn->ping()) {
            return $conn;
        }
        $conn->close();
        $conn = null;
    }
    global $db, $user, $pass, $host;
    $conn = new mysqli($host, "myuser", $pass, $db);
    if ($conn->connect_errno) {
        error_log('DB connect failed: ' . $conn->connect_error);
        throw new RuntimeException('Database connection error');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}



?>