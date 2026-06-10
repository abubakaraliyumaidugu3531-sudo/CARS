<?php
// EvaluationModel: System effectiveness metrics (Objective 5)
// Quantifies how well the system improves course selection by measuring
// recommendation uptake and prerequisite compliance of registrations.
require_once __DIR__ . '/../config/database.php';

class EvaluationModel {
    private $conn;
    public function __construct() {
        $this->conn = get_db_connection();
    }

    // Recommendation acceptance: how many suggestions students acted on.
    public function getRecommendationStats() {
        $sql = "SELECT
                    COUNT(*) AS total,
                    COALESCE(SUM(status = 'accepted'), 0) AS accepted,
                    COALESCE(SUM(status = 'dismissed'), 0) AS dismissed,
                    COALESCE(SUM(status = 'pending'), 0) AS pending
                FROM recommendations";
        $row = $this->conn->query($sql)->fetch_assoc();
        $total = (int) $row['total'];
        $row['acceptance_rate'] = $total > 0 ? round(($row['accepted'] / $total) * 100, 1) : 0.0;
        return $row;
    }

    // Prerequisite compliance: share of registrations where every prerequisite
    // was already passed. A higher rate means fewer invalid course choices.
    public function getPrerequisiteCompliance() {
        $sql = "SELECT
                    (SELECT COUNT(*) FROM prerequisites p WHERE p.course_id = r.course_id) AS required,
                    (SELECT COUNT(*) FROM prerequisites p
                        JOIN academic_records ar
                          ON ar.course_id = p.prerequisite_id
                         AND ar.student_id = r.student_id
                         AND ar.status = 'passed'
                     WHERE p.course_id = r.course_id) AS met
                FROM registrations r";
        $result = $this->conn->query($sql);
        $total = 0;
        $compliant = 0;
        while ($row = $result->fetch_assoc()) {
            $total++;
            if ((int) $row['required'] === (int) $row['met']) {
                $compliant++;
            }
        }
        return [
            'total' => $total,
            'compliant' => $compliant,
            'compliance_rate' => $total > 0 ? round(($compliant / $total) * 100, 1) : 0.0,
        ];
    }

    // Coverage: how many students have received recommendations.
    public function getCoverage() {
        $students = (int) $this->conn->query("SELECT COUNT(*) AS c FROM users WHERE role = 'student'")
            ->fetch_assoc()['c'];
        $covered = (int) $this->conn->query("SELECT COUNT(DISTINCT student_id) AS c FROM recommendations")
            ->fetch_assoc()['c'];
        return [
            'students' => $students,
            'covered' => $covered,
            'coverage_rate' => $students > 0 ? round(($covered / $students) * 100, 1) : 0.0,
        ];
    }
}
