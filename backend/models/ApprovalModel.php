<?php
// ApprovalModel: per-semester course-plan reviews (Objective 4 - advisor flow).
// A student submits the courses they registered for a semester; an advisor in
// the same department reviews and approves/rejects with an optional comment.
require_once __DIR__ . '/../config/database.php';

class ApprovalModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }

    // Student submits (or re-submits) their plan for a semester. Re-submitting
    // an existing request resets it to pending and clears the prior decision.
    public function createRequest($student_id, $semester) {
        $stmt = $this->conn->prepare(
            "INSERT INTO approvals (student_id, semester, status)
             VALUES (?, ?, 'pending')
             ON DUPLICATE KEY UPDATE status = 'pending', advisor_id = NULL,
                                     comment = NULL, decided_at = NULL,
                                     created_at = CURRENT_TIMESTAMP"
        );
        $stmt->bind_param("is", $student_id, $semester);
        return $stmt->execute();
    }

    // Pending plans awaiting review, limited to students in the advisor's
    // department (NULL department = see all). Most recent first.
    public function getQueue($department = null) {
        if ($department) {
            $stmt = $this->conn->prepare(
                "SELECT a.*, u.name AS student_name, u.level, u.department
                 FROM approvals a JOIN users u ON a.student_id = u.id
                 WHERE a.status = 'pending' AND u.department = ?
                 ORDER BY a.created_at ASC"
            );
            $stmt->bind_param("s", $department);
            $stmt->execute();
            return $stmt->get_result();
        }
        return $this->conn->query(
            "SELECT a.*, u.name AS student_name, u.level, u.department
             FROM approvals a JOIN users u ON a.student_id = u.id
             WHERE a.status = 'pending' ORDER BY a.created_at ASC"
        );
    }

    // All plans an advisor has handled or could handle (for dashboard counts).
    public function getHandledByDepartment($department = null) {
        if ($department) {
            $stmt = $this->conn->prepare(
                "SELECT a.*, u.name AS student_name FROM approvals a
                 JOIN users u ON a.student_id = u.id
                 WHERE u.department = ? ORDER BY a.created_at DESC"
            );
            $stmt->bind_param("s", $department);
            $stmt->execute();
            return $stmt->get_result();
        }
        return $this->conn->query(
            "SELECT a.*, u.name AS student_name FROM approvals a
             JOIN users u ON a.student_id = u.id ORDER BY a.created_at DESC"
        );
    }

    // Advisor records a decision on a plan.
    public function decide($id, $advisor_id, $status, $comment = null) {
        $stmt = $this->conn->prepare(
            "UPDATE approvals SET advisor_id = ?, status = ?, comment = ?, decided_at = NOW()
             WHERE id = ?"
        );
        $stmt->bind_param("issi", $advisor_id, $status, $comment, $id);
        return $stmt->execute();
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM approvals WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Latest plan status for a student (for their dashboard / plan page).
    public function getLatestForStudent($student_id) {
        $stmt = $this->conn->prepare(
            "SELECT a.*, adv.name AS advisor_name FROM approvals a
             LEFT JOIN users adv ON a.advisor_id = adv.id
             WHERE a.student_id = ? ORDER BY a.created_at DESC LIMIT 1"
        );
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function getForStudentSemester($student_id, $semester) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM approvals WHERE student_id = ? AND semester = ? LIMIT 1"
        );
        $stmt->bind_param("is", $student_id, $semester);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
