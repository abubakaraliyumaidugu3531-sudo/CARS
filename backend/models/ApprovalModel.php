<?php
// ApprovalModel: Handles advisor approvals
require_once __DIR__ . '/../config/database.php';

class ApprovalModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }
    public function submit($advisor_id, $student_id, $status) {
        $stmt = $this->conn->prepare("REPLACE INTO approvals (advisor_id, student_id, status, decided_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("iis", $advisor_id, $student_id, $status);
        return $stmt->execute();
    }
    public function getByAdvisor($advisor_id) {
        $stmt = $this->conn->prepare("SELECT a.*, u.name AS student_name FROM approvals a JOIN users u ON a.student_id = u.id WHERE a.advisor_id = ?");
        $stmt->bind_param("i", $advisor_id);
        $stmt->execute();
        return $stmt->get_result();
    }
    public function getByStudent($student_id) {
        $stmt = $this->conn->prepare("SELECT * FROM approvals WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
