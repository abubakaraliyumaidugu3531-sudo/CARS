<?php
// CourseController: Handles CRUD for courses
require_once __DIR__ . '/../models/CourseModel.php';

class CourseController {
    private $courseModel;
    public function __construct() {
        $this->courseModel = new CourseModel();
    }
    public function create($code, $title, $credit_unit, $department) {
        return $this->courseModel->create($code, $title, $credit_unit, $department);
    }
    public function update($id, $code, $title, $credit_unit, $department) {
        return $this->courseModel->update($id, $code, $title, $credit_unit, $department);
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
