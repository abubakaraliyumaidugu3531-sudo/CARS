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

    // Count of users in a role (admin KPIs).
    public function countByRole($role) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS c FROM users WHERE role = ?");
        $stmt->bind_param("s", $role);
        $stmt->execute();
        return (int) $stmt->get_result()->fetch_assoc()['c'];
    }

    // Every user, most recent first (admin user list).
    public function listAll() {
        return $this->conn->query(
            "SELECT id, name, email, role, department, level, created_at FROM users ORDER BY created_at DESC, id DESC"
        );
    }

    // Search users by name, email, role, or department (admin feature).
    public function search($keyword = '', $role = '') {
        $query = "SELECT id, name, email, role, department, level, created_at FROM users WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($keyword)) {
            $keyword = "%{$keyword}%";
            $query .= " AND (name LIKE ? OR email LIKE ?)";
            $params[] = $keyword;
            $params[] = $keyword;
            $types .= 'ss';
        }

        if (!empty($role)) {
            $query .= " AND role = ?";
            $params[] = $role;
            $types .= 's';
        }

        $query .= " ORDER BY created_at DESC, id DESC";

        if (empty($params)) {
            return $this->conn->query($query);
        }

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Update user details (admin feature).
    public function update($id, $name, $email, $role, $department = null, $level = null) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET name = ?, email = ?, role = ?, department = ?, level = ? WHERE id = ?"
        );
        $stmt->bind_param("sssssi", $name, $email, $role, $department, $level, $id);
        return $stmt->execute();
    }

    // Delete a user and all their related records (admin feature).
    public function delete($id) {
        // Delete related records first (cascading deletes)
        $this->conn->query("DELETE FROM recommendations WHERE student_id = {$id}");
        $this->conn->query("DELETE FROM registrations WHERE student_id = {$id}");
        $this->conn->query("DELETE FROM academic_records WHERE student_id = {$id}");
        $this->conn->query("DELETE FROM approvals WHERE student_id = {$id}");

        // Delete the user
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Check if email exists (excluding current user, for edit validation).
    public function emailExists($email, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
            $stmt->bind_param("si", $email, $excludeId);
        } else {
            $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param("s", $email);
        }
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
