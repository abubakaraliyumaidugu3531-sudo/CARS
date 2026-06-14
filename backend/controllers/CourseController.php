<?php
// CourseController: course management (admin). Used as a class by pages and as a
// guarded POST endpoint for create/update/delete actions.
require_once __DIR__ . '/../models/CourseModel.php';

class CourseController {
    private $courseModel;
    public function __construct() {
        $this->courseModel = new CourseModel();
    }
    public function create($code, $title, $credit_unit, $department, $level = '100', $semester = 'any', $is_core = 1, $description = null) {
        return $this->courseModel->create($code, $title, $credit_unit, $department, $level, $semester, $is_core, $description);
    }
    public function update($id, $code, $title, $credit_unit, $department, $level = '100', $semester = 'any', $is_core = 1, $description = null) {
        return $this->courseModel->update($id, $code, $title, $credit_unit, $department, $level, $semester, $is_core, $description);
    }
    public function delete($id) {
        return $this->courseModel->delete($id);
    }
    public function getAll($department = null) {
        return $this->courseModel->getAll($department);
    }
    public function findById($id) {
        return $this->courseModel->findById($id);
    }
}

// ---------------------------------------------------------------------------
// Direct POST endpoint (admin only). Runs only when requested directly.
// ---------------------------------------------------------------------------
if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    require_once __DIR__ . '/../helpers/session.php';
    require_once __DIR__ . '/../middleware/role_middleware.php';
    require_admin();

    $controller = new CourseController();
    $back = '/frontend/pages/admin_courses.php';
    $action = $_POST['action'] ?? '';

    $code = trim($_POST['code'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $credit = (int) ($_POST['credit_unit'] ?? 0);
    $dept = trim($_POST['department'] ?? '');
    $level = trim($_POST['level'] ?? '100');
    $semester = $_POST['semester'] ?? 'any';
    $isCore = isset($_POST['is_core']) ? 1 : 0;
    $desc = trim($_POST['description'] ?? '');
    $desc = $desc !== '' ? $desc : null;

    if ($action === 'create') {
        $ok = $code && $title && $credit && $dept
            && $controller->create($code, $title, $credit, $dept, $level, $semester, $isCore, $desc);
        $q = $ok ? 'msg=' . urlencode('Course added.') : 'err=' . urlencode('Could not add course (check code is unique).');
    } elseif ($action === 'update' && !empty($_POST['id'])) {
        $ok = $controller->update((int) $_POST['id'], $code, $title, $credit, $dept, $level, $semester, $isCore, $desc);
        $q = $ok ? 'msg=' . urlencode('Course updated.') : 'err=' . urlencode('Update failed.');
    } elseif ($action === 'delete' && !empty($_POST['id'])) {
        $ok = $controller->delete((int) $_POST['id']);
        $q = $ok ? 'msg=' . urlencode('Course deleted.') : 'err=' . urlencode('Delete failed.');
    } else {
        $q = 'err=' . urlencode('Invalid request.');
    }

    header('Location: ' . $back . '?' . $q);
    exit;
}
