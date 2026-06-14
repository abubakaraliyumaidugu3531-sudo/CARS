<?php
// Application-wide configuration constants.
// Single source of truth for values that would otherwise be duplicated as
// magic strings across pages (registration, plan submission, evaluation).

// The current academic session/semester label. Update once per semester.
define('CURRENT_SEMESTER', '2024/2025-1');

// Letter grade -> grade point mapping (5.0 scale). Used when entering grades.
function grade_to_point($grade) {
    $map = ['A' => 5.0, 'B' => 4.0, 'C' => 3.0, 'D' => 2.0, 'E' => 1.0, 'F' => 0.0];
    return $map[strtoupper(trim($grade))] ?? 0.0;
}

// Derive pass/fail status from a letter grade (F is the only failing grade).
function grade_to_status($grade) {
    return strtoupper(trim($grade)) === 'F' ? 'failed' : 'passed';
}

// GPA -> degree classification (for display on the transcript/dashboard).
function gpa_classification($gpa) {
    if ($gpa >= 4.50) return 'First Class';
    if ($gpa >= 3.50) return 'Second Class Upper';
    if ($gpa >= 2.40) return 'Second Class Lower';
    if ($gpa >= 1.50) return 'Third Class';
    if ($gpa > 0.00)  return 'Pass';
    return 'Not classified';
}
