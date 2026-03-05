# TESTING GUIDE 4: INTEGRATION & EDGE CASES

**Test Suite:** Hours Tracking & Credit System  
**Component:** Integration Testing, Edge Cases, and System Behavior  
**Priority:** CRITICAL (Validation Edge Cases)  
**Last Updated:** December 14, 2025

---

## 📋 PREREQUISITES

### Required Completed Tests
- ✅ **TESTING_GUIDE_1_CREDIT_SYSTEM.md** - Subject management
- ✅ **TESTING_GUIDE_2_HOURS_TRACKING_LESSONS.md** - Lesson creation/editing
- ✅ **TESTING_GUIDE_3_INLINE_EDITING.md** - Inline modal UI

### Test Data State
All subjects and lessons from previous guides should be in place.

---

## 🧪 TEST SCENARIOS

### **SCENARIO 1: SUBJECT MODE SWITCHING WITH EXISTING LESSONS (CRITICAL)**

#### **Test 1.1: Lab to Lecture Mode Switch - Hours Recalculation**

**User Story:**  
*As an admin, when I switch a subject from Lab to Lecture mode, I need to understand the impact on existing lessons and hours tracking.*

**Prerequisites:**
- Create new subject: "Physics" (Lab, 3 credits, 9h)
- Create 2 lessons for Physics (DIT-1A):
  - Lesson 1: 4 hours
  - Lesson 2: 3 hours
  - Total: 7h scheduled, 2h remaining

**Test Steps:**
1. Navigate to `/admin/subjects`
2. Click **Edit** on "Physics"
3. **Verify warning alert:**
   - "⚠️ Warning: This subject has 2 existing lesson(s)"
4. Change **Scheduling Mode:** `Lab` → `Lecture`
5. Keep **Credits:** `3`
6. **Verify confirmation modal:**
   - Shows lesson count: 2
   - Warns about impacts
7. Click **Confirm**
8. Subject updated: Now 3 lecture hours (was 9 lab hours)
9. Navigate to `/admin/lessons`
10. Filter by Physics, DIT-1A
11. **Check hours tracking:**
    - Scheduled: 7h
    - Total: 3h
    - **EXCEEDED by 4h**
12. Try to create new lesson
13. **Verify blocked:**
    - Error: Hours exceeded
    - Cannot create until resolved

**Expected Result:**
- ✅ Warning and confirmation displayed
- ✅ Mode switch successful
- ✅ Hours recalculated: 9h → 3h
- ✅ System detects exceeded state
- ✅ New lessons blocked
- ⚠️ **CRITICAL:** Existing lessons remain but hours tracking shows exceeded
- ⚠️ **CRITICAL:** Credit calculation accurate after switch

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test credit calculation accuracy]`

---

#### **Test 1.2: Lecture to Lab Mode Switch - Hours Increase**

**User Story:**  
*As an admin, when I switch from Lecture to Lab mode, hours should increase and previously blocked lessons may become valid.*

**Prerequisites:**
- Create subject: "Chemistry" (Lecture, 2 credits, 2h)
- Create 1 lesson: 2h (fully scheduled)

**Test Steps:**
1. Edit "Chemistry"
2. Change **Scheduling Mode:** `Lecture` → `Lab`
3. Keep **Credits:** `2`
4. Confirm changes
5. **Verify hours recalculation:**
   - Was: 2h total (100% scheduled)
   - Now: 6h total (2h scheduled = 33%)
   - Remaining: 4h
6. Try to create new 3-hour lab lesson
7. **Verify allowed:**
   - Within remaining 4h
   - Lesson creates successfully

**Expected Result:**
- ✅ Hours increased: 2h → 6h
- ✅ Remaining hours available: 4h
- ✅ Can create new lessons
- ✅ Progress percentage updated

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 1.3: Flexible to Fixed Mode Switch - Unit Validation**

**User Story:**  
*As an admin, when I switch from Flexible to Lab/Lecture mode, the system should validate and adjust units correctly.*

**Prerequisites:**
- Subject: Web Development (Flexible, 2L+1Lab, 5h total)
- Has 1 lecture lesson (1.5h) and 1 lab lesson (3h)

**Test Steps:**
1. Edit "Web Development"
2. Try changing to **Lab Mode** with 2 credits
3. **Expected behavior:**
   - Lecture units set to 0
   - Lab units set to 2
   - Total hours: 6h (was 5h)
4. Confirm changes
5. **Check existing lessons:**
   - Lecture lesson (1.5h) now invalid type?
   - Lab lesson (3h) still valid
6. **Verify hours tracking:**
   - Only counts lab lesson: 3h/6h
   - Lecture lesson excluded or flagged?

**Expected Result:**
- ✅ Mode switch successful
- ✅ Units recalculated
- ⚠️ System behavior with mixed lessons documented
- ⚠️ Hours tracking handles type mismatch

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Document system behavior]`

