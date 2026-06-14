<?php
// PrerequisiteController: add/remove course prerequisites (admin only).
require_once __DIR__ . '/../models/PrerequisiteModel.php';

class PrerequisiteController {
    private $model;
    public function __construct() {
        $this->model = new PrerequisiteModel();
    }
    public function add($course_id, $prerequisite_id) {
        return $this->model->add($course_id, $prerequisite_id);
    }
    public function remove($course_id, $prerequisite_id) {
        return $this->model->remove($course_id, $prerequisite_id);
    }
    public function listAll() {
        return $this->model->listAll();
    }
}

// Direct POST endpoint (admin only).
if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    require_once __DIR__ . '/../helpers/session.php';
    require_once __DIR__ . '/../middleware/role_middleware.php';
    require_admin();

    $controller = new PrerequisiteController();
    $back = '/frontend/pages/admin_prerequisites.php';
    $action = $_POST['action'] ?? '';
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $prereqId = (int) ($_POST['prerequisite_id'] ?? 0);

    if ($action === 'add' && $courseId && $prereqId && $courseId !== $prereqId) {
        $controller->add($courseId, $prereqId);
        $q = 'msg=' . urlencode('Prerequisite added.');
    } elseif ($action === 'remove' && $courseId && $prereqId) {
        $controller->remove($courseId, $prereqId);
        $q = 'msg=' . urlencode('Prerequisite removed.');
    } else {
        $q = 'err=' . urlencode('Invalid request (a course cannot require itself).');
    }
    header('Location: ' . $back . '?' . $q);
    exit;
}
