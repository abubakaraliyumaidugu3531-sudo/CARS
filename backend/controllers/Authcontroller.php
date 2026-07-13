<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/session.php';

$userModel = new UserModel();

// Helper: redirect back to a page with a flash message.
function auth_redirect($path, $key, $text) {
    header('Location: ' . $path . '?' . $key . '=' . urlencode($text));
    exit();
}

// ================= SIGNUP (students only) =================
if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    // Role is forced to 'student'. Advisors/admins are provisioned by an admin.
    $role = 'student';
    $department = trim($_POST['department'] ?? '') ?: null;
    $level = trim($_POST['level'] ?? '') ?: null;

    $signup = '/cars/frontend/pages/signup.php';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        auth_redirect($signup, 'err', 'Please enter a valid email address.');
    }
    if (strlen($password) < 6) {
        auth_redirect($signup, 'err', 'Password must be at least 6 characters.');
    }
    if ($password !== $confirm_password) {
        auth_redirect($signup, 'err', 'Passwords do not match.');
    }
    if ($userModel->findByEmail($email)) {
        auth_redirect($signup, 'err', 'An account with that email already exists.');
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    if ($userModel->create($name, $email, $hashed, $role, $department, $level)) {
        auth_redirect('/cars/frontend/pages/login.php', 'msg', 'Account created. Please log in.');
    }
    auth_redirect($signup, 'err', 'Registration failed. Please try again.');
}

// ================= LOGIN =================
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user = $userModel->findByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];
        switch ($user['role']) {
            case 'admin':
                header('Location: /cars/frontend/pages/admin_dashboard.php');
                break;
            case 'advisor':
                header('Location: /cars/frontend/pages/advisor_dashboard.php');
                break;
            default:
                header('Location: /cars/frontend/pages/student_dashboard.php');
        }
        exit();
    }
    auth_redirect('/cars/frontend/pages/login.php', 'err', 'Invalid email or password.');
}

// ================= ADMIN: CREATE STAFF ACCOUNT =================
// Only an authenticated admin may mint advisor/admin accounts.
if (isset($_POST['create_staff'])) {
    require_once __DIR__ . '/../middleware/role_middleware.php';
    require_admin();

    $users = '/cars/frontend/pages/admin_users.php';
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? '', ['advisor', 'admin'], true) ? $_POST['role'] : 'advisor';
    $department = trim($_POST['department'] ?? '') ?: null;

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        auth_redirect($users, 'err', 'Provide a name, valid email and a 6+ character password.');
    }
    if ($userModel->findByEmail($email)) {
        auth_redirect($users, 'err', 'An account with that email already exists.');
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    if ($userModel->create($name, $email, $hashed, $role, $department, null)) {
        auth_redirect($users, 'msg', ucfirst($role) . ' account created.');
    }
    auth_redirect($users, 'err', 'Could not create the account.');
}
