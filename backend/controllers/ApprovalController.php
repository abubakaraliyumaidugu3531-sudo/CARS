<?php
// ApprovalController: course-plan review actions (Objective 4 - advisor flow).
// Used two ways:
//   1) As a class (instantiated by pages that need approval data).
//   2) As a POST endpoint (forms post here); the request-handling block at the
//      bottom only runs when this file is requested directly, not on include.
require_once __DIR__ . '/../models/ApprovalModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class ApprovalController {
    private $approvalModel;
    public function __construct() {
        $this->approvalModel = new ApprovalModel();
    }
    public function submitPlan($student_id, $semester) {
        return $this->approvalModel->createRequest($student_id, $semester);
    }
    public function decide($id, $advisor_id, $status, $comment = null) {
        return $this->approvalModel->decide($id, $advisor_id, $status, $comment);
    }
    public function getQueue($department = null) {
        return $this->approvalModel->getQueue($department);
    }
    public function getHandledByDepartment($department = null) {
        return $this->approvalModel->getHandledByDepartment($department);
    }
    public function getLatestForStudent($student_id) {
        return $this->approvalModel->getLatestForStudent($student_id);
    }
}

// ---------------------------------------------------------------------------
// Direct POST endpoint (runs only when this script is the entry point).
// ---------------------------------------------------------------------------
if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    require_once __DIR__ . '/../helpers/session.php';
    require_once __DIR__ . '/../config/app.php';
    require_login();

    $role = $_SESSION['role'] ?? '';
    $controller = new ApprovalController();
    $back = $_SERVER['HTTP_REFERER'] ?? '/frontend/pages/login.php';
    $sep = strpos($back, '?') === false ? '?' : '&';
    $action = $_POST['action'] ?? '';

    if ($role === 'student' && $action === 'submit_plan') {
        $controller->submitPlan((int) $_SESSION['user_id'], CURRENT_SEMESTER);
        header('Location: ' . $back . $sep . 'msg=' . urlencode('Course plan submitted for advisor approval.'));
        exit;
    }

    if ($role === 'advisor' && in_array($action, ['approve', 'reject'], true) && !empty($_POST['approval_id'])) {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $comment = trim($_POST['comment'] ?? '');
        $controller->decide((int) $_POST['approval_id'], (int) $_SESSION['user_id'], $status, $comment !== '' ? $comment : null);
        header('Location: ' . $back . $sep . 'msg=' . urlencode("Plan {$status}."));
        exit;
    }

    header('Location: ' . $back . $sep . 'err=' . urlencode('Invalid request.'));
    exit;
}
