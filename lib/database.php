<?php

$host = getenv('DB_HOST') ?: 'localhost';
$db   = getenv('DB_NAME') ?: 'my_database';
$user = getenv('DB_USER') ?: 'db_user';
$pass = getenv('DB_PASS') ?: 'db_password';

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

    $conn = new mysqli($GLOBALS['host'], $GLOBALS['user'], $GLOBALS['pass'], $GLOBALS['db']);
    if ($conn->connect_errno) {
        error_log('DB connect failed: ' . $conn->connect_error);
        throw new RuntimeException('Database connection error');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

function db_query($sql)
{
    $conn = db_get_conn();
    $result = $conn->query($sql);
    if ($result === false) {
        error_log('DB query error: ' . $conn->error . ' -- SQL: ' . $sql);
        return false;
    }
    return $result;
}

?>