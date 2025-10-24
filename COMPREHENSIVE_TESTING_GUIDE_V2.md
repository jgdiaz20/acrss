# Comprehensive Testing Guide
## Laravel School Timetable System

**Version:** 4.0  
**Last Updated:** December 2024  
**Target:** Admin and Teacher Features Only (Students Excluded)

---

## 📋 How to Use This Guide

1. **Follow each test section in order**
2. **Mark results as:** ✅ PASS, ❌ FAIL, ⏩ SKIP, or ❔ N/A
3. **Record actual results** in the Result field
4. **Note any issues** in the Notes field
5. **Provide feedback** in the Feedback field
6. **Test with seeded data:** admin@admin.com / password, teacher@teacher.com / password

---

## 🔧 Implemented Fixes Testing Priority

**CRITICAL:** The following fixes have been implemented and MUST be tested thoroughly:

### 🟠 Orange Conflict Banner (FIX #1)
- **Location:** Sections 11.1-11.2
- **Purpose:** Distinct orange banner for scheduling conflicts
- **Visual:** Orange gradient background, clock icon, dismissible
- **Expected:** Appears for teacher/room/class conflicts

### 📧 Email Validation (.edu.ph) (FIX #2)
- **Location:** Section 12 (Email Validation Testing)
- **Purpose:** Enforce institutional email format
- **Rule:** New users must use .edu.ph emails
- **Exception:** Existing users can keep current emails

### 🟡 Duplicate Error Prevention (FIX #3)
- **Location:** Sections 11.3-11.4
- **Purpose:** Different styling for error levels
- **Visual:** Yellow banner (summary) + Red field errors (details)
- **Expected:** No duplicate error messages

### 📅 Weekend Error Persistence (FIX #4)
- **Location:** Section 11.5
- **Purpose:** Clear weekend errors when switching to Diploma
- **Behavior:** Error disappears when changing to Diploma class
- **Expected:** Smooth error clearing

### 🔄 Validation Banner Persistence (FIX #5)
- **Location:** Section 11.6
- **Purpose:** Hide error banners when conflicts resolved
- **Behavior:** Banner disappears when all errors fixed
- **Expected:** Submit button enabled after fixing errors

---

## 📚 Table of Contents

