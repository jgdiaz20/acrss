@echo off
REM Documentation Cleanup Script
REM Removes outdated and redundant documentation files
REM Created: October 17, 2025

echo ========================================
echo Documentation Cleanup Script
echo ========================================
echo.
echo This script will remove 10 outdated documentation files.
echo.
echo Files to be removed:
echo 1. COMPREHENSIVE_TESTING_GUIDE.md
echo 2. COMPREHENSIVE_SYSTEM_TESTING_GUIDE.md
echo 3. CLOSE_BUTTON_INTEGRATION_SUMMARY.md
echo 4. CONFLICT_MODAL_IMPROVEMENTS_SUMMARY.md
echo 5. TIME_SLOT_IMPROVEMENTS_SUMMARY.md
echo 6. TEACHER_ASSIGNMENT_CACHE_FIX.md
echo 7. MASTER_TIMETABLE_SUMMARY.md
echo 8. DIPLOMA_PROGRAM_IMPLEMENTATION.md
echo 9. COURSE_BASED_SUBJECT_FILTERING_ANALYSIS.md
echo 10. COURSE_BASED_SUBJECT_FILTERING_IMPLEMENTATION_GUIDE.md
echo.
echo Press Ctrl+C to cancel or
pause

echo.
echo Starting cleanup...
echo.

REM Remove outdated testing guides
if exist "COMPREHENSIVE_TESTING_GUIDE.md" (
    del "COMPREHENSIVE_TESTING_GUIDE.md"
    echo [OK] Removed COMPREHENSIVE_TESTING_GUIDE.md
) else (
    echo [SKIP] COMPREHENSIVE_TESTING_GUIDE.md not found
)

if exist "COMPREHENSIVE_SYSTEM_TESTING_GUIDE.md" (
    del "COMPREHENSIVE_SYSTEM_TESTING_GUIDE.md"
    echo [OK] Removed COMPREHENSIVE_SYSTEM_TESTING_GUIDE.md
) else (
    echo [SKIP] COMPREHENSIVE_SYSTEM_TESTING_GUIDE.md not found
)

REM Remove completed feature summaries
if exist "CLOSE_BUTTON_INTEGRATION_SUMMARY.md" (
    del "CLOSE_BUTTON_INTEGRATION_SUMMARY.md"
    echo [OK] Removed CLOSE_BUTTON_INTEGRATION_SUMMARY.md
) else (
    echo [SKIP] CLOSE_BUTTON_INTEGRATION_SUMMARY.md not found
)

if exist "CONFLICT_MODAL_IMPROVEMENTS_SUMMARY.md" (
    del "CONFLICT_MODAL_IMPROVEMENTS_SUMMARY.md"
    echo [OK] Removed CONFLICT_MODAL_IMPROVEMENTS_SUMMARY.md
) else (
    echo [SKIP] CONFLICT_MODAL_IMPROVEMENTS_SUMMARY.md not found
)

if exist "TIME_SLOT_IMPROVEMENTS_SUMMARY.md" (
    del "TIME_SLOT_IMPROVEMENTS_SUMMARY.md"
    echo [OK] Removed TIME_SLOT_IMPROVEMENTS_SUMMARY.md
) else (
    echo [SKIP] TIME_SLOT_IMPROVEMENTS_SUMMARY.md not found
)

if exist "TEACHER_ASSIGNMENT_CACHE_FIX.md" (
    del "TEACHER_ASSIGNMENT_CACHE_FIX.md"
    echo [OK] Removed TEACHER_ASSIGNMENT_CACHE_FIX.md
) else (
    echo [SKIP] TEACHER_ASSIGNMENT_CACHE_FIX.md not found
)

if exist "MASTER_TIMETABLE_SUMMARY.md" (
    del "MASTER_TIMETABLE_SUMMARY.md"
    echo [OK] Removed MASTER_TIMETABLE_SUMMARY.md
) else (
    echo [SKIP] MASTER_TIMETABLE_SUMMARY.md not found
)

if exist "DIPLOMA_PROGRAM_IMPLEMENTATION.md" (
    del "DIPLOMA_PROGRAM_IMPLEMENTATION.md"
    echo [OK] Removed DIPLOMA_PROGRAM_IMPLEMENTATION.md
) else (
    echo [SKIP] DIPLOMA_PROGRAM_IMPLEMENTATION.md not found
)

REM Remove analysis documents
if exist "COURSE_BASED_SUBJECT_FILTERING_ANALYSIS.md" (
    del "COURSE_BASED_SUBJECT_FILTERING_ANALYSIS.md"
    echo [OK] Removed COURSE_BASED_SUBJECT_FILTERING_ANALYSIS.md
) else (
    echo [SKIP] COURSE_BASED_SUBJECT_FILTERING_ANALYSIS.md not found
)

if exist "COURSE_BASED_SUBJECT_FILTERING_IMPLEMENTATION_GUIDE.md" (
    del "COURSE_BASED_SUBJECT_FILTERING_IMPLEMENTATION_GUIDE.md"
    echo [OK] Removed COURSE_BASED_SUBJECT_FILTERING_IMPLEMENTATION_GUIDE.md
) else (
    echo [SKIP] COURSE_BASED_SUBJECT_FILTERING_IMPLEMENTATION_GUIDE.md not found
)

echo.
echo ========================================
echo Cleanup Complete!
echo ========================================
echo.
echo Remaining documentation files:
echo - README.md
echo - TECHNICAL_OVERVIEW.md
echo - TESTING_GUIDE_FINAL.md
echo - WEEKEND_SCHEDULE_IMPLEMENTATION.md
echo - MASTER_TIMETABLE_IMPLEMENTATION_GUIDE.md
echo - ERROR_CLEARING_FIX.md (archive after 30 days)
echo - INLINE_EDITING_ERROR_DISPLAY.md (archive after 30 days)
echo - INLINE_EDITING_WEEKEND_FIX.md (archive after 30 days)
echo - CLEANUP_SUMMARY.md
echo.
echo See CLEANUP_SUMMARY.md for details.
echo.
pause
