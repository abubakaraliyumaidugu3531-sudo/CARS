<?php
// RecordController: enter a student's grade (admin). Persists an academic record
// deriving grade point + pass/fail from the letter grade (Objective 2).
require_once __DIR__ . '/../models/AcademicRecordModel.php';

class RecordController {
    private $model;
    public function __construct() {
        $this->model = new AcademicRecordModel();
    }
    public function save($student_id, $course_id, $semester, $grade) {
        require_once __DIR__ . '/../config/app.php';
        $point = grade_to_point($grade);
        $status = grade_to_status($grade);
        return $this->model->save($student_id, $course_id, $semester, strtoupper($grade), $point, $status);
    }
}

// Direct POST endpoint (admin only).
if (realpath(__FILE__) === realpath($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    require_once __DIR__ . '/../helpers/session.php';
    require_once __DIR__ . '/../middleware/role_middleware.php';
    require_admin();

    $controller = new RecordController();
    $back = '/cars/frontend/pages/admin_records.php';

    $student_id = (int) ($_POST['student_id'] ?? 0);
    $course_id = (int) ($_POST['course_id'] ?? 0);
    $semester = trim($_POST['semester'] ?? '');
    $grade = strtoupper(trim($_POST['grade'] ?? ''));

    if ($student_id && $course_id && $semester && in_array($grade, ['A','B','C','D','E','F'], true)) {
        $controller->save($student_id, $course_id, $semester, $grade);
        $q = 'msg=' . urlencode('Grade recorded.');
    } else {
        $q = 'err=' . urlencode('Select a student, course, semester and valid grade.');
    }
    header('Location: ' . $back . '?' . $q);
    exit;
}
