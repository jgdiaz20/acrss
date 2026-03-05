# TESTING GUIDE 3: HOURS TRACKING - INLINE EDITING MODAL

**Test Suite:** Hours Tracking & Credit System  
**Component:** Inline Editing Modal - Real-Time Hours Tracking UI  
**Priority:** CRITICAL (Display/Refresh) + MEDIUM (UI/UX)  
**Last Updated:** December 14, 2025

---

## 📋 PREREQUISITES

### Required Completed Tests
- ✅ **TESTING_GUIDE_1_CREDIT_SYSTEM.md** - Subjects created
- ✅ **TESTING_GUIDE_2_HOURS_TRACKING_LESSONS.md** - Lessons created

### Test Environment
- **Page:** Room Timetable (`/admin/room-management/rooms/{room}/timetable`)
- **Feature:** Inline editing modal with hours tracking toggle
- **Browser:** Chrome/Firefox with DevTools (F12) for console monitoring

### Test Data State
```
Computer Programming (DIT-1A): 5h/9h scheduled (4h remaining)
Database Systems (DIT-1B): 3h/6h scheduled (3h remaining)
Mathematics (DIT-1A): 1h/3h scheduled (2h remaining)
English (DIT-1B): 2h/2h scheduled (0h remaining) - FULLY SCHEDULED
Web Development (DIT-1A): 1.5h lecture, 3h lab scheduled
```

---

## 🧪 TEST SCENARIOS

### **SCENARIO 1: HOURS TRACKING TOGGLE DISPLAY**

#### **Test 1.1: Toggle Visibility - Subject with Available Hours**

**User Story:**  
*As an admin, I want to see the hours tracking toggle when creating a lesson so I can monitor remaining hours.*

**Test Steps:**
1. Navigate to Room Timetable for Computer Lab 1
2. Enable **Edit Mode**
3. Click on an empty time slot (e.g., Monday 08:00)
4. **Create Lesson Modal opens**
5. Select **Class:** `DIT-1A`
6. Select **Subject:** `Computer Programming (COMPROG)`
7. **Locate hours tracking toggle button:**
   - ID: `#modal-hours-tracking-toggle`
   - Text: "Show Hours Tracking"
   - Icon: Chevron down
   - Color: Blue (btn-outline-info)
8. Click the toggle button
9. **Verify hours tracking section expands**

**Expected Result:**
- ✅ Toggle button visible and styled correctly
- ✅ Button color: Blue (default state)
- ✅ Click expands section smoothly
- ✅ Chevron icon rotates 180° on expand
- ✅ Section shows hours data:
  - Lab Hours: 5h/9h scheduled
  - Remaining: 4h
  - Progress bar: 55.6%

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 1.2: Toggle Color Transition - Exceeded Hours (CRITICAL)**

**User Story:**  
*As an admin, I want the hours tracking toggle to turn red when hours are exceeded so I immediately know why I can't create a lesson.*

**Test Steps:**
1. Open create lesson modal
2. Select **Class:** `DIT-1B`
3. Select **Subject:** `English (ENG)` (0h remaining - fully scheduled)
4. **Observe toggle button immediately:**
   - Should turn RED without clicking
   - Should show pulsing animation
5. Open browser console (F12)
6. **Check console logs:**
   - "No remaining hours detected on load - applying error state to toggle"
7. Click toggle to expand
8. **Verify error message displayed:**
   - "No remaining lecture hours for this class. All 2h have been scheduled."