1. [Authentication & Access Control](#1-authentication--access-control)
2. [Dashboard Functionality](#2-dashboard-functionality)
3. [User Management](#3-user-management)
4. [Academic Program Management](#4-academic-program-management)
5. [Class Management](#5-class-management)
6. [Subject Management](#6-subject-management)
7. [Room Management](#7-room-management)
8. [Lesson Management](#8-lesson-management)
9. [Timetable Management](#9-timetable-management)
10. [Conflict Detection & Validation](#10-conflict-detection--validation)
11. [Validation Error System Testing](#11-validation-error-system-testing)
12. [Email Validation Testing](#12-email-validation-testing)
13. [Pagination & Data Display](#13-pagination--data-display)
14. [Export & Print Features](#14-export--print-features)
15. [Public Features](#15-public-features)
16. [UI/UX Testing](#16-uiux-testing)
17. [Mobile Responsiveness](#17-mobile-responsiveness)
18. [Performance Testing](#18-performance-testing)

---

## 1. Authentication & Access Control

### TEST 1.1: Admin Login
**Objective:** Verify admin can login and access admin dashboard

**Steps:**
1. Navigate to `/login`
2. Enter admin credentials (Email: admin@admin.com, Password: password)
3. Click Login button
4. Observe redirect and dashboard

**Expected Result:**
- Redirected to `/admin` dashboard
- Admin menu with all management options visible
- Username appears in top right corner
- All admin features accessible

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 1.2: Teacher Login
**Objective:** Verify teacher can login and access teacher dashboard

**Steps:**
1. Logout from admin account
2. Navigate to `/login`
3. Enter teacher credentials (Email: teacher@teacher.com, Password: password)
4. Click Login button
5. Observe redirect and dashboard

**Expected Result:**
- Redirected to `/teacher` dashboard
- Teacher-specific menu visible
- Teacher can view their schedule
- Limited access to admin features

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 1.3: Unauthorized Access Prevention
**Objective:** Verify non-admin users cannot access admin pages

**Steps:**
1. Login as teacher
2. Manually navigate to `/admin/lessons`
3. Observe response
4. Try accessing `/admin/users`
5. Observe response

**Expected Result:**
- Access denied with 403 Forbidden error
- Or redirect to appropriate dashboard
- Clear error message displayed

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 1.4: Session Management
**Objective:** Verify session handling works correctly

**Steps:**
1. Login as admin
2. Leave browser idle for extended period
3. Try to access admin page
4. Observe behavior
5. Login again and verify functionality

**Expected Result:**
- Session expires appropriately
- User redirected to login page
- Can login again successfully

**Result:** 
**Notes:** 
**Feedback:**

---

## 2. Dashboard Functionality

### TEST 2.1: Admin Dashboard
**Objective:** Verify admin dashboard displays correct information

**Steps:**
1. Login as admin
2. Navigate to `/admin`
3. Observe dashboard content
4. Check statistics and widgets
5. Verify navigation menu

**Expected Result:**
- Dashboard loads correctly
- Statistics are accurate
- All menu items visible
- Quick access buttons work

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 2.2: Teacher Dashboard
**Objective:** Verify teacher dashboard shows relevant information

**Steps:**
1. Login as teacher
2. Navigate to `/teacher`
3. Observe dashboard content
4. Check today's classes
5. Check upcoming classes
6. Verify schedule display

**Expected Result:**
- Dashboard shows teacher's schedule
- Today's classes displayed
- Upcoming classes shown
- Schedule is accurate

**Result:** 
**Notes:** 
**Feedback:**

---

## 3. User Management

### TEST 3.1: View All Users
**Objective:** Verify admin can view all users with pagination

**Steps:**
1. Navigate to Admin > Users
2. Observe user list
3. Test pagination controls
4. Test per-page selector (10, 20, 50, 100)
5. Verify user count display

**Expected Result:**
- All users displayed in table
- Pagination works correctly
- Per-page selector functions
- User count accurate

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 3.2: Create New User
**Objective:** Verify admin can create new users

**Steps:**
1. Navigate to Admin > Users
2. Click "Add User" button
3. Fill in form:
   - Name: Test Teacher
   - Email: test@teacher.com
   - Password: password
   - Role: Teacher
4. Click Save button

**Expected Result:**
- Success message appears
- User created successfully
- User appears in list
- Can login with new credentials

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 3.3: Edit User
**Objective:** Verify admin can edit user information

**Steps:**
1. Find the user created in Test 3.2
2. Click Edit button
3. Change name to "Updated Teacher"
4. Click Update button

**Expected Result:**
- Success message appears
- User information updated
- Changes reflected in list

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 3.4: Delete User
**Objective:** Verify admin can delete users with confirmation

**Steps:**
1. Find the user created in Test 3.2
2. Click Delete button
3. Confirm deletion in modal
4. Observe result

**Expected Result:**
- Confirmation modal appears
- User deleted after confirmation
- Success message shown
- User removed from list

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 3.5: Filter Users by Role
**Objective:** Verify user filtering by role works

**Steps:**
1. Navigate to Admin > Users
2. Click on "Teachers" menu item
3. Observe filtered results
4. Click on "All Users" to clear filter

**Expected Result:**
- Only teachers displayed
- Filter works correctly
- Can clear filter

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 3.6: User Search
**Objective:** Verify user search functionality

**Steps:**
1. Navigate to Admin > Users
2. Use search functionality (if available)
3. Search for specific user name
4. Verify results

**Expected Result:**
- Search returns relevant results
- Search works correctly
- Can clear search

**Result:** 
**Notes:** 
**Feedback:**

---

## 4. Academic Program Management

### TEST 4.1: View Academic Programs
**Objective:** Verify admin can view all academic programs

**Steps:**
1. Navigate to Admin > Classes > Manage Programs
2. Observe program list
3. Check program details
4. Verify pagination (if applicable)

**Expected Result:**
- All programs displayed
- Program details visible
- Pagination works correctly

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 4.2: Create Academic Program
**Objective:** Verify admin can create new academic programs

**Steps:**
1. Navigate to Admin > Classes > Manage Programs
2. Click "Add Academic Program" button
3. Fill in form:
   - Name: Bachelor of Science in Computer Science
   - Short Name: BSCS
   - Type: College
   - Description: 4-year Computer Science program
4. Click Save button

**Expected Result:**
- Success message appears
- Program created successfully
- Program appears in list
- All details correct

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 4.3: Edit Academic Program
**Objective:** Verify admin can edit existing programs

**Steps:**
1. Find the program created in Test 4.2
2. Click Edit button
3. Change name to "BS Computer Science"
4. Click Update button

**Expected Result:**
- Success message appears
- Program updated successfully
- Changes reflected in list

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 4.4: Delete Academic Program
**Objective:** Verify program deletion with dependency check

**Steps:**
1. Try to delete a program with associated classes
2. Observe error message
3. Try to delete a program without classes
4. Confirm deletion

**Expected Result:**
- Cannot delete program with classes
- Clear error message shown
- Can delete program without classes

**Result:** 
**Notes:** 
**Feedback:**

---

## 5. Class Management

### TEST 5.1: View Classes by Program
**Objective:** Verify class viewing by program type

**Steps:**
1. Navigate to Admin > Classes > Senior High School
2. Observe class list
3. Navigate to Admin > Classes > Diploma Program
4. Observe class list
5. Navigate to Admin > Classes > College
6. Observe class list

**Expected Result:**
- Classes filtered by program type
- Correct classes displayed for each program
- Navigation works correctly

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 5.2: Create Senior High Class
**Objective:** Verify admin can create Senior High classes

**Steps:**
1. Navigate to Admin > Classes > Senior High School
2. Click "Add Class" button
3. Fill in form:
   - Name: STEM 12-A
   - Program: Senior High School
   - Grade Level: Grade 12
   - Max Students: 40
4. Click Save button

**Expected Result:**
- Success message appears
- Class created successfully
- Class appears in Senior High list
- All details correct

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 5.3: Create Diploma Class
**Objective:** Verify admin can create Diploma classes

**Steps:**
1. Navigate to Admin > Classes > Diploma Program
2. Click "Add Class" button
3. Fill in form:
   - Name: Business Administration - 2nd Year
   - Program: Select Diploma program
   - Grade Level: Select appropriate level
   - Max Students: 35
4. Click Save button

**Expected Result:**
- Success message appears
- Class created successfully
- Class appears in Diploma list
- All details correct

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 5.4: Edit Class
**Objective:** Verify admin can edit class information

**Steps:**
1. Find a class created in previous tests
2. Click Edit button
3. Change max students to 45
4. Click Update button

**Expected Result:**
- Success message appears
- Class updated successfully
- Changes reflected in list

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 5.5: Delete Class
**Objective:** Verify class deletion with dependency check

**Steps:**
1. Try to delete a class with associated lessons
2. Observe error message
3. Try to delete a class without lessons
4. Confirm deletion

**Expected Result:**
- Cannot delete class with lessons
- Clear error message shown
- Can delete class without lessons

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 5.6: Class Search and Filter
**Objective:** Verify class search and filtering

**Steps:**
1. Navigate to any class program page
2. Use search functionality (if available)
3. Search for specific class name
4. Use any available filters
5. Verify results

**Expected Result:**
- Search returns relevant results
- Filters work correctly
- Can clear search/filters

**Result:** 
**Notes:** 
**Feedback:**

---

## 6. Subject Management

### TEST 6.1: View Subjects
**Objective:** Verify admin can view all subjects with pagination

**Steps:**
1. Navigate to Admin > Subjects
2. Observe subject list
3. Test pagination controls
4. Test per-page selector (10, 20, 50, 100)
5. Verify subject count display

**Expected Result:**
- All subjects displayed in table
- Pagination works correctly
- Per-page selector functions
- Subject count accurate

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 6.2: Create Subject
**Objective:** Verify admin can create new subjects

**Steps:**
1. Navigate to Admin > Subjects
2. Click "Add Subject" button
3. Fill in form:
   - Name: Advanced Mathematics
   - Code: MATH-101
   - Description: Advanced level mathematics course
4. Click Save button

**Expected Result:**
- Success message appears
- Subject created successfully
- Subject appears in list
- All details correct

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 6.3: Edit Subject
**Objective:** Verify admin can edit subject information

**Steps:**
1. Find the subject created in Test 6.2
2. Click Edit button
3. Change description to "Advanced Mathematics Course"
4. Click Update button

**Expected Result:**
- Success message appears
- Subject updated successfully
- Changes reflected in list

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 6.4: Delete Subject
**Objective:** Verify subject deletion with dependency check

**Steps:**
1. Try to delete a subject with associated lessons
2. Observe error message
3. Try to delete a subject without lessons
4. Confirm deletion

**Expected Result:**
- Cannot delete subject with lessons
- Clear error message shown
- Can delete subject without lessons

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 6.5: Subject Search and Filter
**Objective:** Verify subject search and filtering

**Steps:**
1. Navigate to Admin > Subjects
2. Use search functionality
3. Search for specific subject name
4. Use any available filters
5. Verify results

**Expected Result:**
- Search returns relevant results
- Filters work correctly
- Can clear search/filters

**Result:** 
**Notes:** 
**Feedback:**

---

## 7. Room Management

### TEST 7.1: View Rooms
**Objective:** Verify admin can view all rooms with pagination

**Steps:**
1. Navigate to Admin > Room Management > Rooms
2. Observe room list
3. Test pagination controls
4. Test per-page selector (10, 20, 50, 100)
5. Verify room count display

**Expected Result:**
- All rooms displayed in table
- Pagination works correctly
- Per-page selector functions
- Room count accurate

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 7.2: Create Room
**Objective:** Verify admin can create new rooms

**Steps:**
1. Navigate to Admin > Room Management > Rooms
2. Click "Add Room" button
3. Fill in form:
   - Name: Computer Lab 1
   - Description: Computer laboratory with 30 PCs
   - Type: Laboratory
   - Equipment: Computers, Projector
   - Capacity: 30
4. Click Save button

**Expected Result:**
- Success message appears
- Room created successfully
- Room appears in list
- All details correct

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 7.3: Edit Room
**Objective:** Verify admin can edit room information

**Steps:**
1. Find the room created in Test 7.2
2. Click Edit button
3. Change capacity to 35
4. Click Update button

**Expected Result:**
- Success message appears
- Room updated successfully
- Changes reflected in list

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 7.4: Delete Room
**Objective:** Verify room deletion with dependency check

**Steps:**
1. Try to delete a room with scheduled lessons
2. Observe error message
3. Try to delete a room without lessons
4. Confirm deletion

**Expected Result:**
- Cannot delete room with lessons
- Clear error message shown
- Can delete room without lessons

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 7.5: Room Search and Filter
**Objective:** Verify room search and filtering

**Steps:**
1. Navigate to Admin > Room Management > Rooms
2. Use search functionality
3. Search for specific room name
4. Use type filter
5. Use equipment filter
6. Use capacity range filter
7. Verify results

**Expected Result:**
- Search returns relevant results
- All filters work correctly
- Can clear search/filters

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 7.6: Room QR Code Generation
**Objective:** Verify QR code generation for rooms

**Steps:**
1. Navigate to Admin > Room Management > Rooms
2. Find a room with scheduled lessons
3. Click QR Code button
4. Observe QR code display
5. Test QR code scanning (if possible)

**Expected Result:**
- QR code generates successfully
- QR code displays room timetable
- QR code is scannable

**Result:** 
**Notes:** 
**Feedback:**

---

## 8. Lesson Management

### TEST 8.1: View Lessons
**Objective:** Verify admin can view all lessons with pagination

**Steps:**
1. Navigate to Admin > Lessons
2. Observe lesson list
3. Test pagination controls
4. Test per-page selector (10, 20, 50, 100)
5. Verify lesson count display

**Expected Result:**
- All lessons displayed in table
- Pagination works correctly
- Per-page selector functions
- Lesson count accurate

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 8.2: Create Weekday Lesson (Senior High)
**Objective:** Verify admin can create weekday lessons for Senior High

**Steps:**
1. Navigate to Admin > Lessons
2. Click "Add Lesson" button
3. Fill in form:
   - Class: Select Senior High class
   - Subject: Select appropriate subject
   - Teacher: Select qualified teacher
   - Room: Select available room
   - Weekday: Monday
   - Start Time: 8:00 AM
   - End Time: 9:00 AM
4. Click Save button

**Expected Result:**
- Success message appears
- Lesson created successfully
- Lesson appears in list
- All details correct
- No conflicts detected

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 8.3: Create Weekend Lesson (Diploma)
**Objective:** Verify admin can create weekend lessons for Diploma programs

**Steps:**
1. Navigate to Admin > Lessons
2. Click "Add Lesson" button
3. Fill in form:
   - Class: Select Diploma class
   - Subject: Select appropriate subject
   - Teacher: Select qualified teacher
   - Room: Select available room
   - Weekday: Saturday
   - Start Time: 8:00 AM
   - End Time: 10:00 AM
4. Click Save button

**Expected Result:**
- Success message appears
- Lesson created successfully
- Lesson appears in list
- All details correct
- No validation errors

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 8.4: Attempt Weekend Lesson (Senior High)
**Objective:** Verify Senior High cannot have weekend lessons

**Steps:**
1. Navigate to Admin > Lessons
2. Click "Add Lesson" button
3. Fill in form:
   - Class: Select Senior High class
   - Subject: Select appropriate subject
   - Teacher: Select qualified teacher
   - Room: Select available room
   - Weekday: Saturday
   - Start Time: 8:00 AM
   - End Time: 9:00 AM
4. Click Save button

**Expected Result:**
- Validation error appears
- Error message: Weekend classes (Saturday/Sunday) are only available for Diploma Programs
- Lesson is NOT created
- Form remains open for correction

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 8.5: Edit Lesson
**Objective:** Verify admin can edit lesson information

**Steps:**
1. Find a lesson created in previous tests
2. Click Edit button
3. Change teacher to different teacher
4. Click Update button

**Expected Result:**
- Success message appears
- Lesson updated successfully
- Changes reflected in list
- No conflicts with new teacher

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 8.6: Delete Lesson
**Objective:** Verify admin can delete lessons with confirmation

**Steps:**
1. Find a lesson created in previous tests
2. Click Delete button
3. Confirm deletion in modal
4. Observe result

**Expected Result:**
- Confirmation modal appears
- Lesson deleted after confirmation
- Success message shown
- Lesson removed from list

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 8.7: Lesson Search and Filter
**Objective:** Verify lesson search and filtering (if available)

**Steps:**
1. Navigate to Admin > Lessons
2. Use search functionality (if available)
3. Search for specific lesson details
4. Use any available filters
5. Verify results

**Expected Result:**
- Search returns relevant results (if feature exists)
- Filters work correctly (if feature exists)
- Can clear search/filters

**Result:** 
**Notes:** 
**Feedback:**

---

## 9. Timetable Management

### TEST 9.1: Room Timetable View
**Objective:** Verify room timetable displays correctly

**Steps:**
1. Navigate to Admin > Room Management > Room Timetables
2. Select a room from dropdown
3. Observe timetable display
4. Check all day tabs (Mon-Sun)
5. Verify lesson details

**Expected Result:**
- Timetable loads correctly
- All days displayed
- Lessons shown accurately
- Empty slots clearly marked

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 9.2: Master Timetable View
**Objective:** Verify master timetable displays all lessons

**Steps:**
1. Navigate to Admin > Room Management > Master Timetable
2. Observe master timetable
3. Check all day tabs (Mon-Sun)
4. Verify lesson distribution
5. Check room utilization

**Expected Result:**
- Master timetable loads correctly
- All days displayed
- All lessons visible
- Room utilization accurate

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 9.3: Enable Edit Mode (Room Timetable)
**Objective:** Verify edit mode can be enabled

**Steps:**
1. Navigate to Admin > Room Management > Room Timetables
2. Select any room
3. Click "Enable Edit Mode" toggle
4. Observe changes

**Expected Result:**
- Edit mode indicator appears
- Empty slots show + button
- Lesson boxes show edit/delete buttons
- Visual indication of edit mode active

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 9.4: Create Lesson via Inline Editing
**Objective:** Verify lesson creation through inline modal

**Steps:**
1. Enable edit mode (from Test 9.3)
2. Navigate to Monday tab
3. Click on empty time slot (e.g., 8:00 AM)
4. Modal opens with form
5. Fill in:
   - Class: Select appropriate class
   - Subject: Select subject
   - Teacher: Select teacher
6. Observe weekday is locked to Monday
7. Click Save Lesson button

**Expected Result:**
- Modal opens with weekday pre-filled and locked
- Form accepts all inputs
- Success message appears
- Modal closes
- New lesson appears in clicked slot
- Timetable refreshes automatically

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 9.5: Edit Lesson via Inline Editing
**Objective:** Verify lesson can be edited inline

**Steps:**
1. Enable edit mode
2. Click on existing lesson box
3. Click Edit button in lesson box
4. Modal opens with lesson details
5. Change teacher to different teacher
6. Click Update Lesson button

**Expected Result:**
- Modal opens with current lesson data
- All fields are editable except weekday
- Success message appears
- Modal closes
- Lesson updates with new teacher
- Timetable refreshes

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 9.6: Delete Lesson via Inline Editing
**Objective:** Verify lesson can be deleted inline

**Steps:**
1. Enable edit mode
2. Click on existing lesson box
3. Click Delete button in lesson box
4. Confirmation dialog appears
5. Confirm deletion

**Expected Result:**
- Confirmation dialog shows lesson details
- Warning: This action cannot be undone
- After confirmation, success message appears
- Lesson removed from timetable
- Slot becomes empty
- Timetable refreshes

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 9.7: Weekend Validation in Inline Editing
**Objective:** Verify weekend validation works in inline editing

**Steps:**
1. Enable edit mode
2. Navigate to Saturday tab
3. Click on empty time slot
4. Modal opens
5. Fill in:
   - Class: STEM 11-A (Senior High)
   - Subject: Select subject
   - Teacher: Select teacher
6. Observe weekday is locked to Saturday
7. Click Save Lesson button

**Expected Result:**
- Modal stays open
- Red validation error box appears
- Error message: Weekend classes (Saturday/Sunday) are only available for Diploma Programs
- Lesson is NOT created
- User can change class or cancel

**Result:** 
**Notes:** 
**Feedback:**

---

## 10. Conflict Detection & Validation

### TEST 10.1: Teacher Conflict Detection
**Objective:** Verify system detects teacher double-booking

**Steps:**
1. Create a lesson:
   - Teacher: John Doe
   - Day: Monday
   - Time: 8:00 AM - 9:00 AM
2. Attempt to create another lesson:
   - Same Teacher: John Doe
   - Same Day: Monday
   - Overlapping Time: 8:30 AM - 9:30 AM
3. Click Save button

**Expected Result:**
- Conflict warning appears
- Message: Teacher John Doe is already scheduled during this time
- Shows conflicting lesson details
- Lesson is NOT created
- User can modify or cancel

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 10.2: Room Conflict Detection
**Objective:** Verify system detects room double-booking

**Steps:**
1. Create a lesson in Room 301:
   - Day: Tuesday
   - Time: 10:00 AM - 11:00 AM
2. Attempt to create another lesson:
   - Same Room: Room 301
   - Same Day: Tuesday
   - Overlapping Time: 10:30 AM - 11:30 AM
3. Click Save button

**Expected Result:**
- Conflict warning appears
- Message: Room 301 is already occupied during this time
- Shows conflicting lesson details
- Lesson is NOT created

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 10.3: Class Conflict Detection
**Objective:** Verify system detects class double-booking

**Steps:**
1. Create a lesson for STEM 11-A:
   - Day: Wednesday
   - Time: 1:00 PM - 2:00 PM
2. Attempt to create another lesson:
   - Same Class: STEM 11-A
   - Same Day: Wednesday
   - Overlapping Time: 1:30 PM - 2:30 PM
3. Click Save button

**Expected Result:**
- Conflict warning appears
- Message: Class STEM 11-A is already scheduled during this time
- Shows conflicting lesson details
- Lesson is NOT created

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 10.4: No Conflict - Adjacent Times
**Objective:** Verify system allows adjacent non-overlapping lessons

**Steps:**
1. Create a lesson:
   - Teacher: John Doe
   - Day: Thursday
   - Time: 9:00 AM - 10:00 AM
2. Create another lesson:
   - Same Teacher: John Doe
   - Same Day: Thursday
   - Adjacent Time: 10:00 AM - 11:00 AM
3. Click Save button

**Expected Result:**
- No conflict detected
- Success message: Lesson created successfully
- Both lessons appear in schedule
- Times are back-to-back with no overlap

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 10.5: Time Validation (End Before Start)
**Objective:** Verify end time must be after start time

**Steps:**
1. Navigate to Admin > Lessons > Add Lesson
2. Fill in form:
   - Start Time: 10:00 AM
   - End Time: 9:00 AM
3. Click Save button

**Expected Result:**
- Validation error appears
- Error message: End time must be after start time
- Lesson is NOT created
- User can correct times

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 10.6: Required Field Validation
**Objective:** Verify required fields are enforced

**Steps:**
1. Navigate to Admin > Lessons > Add Lesson
2. Leave Class field empty
3. Fill other fields
4. Click Save button

**Expected Result:**
- Validation error appears
- Error message: Please select a class
- Field is highlighted in red
- Form does not submit
- User can correct and resubmit

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 10.7: Email Validation
**Objective:** Verify email validation works

**Steps:**
1. Navigate to Admin > Users > Add User
2. Enter invalid email: johndoe@invalid
3. Fill other fields
4. Click Save button

**Expected Result:**
- Validation error appears
- Error message: Please enter a valid email address
- User is NOT created
- User can correct email

**Result:** 
**Notes:** 
**Feedback:**

---

## 11. Validation Error System Testing

### TEST 11.1: Orange Conflict Banner Display
**Objective:** Verify orange conflict banner appears for scheduling conflicts

**Steps:**
1. Navigate to Admin > Lessons
2. Create a lesson:
   - Teacher: John Doe
   - Day: Monday
   - Time: 8:00 AM - 9:00 AM
3. Click Save (lesson created successfully)
4. Click Add Lesson again
5. Create conflicting lesson:
   - Different Class and Subject
   - Same Teacher: John Doe
   - Same Day: Monday
   - Overlapping Time: 8:30 AM - 9:30 AM
6. Click Save button

**Expected Result:**
- 🟠 **Orange Conflict Banner** appears at top with:
  - Background: Orange gradient (#fff4e6 to #ffe0b2)
  - Left border: 4px solid orange (#ff9800)
  - Clock icon (⏰) - 24px size
  - Title: "Scheduling Conflict Detected"
  - Text: "Conflict with Teacher John Doe at 08:00:00 - 09:00:00"
  - Dismissible with X button
- 🟡 **Yellow Validation Banner** appears below with error count
- 🔴 **Red Field Error** appears below the conflicting field

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 11.2: Validation Error Banner Persistence Fix
**Objective:** Verify validation error banner disappears when conflicts are resolved

**Steps:**
1. Create a room conflict (same room, overlapping time)
2. Observe orange conflict banner appears
3. Change the conflicting field (room or time) to resolve conflict
4. Observe banner behavior

**Expected Result:**
- Orange conflict banner disappears with smooth animation
- Submit button becomes enabled
- User can successfully save the lesson
- No console errors

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 11.3: Duplicate Error Message Prevention
**Objective:** Verify different error styling prevents duplicate messages

**Steps:**
1. Navigate to Admin > Users > Add User
2. Fill form with invalid data:
   - Name: Test User
   - Email: invalid@gmail.com (not .edu.ph)
   - Password: 123 (too short)
   - Confirm Password: 456 (doesn't match)
3. Click Save button

**Expected Result:**
- 🟡 **Top Banner:** Yellow warning with "Please review the form below and correct 3 errors highlighted in red"
- 🔴 **Field Errors:** Each invalid field shows red border and specific error message
- **No Duplication:** Top banner shows summary only, field errors show details
- **Visual Distinction:** Yellow (warning) vs Red (danger) colors

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 11.4: Error Clearing After Correction
**Objective:** Verify errors clear when fields are corrected

**Steps:**
1. Create user with invalid email: test@gmail.com
2. Observe validation error appears
3. Change email to: test@school.edu.ph
4. Click Save button again

**Expected Result:**
- Email field error disappears
- Red border removed from email field
- Top banner disappears (all errors fixed)
- User created successfully
- Success message appears

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 11.5: Weekend Error Persistence Fix
**Objective:** Verify weekend validation error clears when switching to Diploma class

**Steps:**
1. Navigate to Admin > Lessons > Add Lesson
2. Select Senior High class
3. Select Saturday as weekday
4. Fill other fields and click Save
5. Observe error appears
6. Change class to Diploma class
7. Observe error behavior

**Expected Result:**
- Error appears for Senior High + Saturday
- Error disappears immediately when switching to Diploma class
- Help text updates to show weekend availability
- Lesson can be saved successfully

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 11.6: Inline Editing Error Banner Persistence
**Objective:** Verify error banner clears in inline editing when conflicts resolved

**Steps:**
1. Navigate to Admin > Room Management > Room Timetables
2. Enable edit mode
3. Click on occupied time slot
4. Fill form and click Save (conflict occurs)
5. Change conflicting field (room/time)
6. Observe banner behavior

**Expected Result:**
- Validation error banner appears for conflicts
- Banner disappears when conflict is resolved
- Submit button enabled after fixing errors
- Smooth user experience

**Result:** 
**Notes:** 
**Feedback:**

---

## 12. Email Validation Testing

### TEST 12.1: Valid .edu.ph Email Creation
**Objective:** Verify new users can be created with valid .edu.ph emails

**Steps:**
1. Navigate to Admin > Users
2. Click "Add User" button
3. Fill in form:
   - Name: John Doe
   - Email: john.doe@school.edu.ph
   - Password: password123
   - Confirm Password: password123
   - Role: Teacher
4. Click Save button

**Expected Result:**
- Success message: "User created successfully"
- User appears in user list
- Email saved as john.doe@school.edu.ph
- User can login with new credentials

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 12.2: Invalid Email Format Rejection
**Objective:** Verify invalid email formats are rejected

**Steps:**
1. Navigate to Admin > Users > Add User
2. Try each invalid email format:
   - test@gmail.com (not .edu.ph)
   - user@school.edu (missing .ph)
   - user@school.ph (missing .edu)
   - @school.edu.ph (missing username)
3. Fill other fields and click Save

**Expected Result:**
- Validation error appears for each invalid format
- Error message: "Email must be a valid institutional email address ending with .edu.ph"
- User is NOT created
- Form remains open for correction

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 12.3: Existing User Email Preservation
**Objective:** Verify existing users can keep their current emails

**Steps:**
1. Navigate to Admin > Users
2. Find admin user (admin@admin.com)
3. Click Edit button
4. Change name to "System Administrator"
5. Do NOT change email (leave as admin@admin.com)
6. Click Update button

**Expected Result:**
- Success message: "User updated successfully"
- Name updated to "System Administrator"
- Email remains as admin@admin.com
- No validation error for email
- User can still login with admin@admin.com

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 12.4: Email Change Validation
**Objective:** Verify email changes must use .edu.ph format

**Steps:**
1. Navigate to Admin > Users
2. Find any existing user
3. Click Edit button
4. Change email to: newemail@gmail.com
5. Click Update button

**Expected Result:**
- Validation error appears
- Error message: "New email must be a valid institutional email address ending with .edu.ph"
- Email is NOT updated
- User keeps original email

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 12.5: Valid Email Change
**Objective:** Verify users can change to valid .edu.ph emails

**Steps:**
1. Navigate to Admin > Users
2. Find any existing user
3. Click Edit button
4. Change email to: newemail@school.edu.ph
5. Click Update button

**Expected Result:**
- Success message: "User updated successfully"
- Email updated to newemail@school.edu.ph
- User can login with new email

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 12.6: Case Insensitive Email Validation
**Objective:** Verify email validation is case insensitive

**Steps:**
1. Navigate to Admin > Users > Add User
2. Fill form with:
   - Name: Test User
   - Email: User@SCHOOL.EDU.PH (mixed case)
   - Password: password123
   - Confirm Password: password123
   - Role: Teacher
3. Click Save button

**Expected Result:**
- Email accepted (case insensitive)
- User created successfully
- Email stored correctly

**Result:** 
**Notes:** 
**Feedback:**

---

## 13. Pagination & Data Display

### TEST 13.1: Users Pagination
**Objective:** Verify users page pagination works correctly

**Steps:**
1. Navigate to Admin > Users
2. Test pagination controls (Previous/Next)
3. Test per-page selector (10, 20, 50, 100)
4. Verify results info display
5. Test page navigation

**Expected Result:**
- Pagination controls work correctly
- Per-page selector functions
- Results info accurate
- Page navigation smooth

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 13.2: Subjects Pagination
**Objective:** Verify subjects page pagination works correctly

**Steps:**
1. Navigate to Admin > Subjects
2. Test pagination controls
3. Test per-page selector
4. Verify results info display
5. Test page navigation

**Expected Result:**
- Pagination controls work correctly
- Per-page selector functions
- Results info accurate
- Page navigation smooth

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 13.3: Rooms Pagination
**Objective:** Verify rooms page pagination works correctly

**Steps:**
1. Navigate to Admin > Room Management > Rooms
2. Test pagination controls
3. Test per-page selector
4. Verify results info display
5. Test page navigation

**Expected Result:**
- Pagination controls work correctly
- Per-page selector functions
- Results info accurate
- Page navigation smooth

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 13.4: Lessons Pagination
**Objective:** Verify lessons page pagination works correctly

**Steps:**
1. Navigate to Admin > Lessons
2. Test pagination controls
3. Test per-page selector
4. Verify results info display
5. Test page navigation

**Expected Result:**
- Pagination controls work correctly
- Per-page selector functions
- Results info accurate
- Page navigation smooth

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 13.5: Data Table Features
**Objective:** Verify DataTable features work correctly

**Steps:**
1. Navigate to any paginated page (Users, Subjects, Rooms, Lessons)
2. Test row selection
3. Test bulk actions (if available)
4. Test column sorting
5. Test search functionality

**Expected Result:**
- Row selection works
- Bulk actions function correctly
- Column sorting works
- Search functionality works

**Result:** 
**Notes:** 
**Feedback:**

---

## 14. Export & Print Features

### TEST 14.1: Export Timetable to CSV
**Objective:** Verify timetable can be exported to CSV

**Steps:**
1. Navigate to Admin > Room Management > Master Timetable
2. Select Tuesday
3. Click Export button
4. Select CSV format
5. Download file

**Expected Result:**
- CSV file downloads successfully
- File contains Tuesday schedule
- Data is comma-separated
- All lesson details included
- File can be opened in Excel

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 14.2: Print Class Timetable
**Objective:** Verify class timetable can be printed

**Steps:**
1. Navigate to Admin > Classes
2. Select any class
3. Click View Timetable
4. Click Print button
5. Review print preview

**Expected Result:**
- Print dialog opens
- Print preview shows formatted timetable
- All lessons visible in preview
- Page layout is appropriate
- Headers and footers included

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 14.3: Export All Timetables
**Objective:** Verify all timetables can be exported

**Steps:**
1. Navigate to Admin > Room Management > Master Timetable
2. Click Export All button
3. Select format (CSV)
4. Download file

**Expected Result:**
- Export all function works
- File contains all timetable data
- Data is properly formatted

**Result:** 
**Notes:** 
**Feedback:**

---

## 15. Public Features

### TEST 15.1: Public Room Timetable Access
**Objective:** Verify public can access room timetables

**Steps:**
1. Open new browser window (not logged in)
2. Navigate to `/public/room/{room-identifier}`
3. Observe timetable display
4. Check all day tabs
5. Verify lesson details

**Expected Result:**
- Public timetable loads without login
- All days displayed
- Lessons shown accurately
- No admin controls visible

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 15.2: Public Room Timetable Mobile View
**Objective:** Verify public timetable works on mobile

**Steps:**
1. Open public room timetable on mobile device
2. Test navigation between days
3. Check lesson details display
4. Test responsiveness

**Expected Result:**
- Timetable displays correctly on mobile
- Navigation works smoothly
- Lesson details readable
- Responsive design works

**Result:** 
**Notes:** 
**Feedback:**

---

## 16. UI/UX Testing

### TEST 16.1: Button Functionality
**Objective:** Verify all buttons work correctly

**Steps:**
1. Test Save buttons on all forms
2. Test Cancel buttons on all forms
3. Test Delete buttons with confirmation
4. Test Edit buttons
5. Test View buttons

**Expected Result:**
- All buttons function correctly
- Confirmations work properly
- Forms submit/cancel appropriately
- Navigation works smoothly

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 16.2: Form Validation Display
**Objective:** Verify form validation messages are clear

**Steps:**
1. Trigger various validation errors
2. Trigger conflict errors
3. Read error messages
4. Check message clarity

**Expected Result:**
- Error messages are clear and specific
- Messages explain what went wrong
- Messages suggest how to fix
- Messages are in plain language
- No technical jargon

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 16.3: Success Message Display
**Objective:** Verify success messages are shown correctly

**Steps:**
1. Create various records (user, class, subject, room, lesson)
2. Edit various records
3. Delete various records
4. Observe success messages

**Expected Result:**
- Success messages appear consistently
- Messages are clear and informative
- Messages don't duplicate
- Messages disappear appropriately

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 16.4: Navigation Consistency
**Objective:** Verify navigation is consistent throughout

**Steps:**
1. Navigate through all main sections
2. Check breadcrumbs
3. Check menu highlighting
4. Check back buttons
5. Check page titles

**Expected Result:**
- Navigation is consistent
- Breadcrumbs accurate
- Menu highlighting correct
- Back buttons work
- Page titles appropriate

**Result:** 
**Notes:** 
**Feedback:**

---

## 17. Mobile Responsiveness

### TEST 17.1: Mobile Dashboard
**Objective:** Verify dashboards work on mobile devices

**Steps:**
1. Open admin dashboard on mobile
2. Test navigation menu
3. Test dashboard widgets
4. Test responsive layout

**Expected Result:**
- Dashboard adapts to mobile screen
- Navigation menu works
- Widgets display correctly
- Layout is responsive

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 17.2: Mobile Forms
**Objective:** Verify forms work on mobile devices

**Steps:**
1. Test user creation form on mobile
2. Test lesson creation form on mobile
3. Test room creation form on mobile
4. Test form inputs and buttons

**Expected Result:**
- Forms adapt to mobile screen
- Input fields are usable
- Buttons are clickable
- Form submission works

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 17.3: Mobile Tables
**Objective:** Verify data tables work on mobile

**Steps:**
1. Test users table on mobile
2. Test subjects table on mobile
3. Test rooms table on mobile
4. Test lessons table on mobile

**Expected Result:**
- Tables adapt to mobile screen
- Data is readable
- Pagination works
- Actions are accessible

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 17.4: Mobile Timetables
**Objective:** Verify timetables work on mobile

**Steps:**
1. Test room timetables on mobile
2. Test master timetable on mobile
3. Test teacher dashboard on mobile
4. Test public timetables on mobile

**Expected Result:**
- Timetables display correctly
- Navigation works smoothly
- Lesson details readable
- Responsive design works

**Result:** 
**Notes:** 
**Feedback:**

---

## 18. Performance Testing

### TEST 18.1: Page Load Speed
**Objective:** Verify pages load within acceptable time

**Steps:**
1. Navigate to Admin > Lessons
2. Note page load time
3. Navigate to Admin > Master Timetable
4. Note page load time
5. Navigate to Admin > Users
6. Note page load time

**Expected Result:**
- Pages load within 3 seconds
- No excessive delays
- Loading indicators shown if needed
- Page is responsive after load

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 18.2: Large Dataset Handling
**Objective:** Verify system handles large datasets

**Steps:**
1. Create multiple users (if possible)
2. Create multiple lessons
3. Test pagination with large datasets
4. Test search with large datasets
5. Observe performance

**Expected Result:**
- System handles large datasets
- Pagination works efficiently
- Search remains responsive
- No performance degradation

**Result:** 
**Notes:** 
**Feedback:**

---

### TEST 18.3: Concurrent User Simulation
**Objective:** Verify system handles multiple users

**Steps:**
1. Open multiple browser tabs
2. Login as different users
3. Perform simultaneous actions
4. Observe system behavior
5. Check for conflicts

**Expected Result:**
- System handles multiple users
- No data corruption
- No unexpected errors
- Performance remains stable

**Result:** 
**Notes:** 
**Feedback:**

---

## 📊 Testing Summary

### Overall System Status
- **Total Tests:** 100+
- **Passed:** ___
- **Failed:** ___
- **Skipped:** ___
- **Not Applicable:** ___

### Critical Issues Found
1. ________________________________
2. ________________________________
3. ________________________________

### Minor Issues Found
1. ________________________________
2. ________________________________
3. ________________________________

### Recommendations
1. ________________________________
2. ________________________________
3. ________________________________

### Final Assessment
- **System Ready for Production:** Yes / No
- **Major Issues:** ___ (Number)
- **Minor Issues:** ___ (Number)
- **Overall Rating:** ___ / 10

---

## 📝 Notes
- Test with seeded data: admin@admin.com / password, teacher@teacher.com / password
- Students features excluded as per requirement
- Focus on admin and teacher functionality only
- Test on modern browsers (Chrome, Firefox, Safari, Edge)
- Test on both desktop and mobile devices
- **Special Focus:** Test all implemented fixes (Email Validation, Error Banner System, Conflict Detection, Weekend Validation)

---

**End of Testing Guide**
