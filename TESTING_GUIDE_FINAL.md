 Comprehensive Testing Guide
 Laravel School Timetable System

Version: 2.0
Last Updated: October 17, 2025

---

 HOW TO USE THIS GUIDE

1. Follow each test section in order
2. Mark results as PASS, FAIL, SKIP, or N/A
3. Record actual results in Result field
4. Note any issues in Notes field
5. Provide feedback in Feedback field

---

 SECTION 1: AUTHENTICATION AND ACCESS CONTROL

 TEST 1.1: Admin Login

Objective: Verify admin can login and access admin dashboard

Steps:
1. Open browser and navigate to /login
2. Enter admin credentials (Email: admin@admin.com, Password: password)
3. Click Login button
4. Observe redirect location

Expected Result:
- User redirected to /admin dashboard
- Dashboard displays admin menu with all management options
- User name appears in top right corner

Result: Passed

Notes: None
 
Feedback:Redirected to correct path, admin logged in successfully.

---

 TEST 1.2: Teacher Login

Objective: Verify teacher can login and access teacher dashboard

Steps:
1. Logout from admin account
2. Navigate to /login
3. Enter teacher credentials
4. Click Login button
5. Observe redirect location

Expected Result:
- User redirected to /teacher dashboard
- Dashboard shows teacher-specific menu
- Teacher can view their schedule

Result:Passed 

Notes:Will add teacher timetable on the menu panel, this is the area where the teacher sees his/her class schedule within the week (not on the dashboard).Timetable UI and layout would be the same as the layout in room-timetables. Purely for viewing only, not editable.

Feedback: UI needs improvement, teacher dashboard needs to be improved for better user experience, accurate data, responsive and mobile friendly interface. 

---

 TEST 1.3: Unauthorized Access Prevention

Objective: Verify non-admin users cannot access admin pages

Steps:
1. Login as teacher
2. Manually navigate to /admin/lessons
3. Observe response

Expected Result:
- Access denied with 403 Forbidden error
- Or redirect to appropriate dashboard
- Error message displayed

Result:Passed

Notes:None

Feedback:None

---

 SECTION 2: ACADEMIC PROGRAM MANAGEMENT

 TEST 2.1: Create Academic Program

Objective: Verify admin can create new academic program

Steps:
1. Login as admin
2. Navigate to Admin > Academic Programs
3. Click Add Academic Program button
4. Fill in form:
   - Name: Bachelor of Science in Information Technology
   - Short Name: BSIT
   - Type: College
   - Description: 4-year IT program
5. Click Save button

Expected Result:
- Success message: Academic Program created successfully
- New program appears in the list
- Program details are correct

Result: Passed

Notes:None

Feedback:None

---

 TEST 2.2: Edit Academic Program

Objective: Verify admin can edit existing program

Steps:
1. Navigate to Admin > Academic Programs
2. Find the BSIT program created in Test 2.1
3. Click Edit button
4. Change Name to: BS Information Technology
5. Click Update button

Expected Result:
- Success message: Academic Program updated successfully
- Program name is updated in the list
- Other details remain unchanged

Result:Passed

Notes:None

Feedback:None

---

 TEST 2.3: Delete Academic Program with Classes

Objective: Verify system prevents deletion of program with associated classes

Steps:
1. Navigate to Admin > Academic Programs
2. Find a program that has classes assigned
3. Click Delete button
4. Confirm deletion in popup

Expected Result:
- Error message: Cannot delete program with existing classes
- Program is NOT deleted
- Program still appears in list

Result:Passed

Notes:None 	

Feedback:None

---

 SECTION 3: CLASS MANAGEMENT

 TEST 3.1: Create Class for Senior High School

Objective: Verify admin can create Senior High class

Steps:
1. Navigate to Admin > Classes
2. Click Add Class button
3. Fill in form:
   - Name: STEM 11-A
   - Program: Senior High School
   - Grade Level: Grade 11
   - School Year: 2024-2025
   - Max Students: 40
4. Click Save button

Expected Result:
- Success message: Class created successfully
- New class appears in list
- Class shows correct program and grade level

Result:Passed

Notes:Can create classes/sections successfully but class creation is done within class programs. User flow should be: User clicks senior high school , navigates to which program user wants to add the sections, fill in appropriate fields and saves the section. 

Feedback: Manage program still needs work on proper naming conventions.

---

 TEST 3.2: Create Class for Diploma Program