**Expected Result:**
- ✅ Toggle button turns RED immediately (no click needed)
- ✅ Color transition: Blue → Red (smooth 0.4s animation)
- ✅ Pulsing animation visible
- ✅ Console log confirms class application
- ✅ Error message displayed in expanded section
- ✅ Submit button disabled
- ⚠️ **CRITICAL:** Color change must be immediate and obvious

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - UI/UX feedback priority]`

---

#### **Test 1.3: Toggle State Persistence - Collapse/Expand**

**User Story:**  
*As an admin, I want the hours tracking section to remember its expanded/collapsed state during modal interaction.*

**Test Steps:**
1. Open create lesson modal
2. Select subject with available hours
3. Click toggle to expand hours tracking
4. Change subject to another with available hours
5. **Verify section remains expanded**
6. Click toggle to collapse
7. Change subject again
8. **Verify section remains collapsed**

**Expected Result:**
- ✅ Expanded state persists across subject changes
- ✅ Collapsed state persists across subject changes
- ✅ Smooth animation on toggle
- ✅ Chevron icon reflects current state

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 2: REAL-TIME HOURS CALCULATION (CRITICAL)**

#### **Test 2.1: Dynamic Hours Calculation - As User Types**

**User Story:**  
*As an admin, I want to see real-time hours calculation as I enter start and end times so I know if my lesson fits.*

**Test Steps:**
1. Open create lesson modal
2. Select **Class:** `DIT-1A`
3. Select **Subject:** `Computer Programming (COMPROG)` (4h remaining)
4. Expand hours tracking
5. Set **Start Time:** `08:00`
6. **Gradually change End Time and observe:**
   - `09:00` (1h) - Should show error (lab min 3h)
   - `10:00` (2h) - Should show error (lab min 3h)
   - `11:00` (3h) - Should show success (within limit)
   - `12:00` (4h) - Should show success (exactly at limit)
   - `13:00` (5h) - Should show error (exceeds 4h remaining)
7. **For each change, verify:**
   - Hours tracking updates immediately
   - Error/info message changes
   - Submit button enables/disables
   - Toggle button color changes (blue/red)

**Expected Result:**
- ✅ Calculation updates in real-time (no delay)
- ✅ Error messages accurate:
  - "Laboratory lessons must be at least 3 hours"
  - "This lesson (5.0h) exceeds remaining laboratory hours (4.0h)"
- ✅ Info message for valid duration:
  - "This lesson will use 4.0h (0.0h laboratory hours remaining after)"
- ✅ Toggle color: Blue (valid) / Red (invalid)
- ✅ Submit button: Enabled (valid) / Disabled (invalid)
- ⚠️ **CRITICAL:** Test hours tracking refresh accuracy

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test display refresh]`

---

#### **Test 2.2: Hours Calculation - Flexible Subject (Lecture vs Lab)**

**User Story:**  
*As an admin, I want to see separate hours tracking for lecture and lab when working with flexible subjects.*

**Test Steps:**
1. Open create lesson modal
2. Select **Class:** `DIT-1A`
3. Select **Subject:** `Web Development (WEBDEV)`
4. **Verify lesson type field is enabled**
5. Expand hours tracking
6. **Verify separate sections displayed:**
   - Lecture Hours: 1.5h/2h (0.5h remaining)
   - Lab Hours: 3h/3h (0h remaining)
7. Select **Lesson Type:** `Lecture`
8. Set duration: 1h
9. **Verify only lecture hours considered:**
   - Error: Exceeds 0.5h lecture remaining
   - Lab hours NOT affected
10. Change **Lesson Type:** `Laboratory`
11. **Verify error:**
    - No lab hours remaining

**Expected Result:**
- ✅ Separate lecture/lab sections displayed
- ✅ Lesson type selection affects correct hours pool
- ✅ Cannot use lab hours for lecture (and vice versa)
- ✅ Error messages specify lecture/lab correctly
- ✅ Progress bars show separately

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 2.3: Hours Calculation - Edit Mode Exclusion (CRITICAL)**

**User Story:**  
*As an admin, when editing a lesson, the current lesson's hours should be excluded from the calculation so I can adjust duration.*

**Test Steps:**
1. Navigate to Room Timetable
2. Enable Edit Mode
3. Click **Edit** on existing lesson:
   - Computer Programming (DIT-1A)
   - Current duration: 3h
   - Other lessons: 2h scheduled
   - Total: 9h, Remaining should show 7h (excluding current 3h)
