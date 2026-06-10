<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/session.php';

$userModel = new UserModel();

// ================= SIGNUP =================
if (isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) ? $_POST['role'] : 'student';
    // Programme details are only meaningful for students; the recommendation
    // engine uses them to target level-appropriate courses.
    $department = ($role === 'student' && !empty($_POST['department'])) ? trim($_POST['department']) : null;
    $level = ($role === 'student' && !empty($_POST['level'])) ? trim($_POST['level']) : null;

    // Input validation
    $allowed_roles = ['student', 'advisor', 'admin'];
    if (!in_array($role, $allowed_roles, true)) {
        die('Invalid role!');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid email address!');
    }
    if (strlen($password) < 6) {
        die('Password must be at least 6 characters!');
    }
    if ($password !== $confirm_password) {
        die('Passwords do not match!');
    }
    if ($userModel->findByEmail($email)) {
        die('Email already exists!');
    }
    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $ok = $userModel->create($name, $email, $hashed, $role, $department, $level);
    if ($ok) {
        echo "Registration successful! <a href='/frontend/pages/login.php'>Login</a>";
    } else {
        echo "Error: Registration failed.";
    }
}

// ================= LOGIN =================
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $user = $userModel->findByEmail($email);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        // Role-based redirection
        switch ($user['role']) {
            case 'admin':
                header('Location: /frontend/pages/admin_dashboard.php');
                break;
            case 'advisor':
                header('Location: /frontend/pages/advisor_dashboard.php');
                break;
            default:
                header('Location: /frontend/pages/student_dashboard.php');
        }
        exit();
    } else {
        echo 'Invalid credentials!';
    }
}

// ================= LOGOUT =================
if (isset($_GET['logout'])) {
    logout();
    header('Location: /frontend/pages/login.php');
    exit();
}
?>