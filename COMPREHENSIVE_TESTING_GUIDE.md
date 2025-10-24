# Comprehensive Testing Guide
Laravel School Timetable System

**Version:** 3.0  
**Last Updated:** October 24, 2025

## Table of Contents
1. [How to Use This Guide](#how-to-use-this-guide)
2. [Authentication & Access Control](#1-authentication--access-control)
3. [User Management](#2-user-management)
4. [Academic Program Management](#3-academic-program-management)
5. [Class Management](#4-class-management)
6. [Subject Management](#5-subject-management)
7. [Room Management](#6-room-management)
8. [Lesson Management](#7-lesson-management)
9. [Timetable Management](#8-timetable-management)
10. [Conflict Detection](#9-conflict-detection)
11. [UI/UX Testing](#10-uiux-testing)
12. [Mobile Responsiveness](#11-mobile-responsiveness)

## How to Use This Guide
1. Follow each test section in order
2. Mark results as ✅ PASS, ❌ FAIL, ⏩ SKIP, or ❔ N/A
3. Record actual results in the Result field
4. Note any issues in the Notes field
5. Provide feedback in the Feedback field

---

## 1. Authentication & Access Control

### TEST 1.1: Admin Login
**Objective:** Verify admin can login and access admin dashboard

**Steps:**
1. Navigate to `/login`
2. Enter admin credentials (Email: admin@admin.com, Password: password)
3. Click Login button

**Expected Result:**
- Redirected to `/admin` dashboard
- Admin menu with all management options visible
- Username appears in top right corner

**Result:** 
**Notes:** 
**Feedback:**

### TEST 1.2: Teacher Login
**Objective:** Verify teacher can login and access teacher dashboard

**Steps:**
1. Logout from admin account
2. Navigate to `/login`
3. Enter teacher credentials
4. Click Login button

**Expected Result:**
- Redirected to `/teacher` dashboard
- Teacher-specific menu visible
- Can view personal schedule

**Result:** 
**Notes:** 
**Feedback:**

### TEST 1.3: Unauthorized Access Prevention
**Objective:** Verify non-admin users cannot access admin pages

**Steps:**
1. Login as teacher
2. Manually navigate to `/admin/lessons`

**Expected Result:**
- 403 Forbidden error or redirect to teacher dashboard
- Error message displayed

**Result:** 
**Notes:** 
**Feedback:**

---

## 2. User Management

### TEST 2.1: Create User with .edu.ph Email (New Validation)
**Objective:** Verify only .edu.ph emails are accepted for new users

**Steps:**
1. Login as admin
2. Navigate to Admin > User Management
3. Click Add User
4. Fill form with non-.edu.ph email (e.g., test@gmail.com)
5. Click Save

**Expected Result:**
- Validation error: "Please use a valid .edu.ph email address"
- Form not submitted

**Result:** 
**Notes:** 
**Feedback:**

### TEST 2.2: Update Existing User Email (Legacy Support)
**Objective:** Verify existing users can keep non-.edu.ph emails

**Steps:**
1. Login as admin
2. Edit admin@admin.com user
3. Change name only (leave email)
4. Click Update

**Expected Result:**
- Update successful
- No email validation error for existing users

**Result:** 
**Notes:** 
**Feedback:**

---

## 7. Lesson Management

### TEST 7.1: Create New Lesson
**Objective:** Verify lesson creation with valid data

**Steps:**
1. Navigate to Admin > Lessons > Add Lesson
2. Fill in all required fields (Class, Subject, Teacher, Room, Weekday, Start/End Time)
3. Click Save

**Expected Result:**
- Lesson is created successfully
- Success message appears
- Lesson appears in the lessons list

**Result:** 
**Notes:** 
**Feedback:**

### TEST 7.2: Edit Existing Lesson
**Objective:** Verify lesson details can be updated

**Steps:**
1. Find an existing lesson in the list
2. Click Edit
3. Change the room and save

**Expected Result:**
- Changes are saved successfully
- Updated information appears in the list
- No conflicts should be introduced

**Result:** 
**Notes:** 
**Feedback:**

### TEST 7.3: Delete Lesson
**Objective:** Verify lesson can be deleted

**Steps:**
1. Find an existing lesson
2. Click Delete
3. Confirm deletion

**Expected Result:**
- Lesson is removed from the list
- Success message appears
- No orphaned references remain

**Result:** 
**Notes:** 
**Feedback:**

### TEST 7.4: Weekend Lesson Validation (Fix #4)
**Objective:** Verify weekend error clears when switching to Diploma program

**Steps:**
1. Create new lesson
2. Select Senior High class
3. Select Saturday as weekday
4. Note error appears
5. Change class to Diploma program

**Expected Result:**
- Error message clears automatically
- Weekend selection becomes valid
- Form can be submitted

**Result:** 
**Notes:** 
**Feedback:**

### TEST 7.2: Error Banner Persistence (Fix #5)
**Objective:** Verify validation errors clear when conflicts are resolved

**Steps:**
1. Create lesson with room conflict
2. Note error banner appears
3. Change room to available one

**Expected Result:**
- Error banner disappears
- Submit button re-enables
- Form can be submitted

**Result:** 
**Notes:** 
**Feedback:**

---

## 9. Conflict Detection

### TEST 9.1: Room Conflict Detection
**Objective:** Verify system detects room double-booking

**Steps:**
1. Create lesson in Room 101, Monday 9:00-10:00
2. Try to create another lesson in same room/time

**Expected Result:**
- Orange conflict banner appears
- Specific error about room conflict
- Submit button disabled

**Result:** 
**Notes:** 
**Feedback:**

### TEST 9.2: Teacher Conflict Detection
**Objective:** Verify system detects teacher double-booking

**Steps:**
1. Create lesson with Teacher A, Monday 9:00-10:00
2. Try to create another lesson with same teacher/time

**Expected Result:**
- Orange conflict banner appears
- Specific error about teacher conflict
- Submit button disabled

**Result:** 
**Notes:** 
**Feedback:**

### TEST 9.3: Class Conflict Detection
**Objective:** Verify system detects class double-booking

**Steps:**
1. Create lesson for Class 10A, Monday 9:00-10:00
2. Try to create another lesson for same class/time

**Expected Result:**
- Orange conflict banner appears
- Specific error about class conflict
- Submit button disabled

**Result:** 
**Notes:** 
**Feedback:**

### TEST 9.4: Conflict Resolution
**Objective:** Verify conflicts can be resolved

**Steps:**
1. Create a conflict (room/teacher/class)
2. Change conflicting field (room/time)
3. Save changes

**Expected Result:**
- Conflict banner disappears
- Submit button enables
- Changes save successfully

**Result:** 
**Notes:** 
**Feedback:**

### TEST 9.1: Room Conflict Detection
**Objective:** Verify system detects room double-booking

**Steps:**
1. Create lesson in Room 101, Monday 9:00-10:00
2. Try to create another lesson in same room/time

**Expected Result:**
- Orange conflict banner appears
- Specific error about room conflict
- Submit button disabled

**Result:** 
**Notes:** 
**Feedback:**

### TEST 9.2: Teacher Conflict Detection
**Objective:** Verify system detects teacher double-booking

**Steps:**
1. Create lesson with Teacher A, Monday 9:00-10:00
2. Try to create another lesson with same teacher/time

**Expected Result:**
- Orange conflict banner appears
- Specific error about teacher conflict
- Submit button disabled

**Result:** 
**Notes:** 
**Feedback:**

---

## 10. UI/UX Testing

### TEST 10.1: Form Validation Messages
**Objective:** Verify clear validation messages

**Steps:**
1. Leave required fields empty
2. Submit form
3. Check error messages

**Expected Result:**
- Clear error messages under each field
- Error summary at top
- Fields highlighted in red

**Result:** 
**Notes:** 
**Feedback:**

### TEST 10.2: Form Persistence on Error
**Objective:** Verify form data persists after validation error

**Steps:**
1. Fill lesson form with one required field missing
2. Submit form
3. Fix error and resubmit

**Expected Result:**
- All previously entered data remains in form
- Only missing/incorrect data needs to be fixed
- No data loss on validation error

**Result:** 
**Notes:** 
**Feedback:**

### TEST 10.3: Error Message Styling (Fix #3)
**Objective:** Verify different error types have distinct styling

**Steps:**
1. Trigger validation error (e.g., empty required field)
2. Trigger conflict error (e.g., room conflict)

**Expected Result:**
- Validation errors: Yellow banner
- Conflict errors: Orange banner
- Field-level errors: Red text below field

**Result:** 
**Notes:** 
**Feedback:**

### TEST 10.1: Error Message Styling (Fix #3)
**Objective:** Verify different error types have distinct styling

**Steps:**
1. Trigger validation error (e.g., empty required field)
2. Trigger conflict error (e.g., room conflict)

**Expected Result:**
- Validation errors: Yellow banner
- Conflict errors: Orange banner
- Field-level errors: Red text below field

**Result:** 
**Notes:** 
**Feedback:**

### TEST 10.2: Form Persistence on Error
**Objective:** Verify form data persists after validation error

**Steps:**
1. Fill lesson form with one required field missing
2. Submit form
3. Fix error and resubmit

**Expected Result:**
- All previously entered data remains in form
- Only missing/incorrect data needs to be fixed

**Result:** 
**Notes:** 
**Feedback:**

---

## 11. Mobile Responsiveness

### TEST 11.1: Filter Panel (Mobile)
**Objective:** Verify filter panel works on mobile

**Steps:**
1. Access on mobile device or use browser dev tools
2. Open any list page (e.g., /admin/lessons)
3. Click "Show Filters"
4. Apply filters

**Expected Result:**
- Filter panel collapses/expands properly
- All filter controls usable on mobile
- Results update correctly

**Result:** 
**Notes:** 
**Feedback:**

### TEST 11.2: Inline Editing (Mobile)
**Objective:** Verify inline editing works on mobile

**Steps:**
1. On mobile, navigate to Room Timetable
2. Click on a timeslot
3. Try to edit lesson details

**Expected Result:**
- Edit modal opens properly
- Form controls are usable
- Can save/cancel changes

**Result:** 
**Notes:** 
**Feedback:**

### TEST 11.3: Navigation (Mobile)
**Objective:** Verify mobile menu works

**Steps:**
1. Resize to mobile view
2. Click hamburger menu
3. Navigate through different sections

**Expected Result:**
- Menu opens/closes smoothly
- All menu items accessible
- Active page highlighted

**Result:** 
**Notes:** 
**Feedback:**

### TEST 11.1: Filter Panel (Mobile)
**Objective:** Verify filter panel works on mobile

**Steps:**
1. Access on mobile device or use browser dev tools
2. Open any list page (e.g., /admin/lessons)
3. Click "Show Filters"
4. Apply filters

**Expected Result:**
- Filter panel collapses/expands properly
- All filter controls usable on mobile
- Results update correctly

**Result:** 
**Notes:** 
**Feedback:**

### TEST 11.2: Inline Editing (Mobile)
**Objective:** Verify inline editing works on mobile

**Steps:**
1. On mobile, navigate to Room Timetable
2. Click on a timeslot
3. Try to edit lesson details

**Expected Result:**
- Edit modal opens properly
- Form controls are usable
- Can save/cancel changes

**Result:** 
**Notes:** 
**Feedback:**

---

## Test Summary

### Totals
- Total Tests: 12
- Passed: 
- Failed: 
- Skipped: 
- Not Applicable: 

### Critical Issues Found:
1. 
2. 
3. 

### Recommendations:
1. 
2. 
3. 

### Tester Notes:
- 
- 
- 

---

**End of Testing Guide**
