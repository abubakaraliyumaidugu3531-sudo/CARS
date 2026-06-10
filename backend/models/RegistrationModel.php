<?php
// RegistrationModel: Handles course registration
require_once __DIR__ . '/../config/database.php';

class RegistrationModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }
    public function register($student_id, $course_id, $semester) {
        $stmt = $this->conn->prepare("INSERT INTO registrations (student_id, course_id, semester) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $student_id, $course_id, $semester);
        return $stmt->execute();
    }
    public function getByStudent($student_id) {
        $stmt = $this->conn->prepare("SELECT r.*, c.code, c.title, c.credit_unit, c.department FROM registrations r JOIN courses c ON r.course_id = c.id WHERE r.student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function getByCourse($course_id) {
        $stmt = $this->conn->prepare("SELECT * FROM registrations WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