---

### **SCENARIO 2: CREDIT LIMIT EDGE CASES (CRITICAL)**

#### **Test 2.1: Maximum Credits Boundary - Lab Mode**

**User Story:**  
*As an admin, I want to verify the system enforces the 3-credit maximum for lab subjects.*

**Test Steps:**
1. Create subject with **Credits:** `3` (Lab) - Should succeed
2. Edit subject, try changing to **Credits:** `4` - Should fail
3. Try creating with **Credits:** `10` - Should fail
4. **Verify validation:**
   - Client-side: HTML5 max="3"
   - Server-side: Error message

**Expected Result:**
- ✅ 3 credits: Accepted (9 lab hours)
- ❌ 4 credits: Rejected with error
- ❌ 10 credits: Rejected with error
- ✅ Both client and server validation working

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test validation edge cases]`

---

#### **Test 2.2: Flexible Mode - Total Credits Exceeding 3**

**User Story:**  
*As an admin, I should be prevented from creating a flexible subject where lecture + lab units exceed 3.*

**Test Steps:**
1. Create flexible subject
2. Try combinations:
   - 2L + 2Lab = 4 total - Should fail
   - 3L + 1Lab = 4 total - Should fail
   - 1L + 3Lab = 4 total - Should fail
   - 2L + 1Lab = 3 total - Should succeed
   - 1L + 2Lab = 3 total - Should succeed

**Expected Result:**
- ❌ Any combination > 3: Error "Total credits (lecture + lab) cannot exceed 3"
- ✅ Combinations = 3: Accepted
- ✅ Validation prevents submission

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 2.3: Zero Credits Edge Case**

**User Story:**  
*As an admin, I should be prevented from creating a subject with zero credits.*

**Test Steps:**
1. Try creating Lab subject with 0 credits
2. Try creating Lecture subject with 0 credits
3. Try creating Flexible subject with 0L + 0Lab
4. **Verify all rejected**

**Expected Result:**
- ❌ All zero-credit attempts rejected
- ❌ Error: "Credits must be between 1 and 3" (Lab/Lecture)
- ❌ Error: "At least one unit must be greater than 0" (Flexible)

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 3: DURATION VALIDATION EDGE CASES (CRITICAL)**

#### **Test 3.1: Lab Lesson - Exact Boundary Durations**

**User Story:**  
*As an admin, I want to verify lab lesson duration validation at exact boundaries.*

**Test Steps:**
1. Create lab subject (9h available)
2. Try creating lessons with exact boundaries:
   - 2.99 hours - Should fail (below 3h)
   - 3.0 hours - Should succeed (minimum)
   - 5.0 hours - Should succeed (maximum)
   - 5.01 hours - Should fail (above 5h)

**Expected Result:**
- ❌ 2.99h: "Laboratory lessons must be at least 3 hours"
- ✅ 3.0h: Accepted
- ✅ 5.0h: Accepted
- ❌ 5.01h: "Laboratory lessons cannot exceed 5 hours"

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test validation edge cases]`

---

#### **Test 3.2: Lecture Lesson - 30-Minute Intervals**

**User Story:**  
*As an admin, I want to verify lecture lessons accept 30-minute intervals.*

