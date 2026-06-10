<?php
// RegistrationController: Handles course registration
require_once __DIR__ . '/../models/RegistrationModel.php';

class RegistrationController {
    private $registrationModel;
    public function __construct() {
        $this->registrationModel = new RegistrationModel();
    }
    public function register($student_id, $course_id, $semester) {
        return $this->registrationModel->register($student_id, $course_id, $semester);
    }
    public function getByStudent($student_id) {
        return $this->registrationModel->getByStudent($student_id);
    }
    public function getByCourse($course_id) {
        return $this->registrationModel->getByCourse($course_id);
    }
}