Objective: Verify admin can create Diploma class

Steps:
1. Navigate to Admin > Classes
2. Click Add Class button
3. Fill in form:
   - Name: Business Administration - 1st Year
   - Program: Select a Diploma program
   - Grade Level: Select appropriate level
   - School Year: 2024-2025
   - Max Students: 35
4. Click Save button

Expected Result:
- Success message: Class created successfully
- New class appears in list
- Class is marked as Diploma program type

Result:Passed

Notes:Can create classes or sections successfully but class creation is done within courses example: Diploma Program in Marketing Technology. User flow should be: User clicks diploma under school classes dropdown menu , navigates to which program user wants to add the sections, fill in appropriate fields and saves the section. 

Feedback: School Year input fields is not yet present, since by semester feature is not yet implemented in the system (included in future plans).

---

 TEST 3.3: Filter Classes by Program

Objective: Verify class filtering works correctly

Steps:
1. Navigate to Admin > Classes
2. Use program filter dropdown
3. Select Senior High School
4. Click Filter or observe auto-filter

Expected Result:
- Only Senior High School classes displayed
- Other program classes hidden
- Filter selection remains active

Result:Passed

Notes:None

Feedback:Filters work flawlessly.

---

 TEST 3.4: Search Classes by Name

Objective: Verify class search functionality

Steps:
1. Navigate to Admin > Classes
2. Enter STEM in search box
3. Press Enter or click Search

Expected Result:
- Only classes with STEM in name displayed
- Search term remains in search box
- Clear search option available

Result: Passed	

Notes:None 

Feedback:Search filter works flawlessly.

---

 SECTION 4: LESSON CREATION - WEEKDAY

 TEST 4.1: Create Monday Lesson for Senior High

Objective: Verify admin can create weekday lesson for Senior High class

Steps:
1. Navigate to Admin > Lessons
2. Click Add Lesson button
3. Fill in form:
   - Class: STEM 11-A (Senior High)
   - Subject: Mathematics
   - Teacher: Select qualified teacher
   - Room: Room 301
   - Weekday: Monday
   - Start Time: 8:00 AM
   - End Time: 9:00 AM
4. Click Save button

Expected Result:
- Success message: Lesson created successfully
- Lesson appears in timetable
- All details are correct
- No conflicts detected

Result:Passed

Notes:Proper success/error messages should be enforced with accurate error details messages for conflict detection otherwise a success message if no issues were found with accurate reference (already showing). 

Feedback: Satisfied with the result, however proper success/error message should be shown. These messages were showing before, identify the issue and improve accordingly.

---

 TEST 4.2: Attempt Saturday Lesson for Senior High

Objective: Verify Senior High cannot have Saturday lessons

Steps:
1. Navigate to Admin > Lessons
2. Click Add Lesson button
3. Fill in form:
   - Class: STEM 11-A (Senior High)
   - Subject: Mathematics
   - Teacher: Select qualified teacher
   - Room: Room 301
   - Weekday: Saturday
   - Start Time: 8:00 AM
   - End Time: 9:00 AM
4. Click Save button

Expected Result:
- Validation error appears
- Error message: Weekend classes (Saturday/Sunday) are only available for Diploma Programs. This class belongs to Senior High School program.
- Lesson is NOT created
- Form remains open for correction

Result: Passed	

Notes: Upon creating a lesson, success/error messages should be improved.

Feedback: Test worked flawlessly.

---

 TEST 4.3: Attempt Sunday Lesson for Senior High

Objective: Verify Senior High cannot have Sunday lessons

Steps:
1. Navigate to Admin > Lessons
2. Click Add Lesson button
3. Fill in form:
   - Class: STEM 11-A (Senior High)
   - Weekday: Sunday
   - Fill other required fields
4. Click Save button

Expected Result:
- Validation error appears
- Error message mentions weekend restriction
- Lesson is NOT created

Result:Passed

Notes: Error messages should be gone  when selecting a diploma program class and saturday is already selected as the weekday, Instead the error is still present at the top and at the bottom of the weekday input field.

Feedback:Lesson not created but the error message is persistent even if the class is changed to a valid class while saturday or sunday is selected in the weekday field.

---

 SECTION 5: LESSON CREATION - WEEKEND (DIPLOMA)

 TEST 5.1: Create Saturday Lesson for Diploma Program

Objective: Verify Diploma program can have Saturday lessons

