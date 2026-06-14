-- =====================================================================
-- Course Advisory and Recommendation System (CARS) - Database Schema
-- =====================================================================
-- Run this against the `course_advisory_system` database.
-- Supports objectives: storing academic records (Obj 2), prerequisite-
-- aware recommendations (Obj 3) and effectiveness evaluation (Obj 5).
-- =====================================================================

-- USERS TABLE
-- `department` and `level` describe the student's programme and are used
-- by the recommendation engine to target level-appropriate courses.
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'advisor', 'admin') NOT NULL,
    department VARCHAR(100) NULL,
    level VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- COURSES TABLE
-- `level` (e.g. 100, 200) and `semester` let the engine recommend courses
-- appropriate to the student's stage. `is_core` distinguishes compulsory
-- programme courses from electives.
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    credit_unit INT NOT NULL,
    department VARCHAR(100) NOT NULL,
    level VARCHAR(20) NOT NULL DEFAULT '100',
    semester ENUM('first', 'second', 'any') NOT NULL DEFAULT 'any',
    is_core TINYINT(1) NOT NULL DEFAULT 1,
    description TEXT NULL
);

-- PREREQUISITES TABLE
-- A course (course_id) requires another course (prerequisite_id) to be
-- passed first. The recommendation engine uses this to gate suggestions.
CREATE TABLE IF NOT EXISTS prerequisites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    prerequisite_id INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (prerequisite_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_prerequisite (course_id, prerequisite_id)
);

-- ACADEMIC RECORDS TABLE  (Objective 2: student academic records)
-- Stores the grade history that drives recommendations: which courses a
-- student has passed (satisfying prerequisites) or failed (needing retake).
CREATE TABLE IF NOT EXISTS academic_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    grade VARCHAR(2) NOT NULL,
    grade_point DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    status ENUM('passed', 'failed') NOT NULL DEFAULT 'passed',
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_record (student_id, course_id, semester)
);

-- REGISTRATIONS TABLE (courses a student is currently enrolled in)
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (student_id, course_id, semester)
);

-- RECOMMENDATIONS TABLE
-- `reason` explains why a course was suggested (transparency for the user)
-- and `status` tracks whether the student acted on it (used for Obj 5).
CREATE TABLE IF NOT EXISTS recommendations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    score FLOAT NOT NULL,
    reason VARCHAR(255) NULL,
    status ENUM('pending', 'accepted', 'dismissed') NOT NULL DEFAULT 'pending',
    recommended_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_recommendation (student_id, course_id)
);

-- APPROVALS TABLE
-- One row per student per semester: the student submits the courses they have
-- registered for that semester as a "plan", and an advisor reviews it. The
-- advisor_id is NULL until an advisor decides, and `comment` carries feedback.
CREATE TABLE IF NOT EXISTS approvals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    advisor_id INT NULL,
    semester VARCHAR(20) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    comment VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    decided_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (advisor_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_student_semester (student_id, semester)
);

-- INDEXES FOR PERFORMANCE
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_courses_department ON courses(department);
CREATE INDEX idx_courses_level ON courses(level);
CREATE INDEX idx_prerequisites_course ON prerequisites(course_id);
CREATE INDEX idx_academic_records_student ON academic_records(student_id);
CREATE INDEX idx_registrations_student ON registrations(student_id);
CREATE INDEX idx_registrations_course ON registrations(course_id);
CREATE INDEX idx_recommendations_student ON recommendations(student_id);
CREATE INDEX idx_approvals_advisor ON approvals(advisor_id);