4. **Edit Modal opens**
5. Expand hours tracking
6. **Verify hours display:**
   - Scheduled: 2h (current lesson excluded)
   - Remaining: 7h (9h - 2h)
7. Open browser console
8. **Check for exclusion log:**
   - Should see `exclude_lesson_id` in API request
9. Change duration from 3h to 4h
10. **Verify calculation:**
    - New total: 6h (2h + 4h)
    - Remaining: 3h
    - Should be valid

**Expected Result:**
- ✅ Current lesson excluded from scheduled hours
- ✅ Remaining hours calculated correctly
- ✅ Can increase duration within available hours
- ✅ API request includes `exclude_lesson_id` parameter
- ✅ Console logs confirm exclusion
- ⚠️ **CRITICAL:** Edit mode exclusion must work correctly

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test hours tracking accuracy]`

---

### **SCENARIO 3: PROGRESS BAR DISPLAY**

#### **Test 3.1: Progress Bar - Visual Accuracy**

**User Story:**  
*As an admin, I want to see a visual progress bar that accurately represents hours usage.*

**Test Steps:**
1. Open create lesson modal
2. Test with different subjects and observe progress bars:
   - Computer Programming (DIT-1A): 5h/9h = 55.6%
   - Mathematics (DIT-1A): 1h/3h = 33.3%
   - English (DIT-1B): 2h/2h = 100%
3. For each, verify:
   - Progress bar width matches percentage
   - Color coding (if any)
   - Percentage text displayed
   - Tooltip/label accuracy

**Expected Result:**
- ✅ Progress bar width visually matches percentage
- ✅ 55.6% shows ~56% filled bar
- ✅ 100% shows completely filled bar
- ✅ Percentage text accurate
- ✅ Smooth animation on load

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - UI/UX feedback]`

---

#### **Test 3.2: Progress Bar - Color Coding**

**User Story:**  
*As an admin, I want the progress bar to use color coding to indicate usage levels.*

**Test Steps:**
1. Test progress bar colors at different levels:
   - 0-50%: Should be green/success
   - 51-80%: Should be yellow/warning
   - 81-99%: Should be orange/warning
   - 100%: Should be red/danger
2. Verify colors match Bootstrap classes or custom styling

**Expected Result:**
- ✅ Color changes based on percentage
- ✅ Visual distinction clear
- ✅ 100% clearly indicates full usage

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 4: ERROR MESSAGE DISPLAY (CRITICAL)**

#### **Test 4.1: Error Message - Exceeding Hours**

**User Story:**  
*As an admin, I want clear error messages when I try to exceed remaining hours.*

**Test Steps:**
1. Open create lesson modal
2. Select Computer Programming (DIT-1A) - 4h remaining
3. Set duration: 5h (exceeds by 1h)
4. **Verify error message:**
   - Element: `#modal-hours-error-message`
   - Visibility: Shown
   - Text: "This lesson (5.0h) exceeds remaining laboratory hours (4.0h). Scheduled: 5.0h / Total: 9h"
5. **Verify styling:**
   - Alert class: danger/error
   - Icon: Warning/error icon
   - Color: Red background

**Expected Result:**
- ✅ Error message displayed prominently
- ✅ Message includes:
  - Current lesson duration
  - Remaining hours
  - Scheduled hours
  - Total hours
- ✅ Red/danger styling
- ✅ Submit button disabled
- ⚠️ **CRITICAL:** Message must be accurate and clear

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 4.2: Info Message - Valid Duration**

**User Story:**  
*As an admin, I want to see an info message showing how many hours will remain after creating the lesson.*

**Test Steps:**
1. Open create lesson modal
2. Select Computer Programming (DIT-1A) - 4h remaining
3. Set duration: 3h (valid)
4. **Verify info message:**
   - Element: `#modal-hours-info-message`
   - Visibility: Shown
   - Text: "This lesson will use 3.0h (1.0h laboratory hours remaining after)"
