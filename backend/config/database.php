<?php
// Secure, reusable database connection

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'course_advisory_system');

function get_db_connection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        // Log error in production
        die('Database connection failed.');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

// Usage: $conn = get_db_connection();
?>