Steps:
1. Navigate to Admin > Lessons
2. Click Add Lesson button
3. Fill in form:
   - Class: Business Administration - 1st Year (Diploma)
   - Subject: Select appropriate subject
   - Teacher: Select qualified teacher
   - Room: Select available room
   - Weekday: Saturday
   - Start Time: 8:00 AM
   - End Time: 10:00 AM
4. Click Save button

Expected Result:
- Success message: Lesson created successfully
- Saturday lesson appears in timetable
- All details are correct
- No validation errors

Result: Passed

Notes:None 

Feedback: Lesson creation in this test was successful.

---

 TEST 5.2: Create Sunday Lesson for Diploma Program

Objective: Verify Diploma program can have Sunday lessons

Steps:
1. Navigate to Admin > Lessons
2. Click Add Lesson button
3. Fill in form:
   - Class: Business Administration - 1st Year (Diploma)
   - Weekday: Sunday
   - Fill other required fields
4. Click Save button

Expected Result:
- Success message: Lesson created successfully
- Sunday lesson appears in timetable
- All details are correct

Result: Passed

Notes:None

Feedback:Diploma class sunday lesson created successfully.

---

 TEST 5.3: Verify Weekend Tabs in Master Timetable

Objective: Verify Saturday and Sunday tabs are visible

Steps:
1. Navigate to Admin > Room Management > Master Timetable
2. Observe available day tabs
3. Click on Saturday tab
4. Click on Sunday tab

Expected Result:
- All 7 day tabs visible: Mon, Tue, Wed, Thu, Fri, Sat, Sun
- Saturday tab shows Saturday lessons
- Sunday tab shows Sunday lessons
- Weekend tabs styled same as weekday tabs

Result: Passed

Notes:None

Feedback:Results are as expected.

---

 SECTION 6: CONFLICT DETECTION

 TEST 6.1: Teacher Conflict Detection

Objective: Verify system detects teacher double-booking

Steps:
1. Create a lesson:
   - Teacher: John Doe
   - Day: Monday
   - Time: 8:00 AM - 9:00 AM
2. Attempt to create another lesson:
   - Same Teacher: John Doe
   - Same Day: Monday
   - Overlapping Time: 8:30 AM - 9:30 AM
3. Click Save button

Expected Result:
- Conflict warning appears
- Message: Teacher John Doe is already scheduled during this time
- Shows conflicting lesson details
- Lesson is NOT created
- User can modify or cancel

Result: Passed

Notes: Conflict detection messages for teacher works, it should have conflict type (teacher, time, or if room is occupied) in creating a lesson in /admin/lessons. 

Feedback: Teacher double booking conflict test was successful however the conflict detection error message should have accurate conflict types.If these are already present then no need to modify anything.


---

 TEST 6.2: Room Conflict Detection

Objective: Verify system detects room double-booking

Steps:
1. Create a lesson in Room 301:
   - Day: Tuesday
   - Time: 10:00 AM - 11:00 AM
2. Attempt to create another lesson:
   - Same Room: Room 301
   - Same Day: Tuesday
   - Overlapping Time: 10:30 AM - 11:30 AM
3. Click Save button

Expected Result:
- Conflict warning appears
- Message: Room 301 is already occupied during this time
- Shows conflicting lesson details
- Lesson is NOT created

Result:Passed

Notes:Room double-booking conflict detection working. Proper error message was showing.

Feedback:Test passed with proper conflict type.

---

 TEST 6.3: Class Conflict Detection

Objective: Verify system detects class double-booking

Steps:
1. Create a lesson for STEM 11-A:
   - Day: Wednesday
   - Time: 1:00 PM - 2:00 PM
2. Attempt to create another lesson:
   - Same Class: STEM 11-A
   - Same Day: Wednesday
   - Overlapping Time: 1:30 PM - 2:30 PM
3. Click Save button

Expected Result:
- Conflict warning appears
- Message: Class STEM 11-A is already scheduled during this time
- Shows conflicting lesson details
- Lesson is NOT created

Result: Passed

Notes: Class conflict detection test passed with proper error message.

Feedback:None

---

 TEST 6.4: No Conflict - Adjacent Times

Objective: Verify system allows adjacent non-overlapping lessons

Steps:
1. Create a lesson:
   - Teacher: John Doe
   - Day: Thursday
   - Time: 9:00 AM - 10:00 AM
