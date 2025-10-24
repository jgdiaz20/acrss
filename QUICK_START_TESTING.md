# Quick Start Testing Guide

For testers who want to get started immediately

---

## STEP 1: ACCESS THE SYSTEM

URL: http://localhost:8000 (or your server URL)

Login Credentials:
- Admin: admin@admin.com / password
- Teacher: teacher@teacher.com / password

---

## STEP 2: PRIORITY TESTS

### Test A: Weekend Scheduling (CRITICAL)

1. Login as admin
2. Go to: Admin > Lessons > Add Lesson
3. Select a Senior High class (e.g., STEM 11-A)
4. Try to select Saturday or Sunday
5. Expected: Saturday/Sunday should not appear in dropdown OR validation error if selected
6. Result: PASS / FAIL

7. Now select a Diploma class
8. Try to select Saturday or Sunday
9. Expected: Can select and save successfully
10. Result: PASS / FAIL

---

### Test B: Inline Editing Weekend Validation (CRITICAL)

1. Go to: Admin > Room Management > Room Timetable
2. Enable Edit Mode
3. Click Saturday tab
4. Click empty time slot
5. Select Senior High class
6. Click Save
7. Expected: Red error box appears with message about weekend restriction
8. Result: PASS / FAIL

9. Close modal and reopen
10. Expected: Error is gone, form is clean
11. Result: PASS / FAIL

---

### Test C: Conflict Detection (HIGH PRIORITY)

1. Create a lesson: Teacher John, Monday 8-9 AM
2. Try to create another: Same teacher, Monday 8:30-9:30 AM
3. Expected: Conflict warning appears
4. Result: PASS / FAIL

---

### Test D: Master Timetable Navigation (MEDIUM PRIORITY)

1. Go to: Admin > Room Management > Master Timetable
2. Click through all 7 day tabs (Mon-Sun)
3. Expected: All tabs work, Saturday and Sunday tabs visible
4. Result: PASS / FAIL

---

## STEP 3: FULL TESTING

Open TESTING_GUIDE_FINAL.md and follow all tests in order

---

## COMMON ISSUES TO WATCH FOR

1. Weekend lessons for Senior High or College (should NOT work)
2. Weekend lessons for Diploma (should work)
3. Error messages not clearing when modal closes
4. Conflicts not being detected
5. Validation errors not showing clearly
6. Buttons not working as expected

---

## REPORTING ISSUES

For each issue found, record:
- Test number (from TESTING_GUIDE_FINAL.md)
- What you did (steps)
- What happened (actual result)
- What should have happened (expected result)
- Screenshots if possible

---

## NEED HELP?

Refer to:
- TESTING_GUIDE_FINAL.md - Complete testing procedures
- TECHNICAL_OVERVIEW.md - System documentation
- WEEKEND_SCHEDULE_IMPLEMENTATION.md - Weekend feature details

---

Ready to start? Open TESTING_GUIDE_FINAL.md and begin with Section 1!
