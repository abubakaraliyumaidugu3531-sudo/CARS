<?php
// RecommendationController: Rule-based course recommendation engine (Objective 3)
//
// The engine combines a student's academic record, course prerequisites and
// programme level to produce ranked, explainable course suggestions:
//   1. Exclude courses already passed or currently registered.
//   2. Gate out courses whose prerequisites are not yet satisfied.
//   3. Score the remaining candidates from transparent rules and record
//      a human-readable reason for each suggestion.
require_once __DIR__ . '/../models/RecommendationModel.php';
require_once __DIR__ . '/../models/CourseModel.php';
require_once __DIR__ . '/../models/RegistrationModel.php';
require_once __DIR__ . '/../models/AcademicRecordModel.php';
require_once __DIR__ . '/../models/PrerequisiteModel.php';
require_once __DIR__ . '/../models/UserModel.php';

class RecommendationController {
    private $recommendationModel;
    private $courseModel;
    private $registrationModel;
    private $academicRecordModel;
    private $prerequisiteModel;
    private $userModel;

    public function __construct() {
        $this->recommendationModel = new RecommendationModel();
        $this->courseModel = new CourseModel();
        $this->registrationModel = new RegistrationModel();
        $this->academicRecordModel = new AcademicRecordModel();
        $this->prerequisiteModel = new PrerequisiteModel();
        $this->userModel = new UserModel();
    }

    // Generate, persist and return ranked recommendations for a student.
    public function generate($student_id, $max_courses = 6) {
        $student = $this->userModel->findById($student_id);
        $department = $student['department'] ?? null;
        $studentLevel = isset($student['level']) ? (int) $student['level'] : 0;

        // Academic context.
        $passed = $this->academicRecordModel->getPassedCourseIds($student_id);   // satisfied prereqs
        $failed = $this->academicRecordModel->getFailedCourseIds($student_id);   // retake candidates
        $prereqMap = $this->prerequisiteModel->getAllMap();

        // Currently registered courses are excluded from suggestions.
        $registeredResult = $this->registrationModel->getByStudent($student_id);
        $registered = [];
        while ($row = $registeredResult->fetch_assoc()) {
            $registered[] = (int) $row['course_id'];
        }

        // Candidate pool: courses in the student's department (fallback: all).
        $courses = $department
            ? $this->courseModel->getAll($department)
            : $this->courseModel->getAll();

        $recommendations = [];
        foreach ($courses as $course) {
            $courseId = (int) $course['id'];

            // 1. Skip courses already passed or currently registered.
            if (in_array($courseId, $passed, true) || in_array($courseId, $registered, true)) {
                continue;
            }

            // 2. Prerequisite gate: every prerequisite must be passed.
            $prereqs = $prereqMap[$courseId] ?? [];
            $missing = array_diff($prereqs, $passed);
            if (!empty($missing)) {
                continue; // not yet eligible
            }

            // 3. Transparent rule-based scoring.
            $score = 0.0;
            $reasons = [];

            $isRetake = in_array($courseId, $failed, true);
            if ($isRetake) {
                $score += 5;
                $reasons[] = 'Retake — previously failed';
            }

            if (!empty($course['is_core'])) {
                $score += 3;
                $reasons[] = 'Core course for your programme';
            } else {
                $score += 1;
                $reasons[] = 'Elective';
            }

            $courseLevel = (int) ($course['level'] ?? 0);
            if ($studentLevel > 0) {
                if ($courseLevel === $studentLevel) {
                    $score += 2;
                    $reasons[] = 'Matches your current level';
                } elseif ($courseLevel < $studentLevel) {
                    $score += 1;
                    $reasons[] = 'Outstanding lower-level course';
                } else {
                    // Eligible advanced course (prerequisites already met).
                    $reasons[] = 'Advanced course you now qualify for';
                }
            }

            // Credit-weight nudge so heavier courses rank slightly higher.
            $score += ((int) $course['credit_unit']) * 0.5;

            $recommendations[] = [
                'course_id'   => $courseId,
                'code'        => $course['code'],
                'title'       => $course['title'],
                'credit_unit' => (int) $course['credit_unit'],
                'level'       => $course['level'] ?? '',
                'score'       => round($score, 2),
                'reason'      => implode('; ', $reasons),
            ];
        }

        // Rank by score and keep the top N.
        usort($recommendations, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
        $recommendations = array_slice($recommendations, 0, $max_courses);

        // Persist for later display and effectiveness tracking.
        foreach ($recommendations as $rec) {
            $this->recommendationModel->save(
                $student_id, $rec['course_id'], $rec['score'], $rec['reason']
            );
        }

        return $recommendations;
    }

    public function getByStudent($student_id) {
        return $this->recommendationModel->getByStudent($student_id);
    }

    public function setStatus($student_id, $course_id, $status) {
        return $this->recommendationModel->setStatus($student_id, $course_id, $status);
    }
}