**Test Steps:**
1. Create lecture subject (3h available)
2. Try creating lessons:
   - 1.0h - Should succeed
   - 1.5h - Should succeed
   - 2.0h - Should succeed
   - 2.5h - Should succeed
   - 3.0h - Should succeed
   - 1.25h (1h 15min) - Verify behavior
   - 1.75h (1h 45min) - Verify behavior

**Expected Result:**
- ✅ All 30-minute intervals (1.0, 1.5, 2.0, 2.5, 3.0) accepted
- ⚠️ 15-minute intervals: Document system behavior
- ✅ Duration validation accurate

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 3.3: Negative Duration (Time Travel)**

**User Story:**  
*As an admin, I should be prevented from creating a lesson where end time is before start time.*

**Test Steps:**
1. Create lesson
2. Set **Start Time:** `10:00`
3. Set **End Time:** `09:00` (before start)
4. Try to save

**Expected Result:**
- ❌ Lesson not created
- ❌ Error: "End time must be after start time"
- ❌ Duration shows as negative or zero

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 4: CONCURRENT EDITING & RACE CONDITIONS**

#### **Test 4.1: Two Admins Creating Lessons Simultaneously**

**User Story:**  
*As an admin, when multiple admins create lessons simultaneously, hours tracking should remain accurate.*

**Prerequisites:**
- Subject: Computer Programming (DIT-1A) with 4h remaining
- Two browser windows/tabs open (simulate two admins)

**Test Steps:**
1. **Tab 1:** Open create lesson modal
2. **Tab 2:** Open create lesson modal
3. Both select same class/subject
4. **Both see:** 4h remaining
5. **Tab 1:** Create 3h lesson, save
6. **Tab 2:** Still shows 4h remaining (stale data)
7. **Tab 2:** Try to create 3h lesson (would exceed)
8. **Verify server-side validation:**
   - Should reject Tab 2's lesson
   - Error: Exceeds remaining hours

**Expected Result:**
- ✅ Tab 1 lesson created: 3h scheduled, 1h remaining
- ❌ Tab 2 lesson rejected by server
- ✅ Server-side validation prevents over-scheduling
- ⚠️ UI may show stale data but server validates

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test validation edge cases]`

---

#### **Test 4.2: Edit Lesson While Another Admin Deletes It**

**User Story:**  
*As an admin, if I try to edit a lesson that another admin just deleted, I should get a clear error.*

**Test Steps:**
1. **Tab 1:** Open edit modal for lesson ID 123
2. **Tab 2:** Delete lesson ID 123
3. **Tab 1:** Make changes and try to save
4. **Verify error handling:**
   - 404 error from server
   - Clear message to user
   - Modal closes or shows error

**Expected Result:**
- ❌ Update fails with 404
- ✅ Error message: "Lesson not found. It may have been deleted."
- ✅ Modal handles error gracefully

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 5: MASS OPERATIONS & BULK CHANGES**

#### **Test 5.1: Delete Multiple Lessons - Hours Recalculation**

**User Story:**  
*As an admin, when I delete multiple lessons at once, hours tracking should update correctly.*

**Prerequisites:**
- Computer Programming (DIT-1A): 3 lessons (3h, 4h, 2h = 9h total)

**Test Steps:**
1. Navigate to `/admin/lessons`
2. Filter by Computer Programming, DIT-1A
3. Select 2 lessons (3h and 4h)
4. Click **Mass Delete**
5. Confirm deletion
6. **Verify hours tracking:**
   - Scheduled: 2h (only remaining lesson)
   - Remaining: 7h
7. Try creating new 5h lesson
8. **Verify allowed**

**Expected Result:**
- ✅ 2 lessons deleted successfully
- ✅ Hours recalculated: 9h → 2h scheduled
- ✅ Remaining: 7h
- ✅ Can create new lessons within 7h

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test hours tracking refresh]`

---

#### **Test 5.2: Change Subject for Multiple Lessons**

**User Story:**  
*As an admin, if I could bulk-edit lessons to change their subject, hours should reallocate correctly.*

**Note:** If bulk edit doesn't exist, document as future feature.

