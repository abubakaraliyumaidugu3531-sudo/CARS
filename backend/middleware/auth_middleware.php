<?php
// Middleware for authentication
require_once __DIR__ . '/../helpers/session.php';

function ensure_authenticated() {
    require_login();
}
