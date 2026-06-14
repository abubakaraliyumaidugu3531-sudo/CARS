-- =====================================================================
-- CARS - Incremental upgrade for an existing database
-- =====================================================================
-- Run this only if you already imported an earlier schema.sql and do NOT
-- want to re-import from scratch. It migrates the `approvals` table to the
-- per-semester plan-review model. (A fresh schema.sql import already matches.)
-- =====================================================================

-- Rebuild the approvals foreign keys so advisor_id can be nullable.
ALTER TABLE approvals DROP FOREIGN KEY approvals_ibfk_1;
ALTER TABLE approvals DROP FOREIGN KEY approvals_ibfk_2;

ALTER TABLE approvals
    MODIFY advisor_id INT NULL,
    ADD COLUMN semester VARCHAR(20) NOT NULL DEFAULT '2024/2025-1' AFTER advisor_id,
    ADD COLUMN comment VARCHAR(255) NULL AFTER status,
    ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER comment;

ALTER TABLE approvals
    ADD UNIQUE KEY unique_student_semester (student_id, semester);

ALTER TABLE approvals
    ADD CONSTRAINT fk_approvals_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    ADD CONSTRAINT fk_approvals_advisor FOREIGN KEY (advisor_id) REFERENCES users(id) ON DELETE SET NULL;
