-- =====================================================================
-- Course Advisory and Recommendation System (CARS)
-- ONE-SHOT DATABASE SETUP  (schema + demo data)
-- =====================================================================
-- How to run (you do NOT need to create the database first):
--   * phpMyAdmin:  Import  ->  choose this file  ->  Go
--   * Command line: mysql -u root < database/schema.sql
--
-- Safe to re-run: it drops and recreates everything, then re-seeds.
--
-- Demo accounts (password for all: password123):
--   admin@cars.test    (admin)
--   advisor@cars.test  (advisor, Computer Science)
--   jane@cars.test     (student, 200 level Computer Science)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS course_advisory_system
    CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE course_advisory_system;

-- Drop in dependency order so the script is fully re-runnable.
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS recommendations;
DROP TABLE IF EXISTS approvals;
DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS academic_records;
DROP TABLE IF EXISTS prerequisites;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================== TABLES ===============================

-- USERS: students self-register; advisors/admins are provisioned by an admin.
-- `department` and `level` drive level-appropriate recommendations.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'advisor', 'admin') NOT NULL,
    department VARCHAR(100) NULL,
    level VARCHAR(20) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- COURSES: `level`/`semester` target the right stage; `is_core` marks
-- compulsory programme courses vs electives.
CREATE TABLE courses (
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

-- PREREQUISITES: course_id requires prerequisite_id to be passed first.
CREATE TABLE prerequisites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    prerequisite_id INT NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (prerequisite_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_prerequisite (course_id, prerequisite_id)
);

-- ACADEMIC RECORDS (Objective 2): grade history that drives recommendations.
CREATE TABLE academic_records (
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

-- REGISTRATIONS: courses a student is currently enrolled in.
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    semester VARCHAR(20) NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_registration (student_id, course_id, semester)
);

-- RECOMMENDATIONS: `reason` explains the suggestion; `status` tracks uptake.
CREATE TABLE recommendations (
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

-- APPROVALS: one row per student per semester. Student submits a course plan;
-- an advisor reviews and approves/rejects with a comment.
CREATE TABLE approvals (
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

-- ============================= INDEXES ===============================
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_courses_department ON courses(department);
CREATE INDEX idx_courses_level ON courses(level);
CREATE INDEX idx_prerequisites_course ON prerequisites(course_id);
CREATE INDEX idx_academic_records_student ON academic_records(student_id);
CREATE INDEX idx_registrations_student ON registrations(student_id);
CREATE INDEX idx_registrations_course ON registrations(course_id);
CREATE INDEX idx_recommendations_student ON recommendations(student_id);
CREATE INDEX idx_approvals_advisor ON approvals(advisor_id);

-- ============================ DEMO DATA ==============================

-- Users (password for all three is: password123)
INSERT INTO users (id, name, email, password, role, department, level) VALUES
(1, 'System Admin', 'admin@cars.test',   '$2y$10$OF00uXLDdiAnnhxMYFvtkujfjNTmhkNloYwVEXWOooqyjtEyvIasi', 'admin',   NULL, NULL),
(2, 'Dr. Advisor',  'advisor@cars.test', '$2y$10$OF00uXLDdiAnnhxMYFvtkujfjNTmhkNloYwVEXWOooqyjtEyvIasi', 'advisor', 'Computer Science', NULL),
(3, 'Jane Student', 'jane@cars.test',    '$2y$10$OF00uXLDdiAnnhxMYFvtkujfjNTmhkNloYwVEXWOooqyjtEyvIasi', 'student', 'Computer Science', '200');

-- Courses
INSERT INTO courses (id, code, title, credit_unit, department, level, semester, is_core, description) VALUES
(1,  'CSC101', 'Introduction to Computer Science', 3, 'Computer Science', '100', 'first',  1, 'Foundations of computing and problem solving.'),
(2,  'CSC102', 'Introduction to Programming',      3, 'Computer Science', '100', 'second', 1, 'Programming fundamentals using a high-level language.'),
(3,  'MTH101', 'Calculus I',                       3, 'Computer Science', '100', 'first',  1, 'Limits, differentiation and integration.'),
(4,  'GST101', 'Use of English',                   2, 'Computer Science', '100', 'first',  1, 'Communication and academic writing skills.'),
(5,  'GST102', 'Communication Skills II',          2, 'Computer Science', '100', 'second', 1, 'Oral and written communication.'),
(6,  'CSC201', 'Data Structures',                  3, 'Computer Science', '200', 'first',  1, 'Linear and non-linear data structures.'),
(7,  'CSC202', 'Object Oriented Programming',      3, 'Computer Science', '200', 'second', 1, 'Classes, objects, inheritance and polymorphism.'),
(8,  'CSC203', 'Discrete Mathematics',             3, 'Computer Science', '200', 'first',  1, 'Logic, sets, relations and graph theory.'),
(9,  'CSC204', 'Database Systems',                 3, 'Computer Science', '200', 'second', 1, 'Relational model, SQL and database design.'),
(10, 'CSC205', 'Web Development',                  2, 'Computer Science', '200', 'first',  0, 'Building dynamic web applications.'),
(11, 'CSC301', 'Algorithms',                       3, 'Computer Science', '300', 'first',  1, 'Algorithm design and analysis.'),
(12, 'CSC302', 'Operating Systems',                3, 'Computer Science', '300', 'second', 1, 'Processes, memory and file systems.');

-- Prerequisites
INSERT INTO prerequisites (course_id, prerequisite_id) VALUES
(6, 2),   -- Data Structures      requires Intro to Programming
(7, 2),   -- OOP                  requires Intro to Programming
(8, 3),   -- Discrete Maths       requires Calculus I
(9, 1),   -- Database Systems     requires Intro to Computer Science
(10, 2),  -- Web Development      requires Intro to Programming
(11, 6),  -- Algorithms           requires Data Structures
(11, 8),  -- Algorithms           requires Discrete Maths
(12, 6);  -- Operating Systems    requires Data Structures

-- Academic records: Jane passed four 100-level courses, failed GST102.
INSERT INTO academic_records (student_id, course_id, semester, grade, grade_point, status) VALUES
(3, 1, '2023/2024-1', 'A', 5.00, 'passed'),  -- CSC101
(3, 2, '2023/2024-2', 'B', 4.00, 'passed'),  -- CSC102
(3, 3, '2023/2024-1', 'B', 4.00, 'passed'),  -- MTH101
(3, 4, '2023/2024-1', 'C', 3.00, 'passed'),  -- GST101
(3, 5, '2023/2024-2', 'F', 0.00, 'failed');  -- GST102 -> recommended as retake

-- Current registration for this semester.
INSERT INTO registrations (student_id, course_id, semester) VALUES
(3, 6, '2024/2025-1');  -- CSC201

-- Jane has submitted her plan; it awaits advisor review.
INSERT INTO approvals (student_id, advisor_id, semester, status) VALUES
(3, NULL, '2024/2025-1', 'pending');