2. Create another lesson:
   - Same Teacher: John Doe
   - Same Day: Thursday
   - Adjacent Time: 10:00 AM - 11:00 AM
3. Click Save button

Expected Result:
- No conflict detected
- Success message: Lesson created successfully
- Both lessons appear in schedule
- Times are back-to-back with no overlap

Result: Passed

Notes Allows: adjacent non-overlapping lessons no conflict detected.

Feedback: Lesson created successfully with no conflict or errors.

---

 SECTION 7: INLINE EDITING (ROOM TIMETABLE)

 TEST 7.1: Enable Edit Mode

Objective: Verify edit mode can be enabled

Steps:
1. Navigate to Admin > Room Management > Room Timetable
2. Select any room
3. Click Enable Edit Mode toggle or button
4. Observe changes

Expected Result:
- Edit mode indicator appears
- Empty slots show + button or become clickable
- Lesson boxes show edit/delete buttons
- Visual indication of edit mode active

Result: Passed

Notes: None

Feedback: Test successful with proper button functions and inline-editing features.

---

 TEST 7.2: Create Lesson via Inline Editing (Weekday)

Objective: Verify lesson creation through inline modal

Steps:
1. Enable edit mode
2. Navigate to Monday tab
3. Click on empty time slot (e.g., 8:00 AM)
4. Modal opens with form
5. Fill in:
   - Class: Select Senior High class
   - Subject: Select subject
   - Teacher: Select teacher
6. Observe weekday is locked to Monday
7. Click Save Lesson button

Expected Result:
- Modal opens with weekday pre-filled and locked
- Form accepts all inputs
- Success message appears
- Modal closes
- New lesson appears in clicked slot
- Timetable refreshes automatically

Result:Passed

Notes: Success message when creating a lesson successfully should be simplified as “Lesson created successfully” 

Feedback: Test showing all expected results and is a success.

---

 TEST 7.3: Attempt Weekend Lesson via Inline Editing

Objective: Verify weekend validation in inline editing

Steps:
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

Expected Result:
- Modal stays open
- Red validation error box appears
- Error message: Weekend classes (Saturday/Sunday) are only available for Diploma Programs. This class belongs to Senior High School program.
- Lesson is NOT created
- User can change class or cancel

Result: Passed

Notes: None

Feedback: Test is successful with proper error message and detection.

---

 TEST 7.4: Create Weekend Lesson via Inline Editing (Diploma)

Objective: Verify Diploma can create weekend lesson inline

Steps:
1. Enable edit mode
2. Navigate to Saturday tab
3. Click on empty time slot
4. Modal opens
5. Fill in:
   - Class: Select Diploma class
   - Subject: Select subject
   - Teacher: Select teacher
6. Click Save Lesson button

Expected Result:
- No validation errors
- Success message appears
- Modal closes
- Lesson appears in Saturday slot
- Timetable refreshes

Result:Passed

Notes: None

Feedback:Successful lesson creation for diploma classes on weekend  lesson creation.

---

 TEST 7.5: Edit Lesson via Inline Editing

Objective: Verify lesson can be edited inline

Steps:
1. Enable edit mode
2. Click on existing lesson box
3. Click Edit button in lesson box
4. Modal opens with lesson details
5. Change teacher to different teacher
6. Click Update Lesson button

Expected Result:
- Modal opens with current lesson data
- All fields are editable except weekday
- Success message appears
- Modal closes
- Lesson updates with new teacher
- Timetable refreshes

Result: Passed

Notes: None

Feedback:Test worked with expected results.

---

 TEST 7.6: Delete Lesson via Inline Editing

Objective: Verify lesson can be deleted inline

Steps:
1. Enable edit mode
2. Click on existing lesson box
3. Click Delete button in lesson box
4. Confirmation dialog appears
5. Confirm deletion

Expected Result:
- Confirmation dialog shows lesson details
- Warning: This action cannot be undone
- After confirmation, success message appears
- Lesson removed from timetable
- Slot becomes empty
- Timetable refreshes

Result:Passed

Notes:None 

Feedback:Inline-editing lesson deletion is working properly with expected results.

---

 TEST 7.7: Error Clearing on Modal Close

Objective: Verify errors clear when modal is closed

Steps:
1. Enable edit mode
2. Click Saturday slot
3. Select Senior High class (triggers error)
4. Click Save - error appears
5. Click Cancel to close modal
6. Click same slot again to reopen modal

