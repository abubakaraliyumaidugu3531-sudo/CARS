<?php
// PrerequisiteModel: Course prerequisite relationships (Objective 3)
require_once __DIR__ . '/../config/database.php';

class PrerequisiteModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }

    // Map of course_id => [prerequisite_id, ...] for the whole catalogue.
    // Returned in one pass so the engine avoids per-course queries.
    public function getAllMap() {
        $result = $this->conn->query("SELECT course_id, prerequisite_id FROM prerequisites");
        $map = [];
        while ($row = $result->fetch_assoc()) {
            $map[(int) $row['course_id']][] = (int) $row['prerequisite_id'];
        }
        return $map;
    }

    // Prerequisite course IDs for a single course.
    public function getByCourse($course_id) {
        $stmt = $this->conn->prepare("SELECT prerequisite_id FROM prerequisites WHERE course_id = ?");
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = (int) $row['prerequisite_id'];
        }
        return $ids;
    }

    // Prerequisite course codes for a single course (for display).
    public function getCodesByCourse($course_id) {
        $stmt = $this->conn->prepare(
            "SELECT c.code FROM prerequisites p JOIN courses c ON p.prerequisite_id = c.id WHERE p.course_id = ?"
        );
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $codes = [];
        while ($row = $result->fetch_assoc()) {
            $codes[] = $row['code'];
        }
        return $codes;
    }

    public function add($course_id, $prerequisite_id) {
        $stmt = $this->conn->prepare(
            "INSERT IGNORE INTO prerequisites (course_id, prerequisite_id) VALUES (?, ?)"
        );
        $stmt->bind_param("ii", $course_id, $prerequisite_id);
        return $stmt->execute();
    }

    public function remove($course_id, $prerequisite_id) {
        $stmt = $this->conn->prepare(
            "DELETE FROM prerequisites WHERE course_id = ? AND prerequisite_id = ?"
        );
        $stmt->bind_param("ii", $course_id, $prerequisite_id);
        return $stmt->execute();
    }

    // All prerequisite pairs with course codes/titles, for the admin screen.
    public function listAll() {
        return $this->conn->query(
            "SELECT p.id, p.course_id, p.prerequisite_id,
                    c.code AS course_code, c.title AS course_title,
                    pre.code AS prereq_code, pre.title AS prereq_title
             FROM prerequisites p
             JOIN courses c ON p.course_id = c.id
             JOIN courses pre ON p.prerequisite_id = pre.id
             ORDER BY c.code, pre.code"
        );
    }
}
