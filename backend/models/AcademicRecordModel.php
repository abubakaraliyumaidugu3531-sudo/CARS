<?php
// AcademicRecordModel: Stores and retrieves student grade history (Objective 2)
require_once __DIR__ . '/../config/database.php';

class AcademicRecordModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }

    // Add or update a grade record for a student.
    public function save($student_id, $course_id, $semester, $grade, $grade_point, $status) {
        $stmt = $this->conn->prepare(
            "REPLACE INTO academic_records (student_id, course_id, semester, grade, grade_point, status)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("iissds", $student_id, $course_id, $semester, $grade, $grade_point, $status);
        return $stmt->execute();
    }

    // Full transcript for a student, with course details, most recent first.
    public function getByStudent($student_id) {
        $stmt = $this->conn->prepare(
            "SELECT ar.*, c.code, c.title, c.credit_unit, c.department
             FROM academic_records ar
             JOIN courses c ON ar.course_id = c.id
             WHERE ar.student_id = ?
             ORDER BY ar.semester DESC, c.code ASC"
        );
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Course IDs the student has passed (used to satisfy prerequisites).
    public function getPassedCourseIds($student_id) {
        $stmt = $this->conn->prepare(
            "SELECT DISTINCT course_id FROM academic_records WHERE student_id = ? AND status = 'passed'"
        );
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = (int) $row['course_id'];
        }
        return $ids;
    }

    // Course IDs the student has failed and not yet passed (retake candidates).
    public function getFailedCourseIds($student_id) {
        $stmt = $this->conn->prepare(
            "SELECT DISTINCT f.course_id
             FROM academic_records f
             WHERE f.student_id = ? AND f.status = 'failed'
               AND NOT EXISTS (
                   SELECT 1 FROM academic_records p
                   WHERE p.student_id = f.student_id AND p.course_id = f.course_id AND p.status = 'passed'
               )"
        );
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = (int) $row['course_id'];
        }
        return $ids;
    }

    // Cumulative GPA across passed and failed attempts (credit-weighted).
    public function getGPA($student_id) {
        $stmt = $this->conn->prepare(
            "SELECT SUM(ar.grade_point * c.credit_unit) AS quality_points,
                    SUM(c.credit_unit) AS total_units
             FROM academic_records ar
             JOIN courses c ON ar.course_id = c.id
             WHERE ar.student_id = ?"
        );
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row || !$row['total_units']) {
            return 0.0;
        }
        return round($row['quality_points'] / $row['total_units'], 2);
    }
}