Expected Result:
- First attempt shows validation error
- After closing and reopening modal
- No error messages visible
- Form is clean and ready for new input
- Previous error does not persist

Result: Partially passed

Notes: The validation error is working but it only shows when the create lesson button is clicked, not when an invalid class is selected (This is okay but if it can show an error upon selecting an invalid class then implement it). 

Feedback:Test successful with expected results.Also I encountered an instance where the rooms field was not showing any room when attempting to create a schedule. Maybe this is a css/bootstrap issue since sometimes it needs a refresh (identify the cause and resolve it).

---

 SECTION 8: DATA VALIDATION

 TEST 8.1: Required Field Validation

Objective: Verify required fields are enforced

Steps:
1. Navigate to Admin > Lessons > Add Lesson
2. Leave Class field empty
3. Fill other fields
4. Click Save button

Expected Result:
- Validation error appears
- Error message: Please select a class
- Field is highlighted in red
- Form does not submit
- User can correct and resubmit

Result:Passed 

Notes: None

Feedback:Test successful with expected results.

---

 TEST 8.2: Time Validation (End Before Start)

Objective: Verify end time must be after start time

Steps:
1. Navigate to Admin > Lessons > Add Lesson
2. Fill in form:
   - Start Time: 10:00 AM
   - End Time: 9:00 AM
3. Click Save button

Expected Result:
- Validation error appears
- Error message: End time must be after start time
- Lesson is NOT created
- User can correct times

Result:Passed

Notes: Time validation works correctly.

Feedback: Upon trying to create a class with a start time at 8pm and an end time at 9pm. The timepicker dropdown feature does not allow me to create a 9pm end time at first instance, it only allows 9pm end time when end time field is clicked again. Resolve this issue, also fix other possible issues like when clicking the time fields, when timepicker dropdown shows, it shows the current time, it should show blank, since timepicker should have strict time validation which is from only 7am to 9pm time and with 30 minute intervals which is working. Timepicker just needs improvement in all areas it is used.

---

 TEST 8.3: Invalid Email Format

Objective: Verify email validation works

Steps:
1. Navigate to Admin > Teachers > Add Teacher
2. Enter invalid email: johndoe@invalid
3. Fill other fields
4. Click Save button

Expected Result:
- Validation error appears
- Error message: Please enter a valid email address
- Teacher is NOT created
- User can correct email

Result: Failed	

Notes: Email validation not working properly, email validation should be the email ending in (edu.ph)

Feedback:Email validation and user management needs improvement especially when creating a user, a user cannot be a teacher and an admin at the same time. And user creation form needs to be updated since it currently requires to re-enter the default password (which is password) this should not happen since password changing is available at edit user. 

---

 SECTION 9: FILTERING AND SEARCH

 TEST 9.1: Filter Lessons by Class

Objective: Verify lesson filtering by class works

Steps:
1. Navigate to Admin > Lessons
2. Use class filter dropdown
3. Select STEM 11-A
4. Click Filter or observe auto-filter

Expected Result:
- Only lessons for STEM 11-A displayed
- Other class lessons hidden
- Filter selection remains active
- Clear filter option available

Result:Failed

Notes:System does not currently have a filter search present in admin/lessons.

Feedback: There are no search feature in lessons, this should be added immediately and ensure proper functionality.

---

 TEST 9.2: Filter Lessons by Teacher

Objective: Verify lesson filtering by teacher works

Steps:
1. Navigate to Admin > Lessons
2. Use teacher filter dropdown
3. Select John Doe
4. Click Filter or observe auto-filter

Expected Result:
- Only lessons taught by John Doe displayed
- Other teacher lessons hidden
- Filter selection remains active

Result: Failed

Notes:Filtering is not present in admin/lessons, the filtering feature is present at admin/subjects. Should similar filtering be added at class schedules as well?.

Feedback:Failed to test since lessons has no filter features.

---

 TEST 9.3: Search Lessons by Subject

Objective: Verify lesson search by subject works

Steps:
1. Navigate to Admin > Lessons
2. Enter Mathematics in search box
3. Press Enter or click Search

Expected Result:
- Only Mathematics lessons displayed
- Search term remains in search box
- Clear search option available

Result:N/A

Notes:This test is not executable since admin/lessons does not have this filter feature.

Feedback: Filter feature is to be added, it will be similar to the filtering in admin/subjects

