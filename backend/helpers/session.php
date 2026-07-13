<?php
// Session helper for secure session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: /cars/frontend/pages/login.php');
        exit;
    }
}

function require_role($role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        header('Location: /cars/frontend/pages/login.php');
        exit;
    }
}

function logout() {
    session_unset();
    session_destroy();
}
?>