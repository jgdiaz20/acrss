-- ========================================================================
-- STUDENT DATA CLEANUP SCRIPT
-- Laravel School Timetable System
-- ========================================================================
-- This script removes all student-related data from the database
-- Run this BEFORE implementing code changes
-- ========================================================================

-- STEP 1: Remove specific student user (jhon@gmail.com)
-- ========================================================================
DELETE FROM users WHERE email = 'jhon@gmail.com';

-- STEP 2: Remove all users with student role (role_id = 4)
-- ========================================================================
DELETE FROM role_user WHERE role_id = 4;

-- STEP 3: Remove all users with is_student = 1
-- ========================================================================
DELETE FROM users WHERE is_student = 1;

-- STEP 4: Clean up orphaned school classes
-- ========================================================================
-- IMPORTANT: Review ALL classes first to identify which ones to delete
-- DO NOT use broad patterns like '%2025%' - too dangerous!

-- Show ALL school classes with their details
SELECT sc.id, sc.name, sc.is_active,
       ap.name as program_name, 
       gl.name as grade_level,
       COUNT(u.id) as student_count
FROM school_classes sc
LEFT JOIN academic_programs ap ON sc.program_id = ap.id
LEFT JOIN grade_levels gl ON sc.grade_level_id = gl.id
LEFT JOIN users u ON sc.id = u.class_id
GROUP BY sc.id, sc.name, sc.is_active, ap.name, gl.name
ORDER BY ap.name, gl.name, sc.name;

-- Find classes with students enrolled
SELECT sc.name as class_name, COUNT(u.id) as student_count
FROM school_classes sc
LEFT JOIN users u ON sc.id = u.class_id
GROUP BY sc.id, sc.name
HAVING COUNT(u.id) > 0;

-- To delete SPECIFIC class (only after reviewing above results):
-- DELETE FROM school_classes WHERE name = 'STEM 2025 B';
-- DELETE FROM school_classes WHERE id = X;  -- Use ID for precision

-- STEP 5: Verify cleanup
-- ========================================================================
-- Check remaining users
SELECT id, name, email, is_admin, is_teacher, is_student, class_id 
FROM users 
ORDER BY is_admin DESC, is_teacher DESC;

-- Check school classes
SELECT sc.id, sc.name, ap.name as program_name, gl.name as grade_level
FROM school_classes sc
LEFT JOIN academic_programs ap ON sc.program_id = ap.id
LEFT JOIN grade_levels gl ON sc.grade_level_id = gl.id
ORDER BY ap.name, gl.name, sc.name;

-- Check for any remaining student role assignments
SELECT COUNT(*) as student_role_count FROM role_user WHERE role_id = 4;

-- ========================================================================
-- NOTES:
-- ========================================================================
-- 1. BACKUP YOUR DATABASE BEFORE RUNNING THIS SCRIPT!
-- 2. Review the SELECT queries first to see what will be deleted
-- 3. Uncomment DELETE statements one at a time
-- 4. Run verification queries after each deletion
-- ========================================================================