---

 SECTION 10: EXPORT AND PRINT

 TEST 10.1: Export Timetable to PDF

Objective: Verify timetable can be exported to PDF

Steps:
1. Navigate to Admin > Room Management > Master Timetable
2. Select Monday
3. Click Export button
4. Select PDF format
5. Download file

Expected Result:
- PDF file downloads successfully
- File contains Monday schedule
- File is properly formatted
- All lesson details included
- File can be opened and viewed

Result: Failed

Notes:Export feature does not currently support pdf format.

Feedback: Will reconsider in adding this.

---

 TEST 10.2: Export Timetable to CSV

Objective: Verify timetable can be exported to CSV

Steps:
1. Navigate to Admin > Room Management > Master Timetable
2. Select Tuesday
3. Click Export button
4. Select CSV format
5. Download file

Expected Result:
- CSV file downloads successfully
- File contains Tuesday schedule
- Data is comma-separated
- All lesson details included
- File can be opened in Excel

Result: Passed

Notes: CSV format exporting working in master timetable. Lesson details are accurate.

Feedback: Test passed flawlessly.

---

 TEST 10.3: Print Class Timetable

Objective: Verify class timetable can be printed

Steps:
1. Navigate to Admin > Classes
2. Select any class
3. Click View Timetable
4. Click Print button
5. Review print preview

Expected Result:
- Print dialog opens
- Print preview shows formatted timetable
- All lessons visible in preview
- Page layout is appropriate
- Headers and footers included

Result:Passed

Notes: Print function working, lessons are visible.

Feedback:The breadcrumbs should not be included during this print preview, consider removing the statistics below the timetable. The print output should just be the timetable and its current room information.

---

 SECTION 11: BUTTON FUNCTIONALITY

 TEST 11.1: Save Button

Objective: Verify Save button works correctly

Steps:
1. Navigate to any Create form
2. Fill in all required fields
3. Click Save button
4. Observe result

Expected Result:
- Form submits successfully
- Success message appears
- User redirected to list page
- New record appears in list

Result: Passed 

Notes: Some success messages are not displayed properly. Success and error messages needs improvement, since some cases multiple error/success messages are displayed.

Feedback: Needs improvement.

---

 TEST 11.2: Cancel Button

Objective: Verify Cancel button works correctly

Steps:
1. Navigate to any Create form
2. Fill in some fields
3. Click Cancel button
4. Observe result

Expected Result:
- Form does not submit
- No data is saved
- User redirected to list page
- No success or error message

Result:Passed

Notes: Cancel buttons work as expected.

Feedback: Test passed flawlessly.

---

 TEST 11.3: Delete Button with Confirmation

Objective: Verify Delete button shows confirmation

Steps:
1. Navigate to any list page
2. Click Delete button on any record
3. Observe confirmation dialog
4. Click Cancel in dialog

Expected Result:
- Confirmation dialog appears
- Dialog shows record details
- Warning message displayed
- After clicking Cancel, record is NOT deleted
- Dialog closes

Result: Failed

Notes: Delete dialog appears but a modal should appear instead (refer to /admin/lessons and /admin/academic-programs) when deleting a lesson and a program, delete is working but it should be a modal throughout the system,

Feedback: This delete confirmation modal feature should be consistent throughout the system. This can be applied in areas where delete buttons are present such as: /admin/subjects , /admin/room-management/rooms, and in /admin/users and in /admin/users?role=3/.

---

 TEST 11.4: Edit Button

Objective: Verify Edit button opens edit form

Steps:
1. Navigate to any list page
2. Click Edit button on any record
3. Observe result

Expected Result:
- Edit form opens
- Form is pre-filled with current data
- All fields are editable
- Save and Cancel buttons available

Result:Passed

Notes:None

Feedback:Test passed with expected results.

---

 TEST 11.5: View Button

Objective: Verify View button shows details

Steps:
1. Navigate to any list page
2. Click View button on any record
3. Observe result

Expected Result:
- Details page opens
- All record information displayed
- Data is read-only
- Back or Close button available

Result:Passed

Notes:None

Feedback:View button is functional, test passed with expected results.

---

 SECTION 12: DATA ACCURACY

 TEST 12.1: Verify Lesson Data Accuracy

Objective: Verify lesson data is stored and displayed correctly

Steps:
1. Create a lesson with specific details
2. View the lesson in timetable
3. Edit the lesson
4. Compare all fields