**Test Steps:**
1. Check if bulk edit exists
2. If yes, test changing subject for multiple lessons
3. Verify hours tracking updates for both subjects

**Expected Result:**
- Document whether feature exists
- If exists, verify hours reallocation

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED/NOT_APPLICABLE]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 6: CROSS-CLASS HOURS TRACKING VERIFICATION**

#### **Test 6.1: Same Subject, Multiple Classes - Complete Isolation**

**User Story:**  
*As an admin, I want to verify that hours tracking is completely isolated per class.*

**Test Steps:**
1. Create lessons for Computer Programming:
   - DIT-1A: 9h (100%)
   - DIT-1B: 5h (55%)
   - DIT-2A: 0h (0%)
2. For each class, verify:
   - Hours tracking shows only that class's usage
   - Creating lesson in one class doesn't affect others
   - Deleting lesson in one class doesn't affect others
3. **Test cross-contamination:**
   - Delete all DIT-1A lessons
   - Verify DIT-1B and DIT-2A unchanged

**Expected Result:**
- ✅ Complete isolation between classes
- ✅ DIT-1A: 9h → 0h (after deletion)
- ✅ DIT-1B: 5h (unchanged)
- ✅ DIT-2A: 0h (unchanged)
- ✅ No cross-class interference

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test class-specific tracking]`

---

#### **Test 6.2: Subject Deletion with Existing Lessons**

**User Story:**  
*As an admin, I should be prevented from deleting a subject that has existing lessons.*

**Test Steps:**
1. Try to delete Computer Programming (has lessons)
2. **Verify prevention:**
   - Delete button disabled, OR
   - Confirmation modal warns about lessons, OR
   - Server rejects with error
3. Delete all lessons first
4. Try deleting subject again
5. **Verify allowed**

**Expected Result:**
- ❌ Cannot delete subject with lessons
- ✅ Warning/error message displayed
- ✅ After deleting lessons, subject deletion allowed

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 7: API RESPONSE VALIDATION**

#### **Test 7.1: Hours Tracking API - Response Structure**

**User Story:**  
*As a developer, I want to verify the hours tracking API returns correct data structure.*

**Test Steps:**
1. Open browser DevTools → Network tab
2. Create lesson and trigger hours tracking fetch
3. Find request to `/admin/lessons/hours-tracking`
4. **Verify request parameters:**
   - subject_id
   - class_id
   - exclude_lesson_id (in edit mode)
5. **Verify response structure:**
```json
{
  "success": true,
  "total_hours": 9,
  "scheduled_hours": 5,
  "remaining_hours": 4,
  "progress": 55.6,
  "lecture_hours": {
    "total": 0,
    "scheduled": 0,
    "remaining": 0
  },
  "lab_hours": {
    "total": 9,
    "scheduled": 5,
    "remaining": 4
  },
  "scheduling_mode": "lab"
}
```

**Expected Result:**
- ✅ All fields present
- ✅ Correct data types
- ✅ Calculations accurate
- ✅ No cache headers (no-cache, no-store)

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 7.2: Hours Tracking API - Error Responses**

**User Story:**  
*As a developer, I want to verify the API handles errors gracefully.*

**Test Steps:**
1. Test API with invalid parameters:
   - Missing subject_id
   - Missing class_id
   - Invalid subject_id (non-existent)
2. **Verify error responses:**
   - 400/422 for missing params
   - 404 for non-existent subject
   - Proper error messages

**Expected Result:**
- ✅ Appropriate HTTP status codes
- ✅ Error messages in response
- ✅ No server crashes

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 8: PERFORMANCE & CACHING**

#### **Test 8.1: Hours Tracking Calculation Performance**

**User Story:**  
*As an admin, hours tracking should calculate quickly even with many lessons.*

**Test Steps:**
1. Create subject with many lessons (20+ if possible)
2. Open create lesson modal
3. Select the subject
4. **Measure time:**
   - Time from subject selection to hours display
   - Should be < 1 second
5. Check browser console for performance logs
6. Check server logs for query time

**Expected Result:**
- ✅ Hours tracking loads in < 1 second
- ✅ No noticeable lag
- ✅ Efficient database queries

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 8.2: Cache Invalidation After Lesson Changes**

**User Story:**  
*As an admin, hours tracking should always show fresh data, not cached stale data.*

**Test Steps:**
1. View hours tracking: 5h/9h
2. Create new lesson: 3h
3. Immediately open create modal again
4. **Verify hours updated:** 8h/9h (not stale 5h/9h)
5. Edit a lesson's duration
6. **Verify hours reflect change**
7. Delete a lesson
8. **Verify hours reflect deletion**

**Expected Result:**
- ✅ No stale cached data
- ✅ Hours always current
- ✅ Cache invalidated after changes

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - Test hours tracking refresh]`

