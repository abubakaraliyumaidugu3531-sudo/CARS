<?php
require_once __DIR__ . '/../helpers/session.php';
require_once __DIR__ . '/../middleware/role_middleware.php';
require_once __DIR__ . '/../models/CourseModel.php';

require_login();
require_admin();

$courseModel = new CourseModel();

// ================= ADMIN: EDIT COURSE =================
if (isset($_POST['edit_course'])) {
    $course_id = (int)($_POST['course_id'] ?? 0);
    $code = trim($_POST['code'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $credit_unit = (int)($_POST['credit_unit'] ?? 0);
    $department = trim($_POST['department'] ?? '');
    $level = trim($_POST['level'] ?? '100');
    $semester = in_array($_POST['semester'] ?? '', ['first', 'second', 'any'], true) ? $_POST['semester'] : 'any';
    $is_core = isset($_POST['is_core']) ? 1 : 0;
    $description = trim($_POST['description'] ?? '') ?: null;

    $courses_page = '/cars/frontend/pages/admin_courses.php';

    if (!$course_id || !$code || !$title || !$credit_unit || !$department) {
        header('Location: ' . $courses_page . '?err=' . urlencode('Provide code, title, credit units, and department.'));
        exit;
    }

    if ($courseModel->update($course_id, $code, $title, $credit_unit, $department, $level, $semester, $is_core, $description)) {
        header('Location: ' . $courses_page . '?msg=' . urlencode('Course updated successfully.'));
        exit;
    }
    header('Location: ' . $courses_page . '?err=' . urlencode('Could not update course.'));
    exit;
}

// ================= ADMIN: DELETE COURSE =================
if (isset($_POST['delete_course'])) {
    $course_id = (int)($_POST['course_id'] ?? 0);
    $courses_page = '/cars/frontend/pages/admin_courses.php';

    if (!$course_id) {
        header('Location: ' . $courses_page . '?err=' . urlencode('Invalid course ID.'));
        exit;
    }

    if ($courseModel->delete($course_id)) {
        header('Location: ' . $courses_page . '?msg=' . urlencode('Course deleted successfully.'));
        exit;
    }
    header('Location: ' . $courses_page . '?err=' . urlencode('Could not delete course.'));
    exit;
}

// ================= ADMIN: SEARCH COURSES (AJAX) =================
if (isset($_GET['search'])) {
    header('Content-Type: application/json');
    $keyword = trim($_GET['q'] ?? '');
    $department = trim($_GET['department'] ?? '');

    $results = $courseModel->search($keyword, $department);
    $data = [];
    while ($row = $results->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $data]);
    exit;
}