Expected Result:
- All data matches what was entered
- Class name is correct
- Teacher name is correct
- Subject name is correct
- Room name is correct
- Day and time are correct

Result:Passed

Notes:None

Feedback: Test successful with expected results.

---

 TEST 12.2: Verify Teacher Schedule Accuracy

Objective: Verify teacher schedule shows correct lessons

Steps:
1. Note all lessons assigned to a specific teacher
2. View that teacher's schedule
3. Compare with noted lessons

Expected Result:
- All assigned lessons appear in schedule
- No extra lessons appear
- All details are accurate
- Schedule matches master timetable

Result: Passed

Notes: None 

Feedback:Test passed with expected results.

---

 TEST 12.3: Verify Room Occupancy Accuracy

Objective: Verify room timetable shows correct occupancy

Steps:
1. Note all lessons in a specific room
2. View that room's timetable
3. Compare with noted lessons

Expected Result:
- All lessons in that room appear
- No lessons from other rooms appear
- Empty slots are clearly marked
- All details are accurate

Result:Passed	

Notes:None

Feedback:Test successful with expected results.

---

 SECTION 13: USER EXPERIENCE

 TEST 13.1: Page Load Speed

Objective: Verify pages load within acceptable time

Steps:
1. Navigate to Admin > Lessons
2. Note page load time
3. Navigate to Admin > Master Timetable
4. Note page load time

Expected Result:
- Pages load within 3 seconds
- No excessive delays
- Loading indicators shown if needed
- Page is responsive after load

Result:Passed

Notes:None 

Feedback:Needs improvement especially with larger datasets in production.

---

 TEST 13.2: Mobile Responsiveness

Objective: Verify system works on mobile devices

Steps:
1. Open system on mobile device or use browser dev tools
2. Navigate through various pages
3. Test creating a lesson
4. Test viewing timetables

Expected Result:
- Pages adapt to mobile screen size
- All buttons are clickable
- Forms are usable
- Timetables are readable
- No horizontal scrolling required

Result: Passed

Notes: Needs improvement.

Feedback: Room timetimetable are not showing correctly in mobile device. The table should be similar to the timetables at public/room in terms of layout. Although public room timetables needs improvement, it shows the timetables properly.






---

 TEST 13.3: Error Message Clarity

Objective: Verify error messages are clear and helpful

Steps:
1. Trigger various validation errors
2. Trigger conflict errors
3. Read error messages

Expected Result:
- Error messages are clear and specific
- Messages explain what went wrong
- Messages suggest how to fix
- Messages are in plain language
- No technical jargon

Result:Failed

Notes:Validation errors needs improvements.

Feedback:Some error validations appears duplicate when  triggered. Some works fine, but some error validations shows at the top and within the form container. Some shows in the invalid field and appears redundant overall.

---

 SECTION 14: EDGE CASES

 TEST 14.1: Create Lesson at Midnight

Objective: Verify system handles midnight time correctly

Steps:
1. Navigate to Admin > Lessons > Add Lesson
2. Set Start Time: 11:00 PM
3. Set End Time: 12:00 AM (next day)
4. Fill other fields
5. Click Save

Expected Result:
- System accepts or rejects appropriately
- If accepted, time is stored correctly
- If rejected, clear error message shown

Result:Skipped

Notes:Cannot execute steps since time validation does not allow this kind of schedules (lessons must be within 7am - 9pm only).

Feedback:This method is not needed since current time validations are working correctly.

---

 TEST 14.2: Create Lesson with Maximum Duration

Objective: Verify system handles long lessons

Steps:
1. Navigate to Admin > Lessons > Add Lesson
2. Set Start Time: 8:00 AM
3. Set End Time: 5:00 PM (9 hours)
4. Fill other fields
5. Click Save

Expected Result:
- System accepts or rejects based on rules
- If rejected, error message explains limit
- If accepted, lesson displays correctly

Result:Passed

Notes:Lesson displays correctly.

Feedback:Test successfully passed with expected results.

---

 TEST 14.3: Create Lesson with Special Characters in Name

Objective: Verify system handles special characters

Steps:
1. Create a class with name: Test & Demo (2024-2025)
2. Create a lesson for this class
3. View the lesson in timetable

Expected Result:
- Special characters are stored correctly
- Special characters display correctly
- No encoding issues
- No JavaScript errors

Result:Passed

Notes:None

Feedback:Test passed with expected results.


---

END OF TESTING GUIDE


