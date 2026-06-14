-- =====================================================================
-- CARS - Demonstration / Evaluation Seed Data
-- =====================================================================
-- Run AFTER schema.sql. Re-runnable: clears existing rows first.
-- All seed accounts use the password: password123
-- (hash below is bcrypt for "password123").
--
-- Demo accounts:
--   admin@cars.test    / password123  (admin)
--   advisor@cars.test  / password123  (advisor)
--   jane@cars.test     / password123  (student, 200 level Computer Science)
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE recommendations;
TRUNCATE TABLE approvals;
TRUNCATE TABLE registrations;
TRUNCATE TABLE academic_records;
TRUNCATE TABLE prerequisites;
TRUNCATE TABLE courses;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------ USERS --------------------------------
INSERT INTO users (id, name, email, password, role, department, level) VALUES
(1, 'System Admin', 'admin@cars.test',   '$2y$10$OF00uXLDdiAnnhxMYFvtkujfjNTmhkNloYwVEXWOooqyjtEyvIasi', 'admin',   NULL, NULL),
(2, 'Dr. Advisor',  'advisor@cars.test', '$2y$10$OF00uXLDdiAnnhxMYFvtkujfjNTmhkNloYwVEXWOooqyjtEyvIasi', 'advisor', 'Computer Science', NULL),
(3, 'Jane Student', 'jane@cars.test',    '$2y$10$OF00uXLDdiAnnhxMYFvtkujfjNTmhkNloYwVEXWOooqyjtEyvIasi', 'student', 'Computer Science', '200');

-- ----------------------------- COURSES -------------------------------
INSERT INTO courses (id, code, title, credit_unit, department, level, semester, is_core, description) VALUES
-- 100 level
(1,  'CSC101', 'Introduction to Computer Science', 3, 'Computer Science', '100', 'first',  1, 'Foundations of computing and problem solving.'),
(2,  'CSC102', 'Introduction to Programming',      3, 'Computer Science', '100', 'second', 1, 'Programming fundamentals using a high-level language.'),
(3,  'MTH101', 'Calculus I',                       3, 'Computer Science', '100', 'first',  1, 'Limits, differentiation and integration.'),
(4,  'GST101', 'Use of English',                   2, 'Computer Science', '100', 'first',  1, 'Communication and academic writing skills.'),
(5,  'GST102', 'Communication Skills II',          2, 'Computer Science', '100', 'second', 1, 'Oral and written communication.'),
-- 200 level
(6,  'CSC201', 'Data Structures',                  3, 'Computer Science', '200', 'first',  1, 'Linear and non-linear data structures.'),
(7,  'CSC202', 'Object Oriented Programming',      3, 'Computer Science', '200', 'second', 1, 'Classes, objects, inheritance and polymorphism.'),
(8,  'CSC203', 'Discrete Mathematics',             3, 'Computer Science', '200', 'first',  1, 'Logic, sets, relations and graph theory.'),
(9,  'CSC204', 'Database Systems',                 3, 'Computer Science', '200', 'second', 1, 'Relational model, SQL and database design.'),
(10, 'CSC205', 'Web Development',                  2, 'Computer Science', '200', 'first',  0, 'Building dynamic web applications.'),
-- 300 level
(11, 'CSC301', 'Algorithms',                       3, 'Computer Science', '300', 'first',  1, 'Algorithm design and analysis.'),
(12, 'CSC302', 'Operating Systems',                3, 'Computer Science', '300', 'second', 1, 'Processes, memory and file systems.');

-- -------------------------- PREREQUISITES ----------------------------
INSERT INTO prerequisites (course_id, prerequisite_id) VALUES
(6, 2),   -- Data Structures        requires Intro to Programming
(7, 2),   -- OOP                    requires Intro to Programming
(8, 3),   -- Discrete Mathematics   requires Calculus I
(9, 1),   -- Database Systems       requires Intro to Computer Science
(10, 2),  -- Web Development        requires Intro to Programming
(11, 6),  -- Algorithms             requires Data Structures
(11, 8),  -- Algorithms             requires Discrete Mathematics
(12, 6);  -- Operating Systems      requires Data Structures

-- ----------------------- ACADEMIC RECORDS ----------------------------
-- Jane (id 3) completed 100 level: passed four courses, failed one (GST102).
INSERT INTO academic_records (student_id, course_id, semester, grade, grade_point, status) VALUES
(3, 1, '2023/2024-1', 'A', 5.00, 'passed'),  -- CSC101
(3, 2, '2023/2024-2', 'B', 4.00, 'passed'),  -- CSC102
(3, 3, '2023/2024-1', 'B', 4.00, 'passed'),  -- MTH101
(3, 4, '2023/2024-1', 'C', 3.00, 'passed'),  -- GST101
(3, 5, '2023/2024-2', 'F', 0.00, 'failed');  -- GST102 -> should be recommended as retake

-- ----------------------- CURRENT REGISTRATION ------------------------
-- Jane is currently registered for Data Structures this semester.
INSERT INTO registrations (student_id, course_id, semester) VALUES
(3, 6, '2024/2025-1');  -- CSC201

-- ------------------------- PLAN FOR APPROVAL -------------------------
-- Jane has submitted her current-semester plan; it awaits advisor review,
-- so the advisor queue is populated on first login.
INSERT INTO approvals (student_id, advisor_id, semester, status) VALUES
(3, NULL, '2024/2025-1', 'pending');
