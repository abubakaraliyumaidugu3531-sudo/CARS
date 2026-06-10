<?php
// CourseModel: Handles course-related DB operations
require_once __DIR__ . '/../config/database.php';

class CourseModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }
    public function getAll($department = null) {
        if ($department) {
            $stmt = $this->conn->prepare("SELECT * FROM courses WHERE department = ?");
            $stmt->bind_param("s", $department);
            $stmt->execute();
            return $stmt->get_result();
        } else {
            return $this->conn->query("SELECT * FROM courses");
        }
    }
    // Search by code/title with an optional department filter (for the UI).
    public function search($search = '', $department = '') {
        $sql = "SELECT * FROM courses WHERE 1=1";
        $types = '';
        $params = [];
        if ($search !== '') {
            $sql .= " AND (code LIKE ? OR title LIKE ?)";
            $like = '%' . $search . '%';
            $types .= 'ss';
            $params[] = $like;
            $params[] = $like;
        }
        if ($department !== '') {
            $sql .= " AND department = ?";
            $types .= 's';
            $params[] = $department;
        }
        $sql .= " ORDER BY level ASC, code ASC";
        $stmt = $this->conn->prepare($sql);
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    }

    // Distinct department list for filter dropdowns.
    public function getDepartments() {
        $result = $this->conn->query("SELECT DISTINCT department FROM courses ORDER BY department");
        $departments = [];
        while ($row = $result->fetch_assoc()) {
            $departments[] = $row['department'];
        }
        return $departments;
    }

    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function create($code, $title, $credit_unit, $department) {
        $stmt = $this->conn->prepare("INSERT INTO courses (code, title, credit_unit, department) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $code, $title, $credit_unit, $department);
        return $stmt->execute();
    }
    public function update($id, $code, $title, $credit_unit, $department) {
        $stmt = $this->conn->prepare("UPDATE courses SET code=?, title=?, credit_unit=?, department=? WHERE id=?");
        $stmt->bind_param("ssisi", $code, $title, $credit_unit, $department, $id);
        return $stmt->execute();
    }
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
