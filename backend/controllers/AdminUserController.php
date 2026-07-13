<?php
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../middleware/role_middleware.php';
require_once __DIR__ . '/../models/UserModel.php';

require_login();
require_admin();

$userModel = new UserModel();

// ================= ADMIN: EDIT USER =================
if (isset($_POST['edit_user'])) {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = in_array($_POST['role'] ?? '', ['student', 'advisor', 'admin'], true) ? $_POST['role'] : 'student';
    $department = trim($_POST['department'] ?? '') ?: null;
    $level = trim($_POST['level'] ?? '') ?: null;

    $users_page = '/cars/frontend/pages/admin_users.php';

    if (!$user_id || !$name || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header('Location: ' . $users_page . '?err=' . urlencode('Provide a valid user ID, name, and email.'));
        exit;
    }

    if ($userModel->emailExists($email, $user_id)) {
        header('Location: ' . $users_page . '?err=' . urlencode('An account with that email already exists.'));
        exit;
    }

    if ($userModel->update($user_id, $name, $email, $role, $department, $level)) {
        header('Location: ' . $users_page . '?msg=' . urlencode('User updated successfully.'));
        exit;
    }
    header('Location: ' . $users_page . '?err=' . urlencode('Could not update user.'));
    exit;
}

// ================= ADMIN: DELETE USER =================
if (isset($_POST['delete_user'])) {
    $user_id = (int)($_POST['user_id'] ?? 0);
    $users_page = '/cars/frontend/pages/admin_users.php';

    if (!$user_id) {
        header('Location: ' . $users_page . '?err=' . urlencode('Invalid user ID.'));
        exit;
    }

    // Prevent self-deletion
    if ($user_id === $_SESSION['user_id']) {
        header('Location: ' . $users_page . '?err=' . urlencode('Cannot delete your own account.'));
        exit;
    }

    if ($userModel->delete($user_id)) {
        header('Location: ' . $users_page . '?msg=' . urlencode('User deleted successfully.'));
        exit;
    }
    header('Location: ' . $users_page . '?err=' . urlencode('Could not delete user.'));
    exit;
}

// ================= ADMIN: SEARCH USERS (AJAX) =================
if (isset($_GET['search'])) {
    header('Content-Type: application/json');
    $keyword = trim($_GET['q'] ?? '');
    $role = trim($_GET['role'] ?? '');

    $results = $userModel->search($keyword, $role);
    $data = [];
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}