5. **Verify styling:**
   - Alert class: info
   - Color: Blue background

**Expected Result:**
- ✅ Info message displayed
- ✅ Shows duration and remaining hours
- ✅ Blue/info styling
- ✅ Submit button enabled

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 4.3: Error Message - Zero Remaining Hours**

**User Story:**  
*As an admin, I want a specific error message when a subject-class has no remaining hours.*

**Test Steps:**
1. Open create lesson modal
2. Select English (DIT-1B) - 0h remaining
3. **Verify error message immediately:**
   - "No remaining lecture hours for this class. All 2h have been scheduled."
4. **Verify:**
   - Error shows before entering times
   - Submit button disabled immediately
   - Toggle button red

**Expected Result:**
- ✅ Specific message for zero hours
- ✅ Displays immediately on subject selection
- ✅ No need to enter times to see error
- ✅ Clear and actionable

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 5: SUBJECT CHANGE & DATA REFRESH (CRITICAL)**

#### **Test 5.1: Subject Change - Hours Data Updates**

**User Story:**  
*As an admin, when I change the subject in the modal, hours tracking should update immediately with the new subject's data.*

**Test Steps:**
1. Open create lesson modal
2. Select **Subject:** `Computer Programming` (9h total)
3. Expand hours tracking
4. **Note displayed hours:** 5h/9h
5. Change **Subject:** to `Database Systems` (6h total)
6. **Verify hours tracking updates:**
   - Should show: 3h/6h
   - Progress bar updates
   - Remaining hours updates
7. Open browser console
8. **Check for API call:**
   - Should see request to `/admin/lessons/hours-tracking`
   - Parameters: subject_id, class_id
9. Change subject multiple times rapidly
10. **Verify no race conditions or stale data**

**Expected Result:**
- ✅ Hours data updates immediately on subject change
- ✅ API call made for each subject change
- ✅ No stale data displayed
- ✅ No race conditions with rapid changes
- ✅ Loading indicator (if any) during fetch
- ⚠️ **CRITICAL:** Test hours tracking refresh accuracy

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test display refresh]`

---

#### **Test 5.2: Class Change - Hours Data Updates**

**User Story:**  
*As an admin, when I change the class, hours tracking should update to show that class's usage.*

**Test Steps:**
1. Open create lesson modal
2. Select **Class:** `DIT-1A`
3. Select **Subject:** `Computer Programming`
4. **Note hours:** 5h/9h (DIT-1A usage)
5. Change **Class:** to `DIT-1B`
6. **Verify hours tracking updates:**
   - Should show: 0h/9h (DIT-1B has no lessons)
7. Change back to **Class:** `DIT-1A`
8. **Verify returns to:** 5h/9h

**Expected Result:**
- ✅ Hours data class-specific
- ✅ Updates immediately on class change
- ✅ Correct data for each class
- ✅ No cross-class interference

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 5.3: API Error Handling**

**User Story:**  
*As an admin, I want to see a clear error message if hours tracking data fails to load.*

**Test Steps:**
1. Open browser DevTools → Network tab
2. Open create lesson modal
3. Select class and subject
4. **Simulate API failure:**
   - Block request to `/admin/lessons/hours-tracking`
   - OR disconnect internet briefly
5. **Verify error handling:**
   - Error message displayed
   - Graceful degradation
   - Can still submit (or blocked with message)

**Expected Result:**
- ✅ Error message displayed to user
- ✅ Console error logged
- ✅ Modal doesn't crash
- ✅ Clear indication of what failed

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 6: CONSOLE LOGGING & DEBUGGING**

#### **Test 6.1: Console Logs - Hours Tracking Events**

**User Story:**  
*As a developer/tester, I want to see console logs that help debug hours tracking issues.*

**Test Steps:**
1. Open browser console (F12)
2. Open create lesson modal
3. Perform various actions and check for logs:
   - Subject selection
   - Hours tracking fetch
   - Toggle button class changes
   - Validation errors
4. **Expected logs:**
   - "Fetching hours tracking data..."
   - "Hours tracking data received: {...}"
   - "Toggle button: Added hours-exceeded class"
   - "Toggle button: Removed hours-exceeded class"
   - "Submit enabled/disabled: [reason]"

**Expected Result:**
- ✅ Console logs present and informative
- ✅ Logs show data flow
- ✅ Logs show validation decisions
- ✅ No errors in console (unless expected)
- ✅ Logs help diagnose issues

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 7: RESPONSIVE BEHAVIOR - SMALL SCREENS**

#### **Test 7.1: Hours Tracking on Mobile/Small Screens**

**User Story:**  
*As an admin on a mobile device, I want hours tracking to display correctly on small screens.*

**Test Steps:**
1. Open DevTools → Toggle device toolbar (Ctrl+Shift+M)
2. Set viewport to mobile (e.g., iPhone 12, 390x844)
3. Navigate to Room Timetable
4. Enable Edit Mode
5. Open create lesson modal
6. **Verify hours tracking:**
   - Toggle button visible and clickable
   - Section expands correctly
   - Progress bars display properly
   - Text readable (no overflow)
   - Buttons accessible

**Expected Result:**
- ✅ Modal fits on small screen
- ✅ Hours tracking section readable
- ✅ Toggle button accessible
- ✅ No horizontal scroll
- ✅ Touch-friendly button sizes

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - UI/UX feedback]`

