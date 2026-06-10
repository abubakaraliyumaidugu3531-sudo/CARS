<?php
// UserModel: Handles user-related DB operations
require_once __DIR__ . '/../config/database.php';

class UserModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }
    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function create($name, $email, $password, $role, $department = null, $level = null) {
        $stmt = $this->conn->prepare(
            "INSERT INTO users (name, email, password, role, department, level) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("ssssss", $name, $email, $password, $role, $department, $level);
        return $stmt->execute();
    }
    public function findById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Update a student's programme details (used by the recommendation engine).
    public function updateProfile($id, $department, $level) {
        $stmt = $this->conn->prepare("UPDATE users SET department = ?, level = ? WHERE id = ?");
        $stmt->bind_param("ssi", $department, $level, $id);
        return $stmt->execute();
    }

    // All users with a given role (e.g. students for an advisor's list).
    public function getByRole($role) {
        $stmt = $this->conn->prepare("SELECT id, name, email, department, level FROM users WHERE role = ? ORDER BY name");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        return $stmt->get_result();
    }
}
