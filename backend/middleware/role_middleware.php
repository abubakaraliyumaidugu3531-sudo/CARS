<?php
// Middleware for role-based access
require_once __DIR__ . '/../helpers/session.php';

function require_admin() {
    require_role('admin');
}
function require_advisor() {
    require_role('advisor');
}
function require_student() {
    require_role('student');
}