---

### **SCENARIO 8: INTEGRATION WITH LESSON SUBMISSION**

#### **Test 8.1: Submit Blocked When Hours Exceeded**

**User Story:**  
*As an admin, I should be prevented from submitting a lesson that exceeds remaining hours.*

**Test Steps:**
1. Open create lesson modal
2. Configure lesson that exceeds hours
3. **Verify submit button:**
   - Disabled state
   - Cursor: not-allowed
   - Tooltip/title explaining why
4. Try clicking submit button
5. **Verify:**
   - Nothing happens
   - Error message remains visible
   - Modal stays open

**Expected Result:**
- ✅ Submit button disabled
- ✅ Visual indication (grayed out)
- ✅ Cannot submit
- ✅ Error message explains why

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 8.2: Submit Allowed When Valid**

**User Story:**  
*As an admin, I should be able to submit a lesson when it's within remaining hours.*

**Test Steps:**
1. Open create lesson modal
2. Configure valid lesson (within hours)
3. **Verify submit button:**
   - Enabled state
   - Normal cursor
   - Blue/primary color
4. Click submit
5. **Verify:**
   - Lesson created successfully
   - Modal closes
   - Timetable refreshes
   - New lesson appears

**Expected Result:**
- ✅ Submit button enabled
- ✅ Lesson saves successfully
- ✅ Hours tracking updated after save
- ✅ No errors

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

## 📊 TEST SUMMARY

**Total Tests:** 20  
**Passed:** `[Count]`  
**Failed:** `[Count]`  
**Blocked:** `[Count]`

### Critical Issues Found
`[List any critical issues with hours tracking display/refresh]`

### UI/UX Issues
`[List any issues with color transitions, progress bars, or visual feedback]`

### Console Errors
`[List any JavaScript errors found in console]`

---

## 🔄 NEXT STEPS

After completing this test suite:
1. ✅ Proceed to **TESTING_GUIDE_4_INTEGRATION.md**
2. Test edge cases and integration scenarios
3. Test subject mode switching with existing lessons

---

**Tester Name:** `[Your name]`  
**Test Date:** `[Date]`  
**Test Duration:** `[Duration]`  
**Environment:** `[Development/Staging/Production]`  
**Browser:** `[Chrome/Firefox/Safari + Version]`