---

### **SCENARIO 9: BROWSER COMPATIBILITY**

#### **Test 9.1: Hours Tracking in Different Browsers**

**User Story:**  
*As an admin, hours tracking should work consistently across browsers.*

**Test Steps:**
1. Test in Chrome
2. Test in Firefox
3. Test in Edge
4. Test in Safari (if available)
5. For each browser, verify:
   - Toggle button works
   - Color transitions work
   - Progress bars display
   - API calls succeed
   - Console logs present

**Expected Result:**
- ✅ Consistent behavior across browsers
- ✅ No browser-specific bugs
- ✅ Visual consistency

**Actual Result:**  
`[Your feedback here - Test in each browser]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 10: ACCESSIBILITY**

#### **Test 10.1: Hours Tracking - Keyboard Navigation**

**User Story:**  
*As an admin using keyboard navigation, I should be able to access all hours tracking features.*

**Test Steps:**
1. Open create lesson modal
2. Use **Tab** key to navigate
3. **Verify can reach:**
   - Hours tracking toggle button
   - Can press Enter/Space to toggle
   - Can navigate within expanded section
4. Use **Shift+Tab** to navigate backwards
5. **Verify focus indicators visible**

**Expected Result:**
- ✅ All elements keyboard accessible
- ✅ Focus indicators visible
- ✅ Logical tab order

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 10.2: Screen Reader Compatibility**

**User Story:**  
*As an admin using a screen reader, I should be able to understand hours tracking information.*

**Test Steps:**
1. Enable screen reader (NVDA/JAWS/VoiceOver)
2. Navigate to hours tracking
3. **Verify announced:**
   - Toggle button label
   - Hours information
   - Error messages
   - Progress percentages
4. **Check ARIA attributes:**
   - aria-expanded on toggle
   - aria-label on progress bars
   - role attributes

**Expected Result:**
- ✅ All information announced
- ✅ Proper ARIA attributes
- ✅ Accessible to screen readers

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

## 📊 TEST SUMMARY

**Total Tests:** 25  
**Passed:** `[Count]`  
**Failed:** `[Count]`  
**Blocked:** `[Count]`

### Critical Issues Found
`[List any critical issues with validation, calculation, or edge cases]`

### Integration Issues
`[List any issues with subject mode switching or cross-component behavior]`

### Edge Cases Not Handled
`[List any edge cases that caused unexpected behavior]`

---

## 🎯 OVERALL TESTING SUMMARY

### All Test Suites Combined
- **Guide 1 (Credit System):** `[Pass/Fail count]`
- **Guide 2 (Hours Tracking - Lessons):** `[Pass/Fail count]`
- **Guide 3 (Inline Editing):** `[Pass/Fail count]`
- **Guide 4 (Integration):** `[Pass/Fail count]`

**Total Tests Across All Guides:** 88  
**Total Passed:** `[Count]`  
**Total Failed:** `[Count]`  
**Total Blocked:** `[Count]`

### Critical Priority Issues
`[Summarize all critical issues found across all guides]`

### Recommendations for Fixes
`[List prioritized recommendations]`

---

**Tester Name:** `[Your name]`  
**Test Date:** `[Date]`  
**Test Duration:** `[Duration]`  
**Environment:** `[Development/Staging/Production]`
