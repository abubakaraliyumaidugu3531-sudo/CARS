<?php
// RecommendationModel: Persists and reports generated recommendations
require_once __DIR__ . '/../config/database.php';

class RecommendationModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }

    // Upsert a recommendation. Preserves an existing 'accepted'/'dismissed'
    // status so regenerating suggestions doesn't wipe the acceptance signal.
    public function save($student_id, $course_id, $score, $reason) {
        $stmt = $this->conn->prepare(
            "INSERT INTO recommendations (student_id, course_id, score, reason, status)
             VALUES (?, ?, ?, ?, 'pending')
             ON DUPLICATE KEY UPDATE score = VALUES(score), reason = VALUES(reason)"
        );
        $stmt->bind_param("iids", $student_id, $course_id, $score, $reason);
        return $stmt->execute();
    }

    public function getByStudent($student_id) {
        $stmt = $this->conn->prepare(
            "SELECT r.*, c.code, c.title, c.credit_unit, c.department, c.level
             FROM recommendations r
             JOIN courses c ON r.course_id = c.id
             WHERE r.student_id = ?
             ORDER BY r.score DESC"
        );
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    public function setStatus($student_id, $course_id, $status) {
        $stmt = $this->conn->prepare(
            "UPDATE recommendations SET status = ? WHERE student_id = ? AND course_id = ?"
        );
        $stmt->bind_param("sii", $status, $student_id, $course_id);
        return $stmt->execute();
    }

    public function countByStudent($student_id) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS c FROM recommendations WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return (int) $stmt->get_result()->fetch_assoc()['c'];
    }
}
