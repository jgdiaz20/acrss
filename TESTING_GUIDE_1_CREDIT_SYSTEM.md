# TESTING GUIDE 1: CREDIT SYSTEM - SUBJECT MANAGEMENT

**Test Suite:** Hours Tracking & Credit System  
**Component:** Subject Creation and Configuration  
**Priority:** CRITICAL  
**Last Updated:** December 14, 2025

---

## 📋 TEST DATA SPECIFICATIONS

### Prerequisites
- **User Role:** Admin (full access)
- **Existing Data:** Classes and Rooms already created
- **Fresh Start:** Delete all existing subjects and lessons before testing

### Test Data Set

#### **Academic Programs & Classes (Assumed Existing)**
```
Program: Diploma in Information Technology
├─ Class: DIT-1A (Grade Level 11)
├─ Class: DIT-1B (Grade Level 11)
└─ Class: DIT-2A (Grade Level 12)

Program: Senior High School - STEM
├─ Class: STEM-11A (Grade Level 11)
└─ Class: STEM-12A (Grade Level 12)
```

#### **Test Subjects to Create**
```
1. Computer Programming (COMPROG) - Lab Mode, 3 credits
2. Database Systems (DBSYS) - Lab Mode, 2 credits
3. Mathematics (MATH) - Lecture Mode, 3 credits
4. English (ENG) - Lecture Mode, 2 credits
5. Web Development (WEBDEV) - Flexible Mode, 2 lecture + 1 lab units
6. Data Structures (DATASTRUCT) - Flexible Mode, 1 lecture + 2 lab units
```

---

## 🧪 TEST SCENARIOS

### **SCENARIO 1: LAB MODE SUBJECT CREATION**

#### **Test 1.1: Create Lab Mode Subject with 3 Credits**

**User Story:**  
*As an admin, I want to create a pure laboratory subject with 3 credits so that students can have 9 hours of lab time per class.*

**Prerequisites:**
- Logged in as Admin
- Navigate to `/admin/subjects`
- Click "Add Subject" button

**Test Steps:**
1. Fill in **Subject Name:** `Computer Programming`
2. Fill in **Subject Code:** `COMPROG`
3. Set **Total Credits:** `3`
4. Select **Scheduling Mode:** `Lab (Pure Laboratory)`
5. Select **Subject Type:** `Major Subject`
6. Click **Create Subject**

**Expected Result:**
- ✅ Subject created successfully
- ✅ Success message: "Subject created successfully"
- ✅ Redirected to subjects list
- ✅ Subject appears in table with:
  - Name: Computer Programming (COMPROG)
  - Credits: 3
  - Mode: Lab (Pure Laboratory)
  - **Calculated Hours:** 9 hours (3 credits × 3 hours)
  - Lecture Units: 0
  - Lab Units: 3

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 1.2: Create Lab Mode Subject with 2 Credits**

**User Story:**  
*As an admin, I want to create a laboratory subject with 2 credits to verify the credit-to-hours conversion works correctly.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `Database Systems`
3. Fill in **Subject Code:** `DBSYS`
4. Set **Total Credits:** `2`
5. Select **Scheduling Mode:** `Lab (Pure Laboratory)`
6. Select **Subject Type:** `Major Subject`
7. Click **Create Subject**

**Expected Result:**
- ✅ Subject created successfully
- ✅ **Calculated Hours:** 6 hours (2 credits × 3 hours)
- ✅ Lecture Units: 0
- ✅ Lab Units: 2

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 1.3: Lab Mode Credit Validation - Below Minimum**

**User Story:**  
*As an admin, I should be prevented from creating a subject with 0 credits to ensure data integrity.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `Invalid Lab Subject`
3. Fill in **Subject Code:** `INVALID`
4. Set **Total Credits:** `0`
5. Select **Scheduling Mode:** `Lab (Pure Laboratory)`
6. Select **Subject Type:** `Major Subject`
7. Click **Create Subject**

**Expected Result:**
- ❌ Subject NOT created
- ❌ Error message displayed: "Credits must be between 1 and 3"
- ❌ Form remains on create page with entered data preserved

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 1.4: Lab Mode Credit Validation - Above Maximum**

**User Story:**  
*As an admin, I should be prevented from creating a subject with more than 3 credits to comply with academic standards.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `Invalid Lab Subject`
3. Fill in **Subject Code:** `INVALID`
4. Set **Total Credits:** `4`
5. Select **Scheduling Mode:** `Lab (Pure Laboratory)`
6. Select **Subject Type:** `Major Subject`
7. Click **Create Subject**

**Expected Result:**
- ❌ Subject NOT created
- ❌ Error message displayed: "Credits must be between 1 and 3"
- ❌ Form validation prevents submission

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 2: LECTURE MODE SUBJECT CREATION**

#### **Test 2.1: Create Lecture Mode Subject with 3 Credits**

**User Story:**  
*As an admin, I want to create a pure lecture subject with 3 credits so that students can have 3 hours of lecture time per class.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `Mathematics`
3. Fill in **Subject Code:** `MATH`
4. Set **Total Credits:** `3`
5. Select **Scheduling Mode:** `Lecture (Pure Lecture)`
6. Select **Subject Type:** `Major Subject`
7. Click **Create Subject**

**Expected Result:**
- ✅ Subject created successfully
- ✅ **Calculated Hours:** 3 hours (3 credits × 1 hour)
- ✅ Lecture Units: 3
- ✅ Lab Units: 0

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 2.2: Create Lecture Mode Subject with 2 Credits**

**User Story:**  
*As an admin, I want to create a lecture subject with 2 credits to verify the credit-to-hours conversion.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `English`
3. Fill in **Subject Code:** `ENG`
4. Set **Total Credits:** `2`
5. Select **Scheduling Mode:** `Lecture (Pure Lecture)`
6. Select **Subject Type:** `Major Subject`
7. Click **Create Subject**

**Expected Result:**
- ✅ Subject created successfully
- ✅ **Calculated Hours:** 2 hours (2 credits × 1 hour)
- ✅ Lecture Units: 2
- ✅ Lab Units: 0

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 2.3: Lecture Mode Credit Validation**

**User Story:**  
*As an admin, I should be prevented from creating a lecture subject with invalid credits.*

**Test Steps:**
1. Try creating with **Credits:** `0` - Should fail
2. Try creating with **Credits:** `5` - Should fail
3. Try creating with **Credits:** `1` - Should succeed

**Expected Result:**
- ❌ Credits 0 and 5: Error message "Credits must be between 1 and 3"
- ✅ Credits 1: Subject created successfully with 1 hour total

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 3: FLEXIBLE MODE SUBJECT CREATION**

#### **Test 3.1: Create Flexible Mode Subject (2 Lecture + 1 Lab)**

**User Story:**  
*As an admin, I want to create a mixed lecture/lab subject so that students can have both theoretical and practical learning.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `Web Development`
3. Fill in **Subject Code:** `WEBDEV`
4. Select **Scheduling Mode:** `Flexible (Mixed)`
5. **Flexible fields should appear**
6. Set **Lecture Units:** `2`
7. Set **Laboratory Units:** `1`
8. **Verify auto-calculation:**
   - Total Hours: 5 hours (2 lecture + 3 lab)
   - Total Credits: 3 (2 + 1)
9. Select **Subject Type:** `Major Subject`
10. Click **Create Subject**

**Expected Result:**
- ✅ Subject created successfully
- ✅ Credits auto-calculated: 3
- ✅ Total Hours: 5 (2 lecture + 3 lab)
- ✅ Lecture Units: 2
- ✅ Lab Units: 1

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 3.2: Create Flexible Mode Subject (1 Lecture + 2 Lab)**

**User Story:**  
*As an admin, I want to create a flexible subject with more lab than lecture hours.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `Data Structures`
3. Fill in **Subject Code:** `DATASTRUCT`
4. Select **Scheduling Mode:** `Flexible (Mixed)`
5. Set **Lecture Units:** `1`
6. Set **Laboratory Units:** `2`
7. **Verify auto-calculation:**
   - Total Hours: 7 hours (1 lecture + 6 lab)
   - Total Credits: 3 (1 + 2)
8. Select **Subject Type:** `Major Subject`
9. Click **Create Subject**

**Expected Result:**
- ✅ Subject created successfully
- ✅ Credits auto-calculated: 3
- ✅ Total Hours: 7 (1 lecture + 6 lab)
- ✅ Lecture Units: 1
- ✅ Lab Units: 2

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 3.3: Flexible Mode - Exceeding Total Credits**

**User Story:**  
*As an admin, I should be prevented from creating a flexible subject that exceeds 3 total credits.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `Invalid Flexible`
3. Fill in **Subject Code:** `INVALID`
4. Select **Scheduling Mode:** `Flexible (Mixed)`
5. Set **Lecture Units:** `2`
6. Set **Laboratory Units:** `2` (Total: 4 credits)
7. Click **Create Subject**

**Expected Result:**
- ❌ Subject NOT created
- ❌ Error message: "Total credits (lecture + lab) cannot exceed 3"
- ❌ Form validation prevents submission

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 3.4: Flexible Mode - Zero Units Validation**

**User Story:**  
*As an admin, I should be prevented from creating a flexible subject with zero units in both fields.*

**Test Steps:**
1. Click "Add Subject"
2. Fill in **Subject Name:** `Invalid Flexible`
3. Fill in **Subject Code:** `INVALID`
4. Select **Scheduling Mode:** `Flexible (Mixed)`
5. Set **Lecture Units:** `0`
6. Set **Laboratory Units:** `0`
7. Click **Create Subject**

**Expected Result:**
- ❌ Subject NOT created
- ❌ Error message: "At least one unit (lecture or lab) must be greater than 0"
- ❌ Form validation prevents submission

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

### **SCENARIO 4: SUBJECT EDITING & MODE SWITCHING**

#### **Test 4.1: Edit Subject Without Lessons (Safe Edit)**

**User Story:**  
*As an admin, I want to edit a subject that has no lessons yet without any warnings.*

**Prerequisites:**
- Subject "Computer Programming" created (no lessons scheduled)

**Test Steps:**
1. Navigate to `/admin/subjects`
2. Click **Edit** on "Computer Programming"
3. Change **Subject Name** to `Computer Programming I`
4. Change **Credits** from `3` to `2`
5. Click **Update Subject**

**Expected Result:**
- ✅ Subject updated successfully
- ✅ No warning messages
- ✅ Credits changed: 3 → 2
- ✅ Total hours recalculated: 9h → 6h
- ✅ Lab units updated: 3 → 2

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 4.2: Edit Subject With Lessons (Warning Display)**

**User Story:**  
*As an admin, I should see a warning when editing a subject that has existing lessons.*

**Prerequisites:**
- Subject "Mathematics" created with at least 1 lesson scheduled

**Test Steps:**
1. Navigate to `/admin/subjects`
2. Click **Edit** on "Mathematics"
3. **Verify warning alert appears:**
   - "⚠️ Warning: This subject has X existing lesson(s)"
   - Shows lesson count
4. Try changing **Scheduling Mode** from `Lecture` to `Lab`
5. **Verify confirmation modal appears:**
   - Shows current lesson count
   - Warns about potential impacts
   - Requires explicit confirmation

**Expected Result:**
- ✅ Warning alert displayed on page load
- ✅ Confirmation modal appears when changing mode
- ✅ Modal shows accurate lesson count
- ✅ Can cancel without changes
- ✅ Can confirm and proceed with changes

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 4.3: Mode Switch Impact - Lab to Lecture**

**User Story:**  
*As an admin, I want to understand the impact of switching from Lab mode to Lecture mode on existing lessons.*

**Prerequisites:**
- Subject "Database Systems" (Lab, 2 credits, 6 hours)
- Has 1 lesson: 4-hour lab session

**Test Steps:**
1. Edit "Database Systems"
2. Change **Scheduling Mode:** `Lab` → `Lecture`
3. Keep **Credits:** `2`
4. Confirm changes
5. Navigate to lessons page
6. Check the existing lesson

**Expected Result:**
- ✅ Subject updated: Now 2 lecture hours (not 6)
- ⚠️ **CRITICAL:** Existing 4-hour lesson now exceeds total hours
- ⚠️ Hours tracking should show: Scheduled 4h / Total 2h (exceeded)
- ⚠️ Cannot create new lessons until issue resolved

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations - This tests credit calculation accuracy]`

---

### **SCENARIO 5: CREDIT CALCULATION ACCURACY (CRITICAL)**

#### **Test 5.1: Verify Lab Credit-to-Hours Conversion**

**User Story:**  
*As an admin, I need to verify that lab credits correctly convert to hours at a 1:3 ratio.*

**Test Steps:**
1. Create 3 lab subjects with different credits:
   - Subject A: 1 credit → Should be 3 hours
   - Subject B: 2 credits → Should be 6 hours
   - Subject C: 3 credits → Should be 9 hours
2. For each subject, navigate to edit page
3. Verify displayed total hours match expected

**Expected Result:**
- ✅ 1 credit = 3 hours (Lab Units: 1)
- ✅ 2 credits = 6 hours (Lab Units: 2)
- ✅ 3 credits = 9 hours (Lab Units: 3)
- ✅ All calculations accurate

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 5.2: Verify Lecture Credit-to-Hours Conversion**

**User Story:**  
*As an admin, I need to verify that lecture credits correctly convert to hours at a 1:1 ratio.*

**Test Steps:**
1. Create 3 lecture subjects with different credits:
   - Subject A: 1 credit → Should be 1 hour
   - Subject B: 2 credits → Should be 2 hours
   - Subject C: 3 credits → Should be 3 hours
2. Verify displayed total hours match expected

**Expected Result:**
- ✅ 1 credit = 1 hour (Lecture Units: 1)
- ✅ 2 credits = 2 hours (Lecture Units: 2)
- ✅ 3 credits = 3 hours (Lecture Units: 3)
- ✅ All calculations accurate

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

#### **Test 5.3: Verify Flexible Mode Mixed Calculation**

**User Story:**  
*As an admin, I need to verify that flexible mode correctly calculates mixed lecture and lab hours.*

**Test Steps:**
1. Create flexible subject:
   - Lecture Units: 2
   - Lab Units: 1
2. Verify calculations:
   - Lecture Hours: 2 × 1 = 2 hours
   - Lab Hours: 1 × 3 = 3 hours
   - Total Hours: 2 + 3 = 5 hours
   - Total Credits: 2 + 1 = 3 credits

**Expected Result:**
- ✅ Lecture hours: 2
- ✅ Lab hours: 3
- ✅ Total hours: 5
- ✅ Total credits: 3
- ✅ All calculations accurate

**Actual Result:**  
`[Your feedback here]`

**Status:** `[PASS/FAIL/BLOCKED]`

**Notes:**  
`[Additional observations]`

---

## 📊 TEST SUMMARY

**Total Tests:** 18  
**Passed:** `[Count]`  
**Failed:** `[Count]`  
**Blocked:** `[Count]`

### Critical Issues Found
`[List any critical issues discovered during testing]`

### Recommendations
`[List any recommendations for improvements]`

---

## 🔄 NEXT STEPS

After completing this test suite:
1. ✅ Proceed to **TESTING_GUIDE_2_HOURS_TRACKING_LESSONS.md**
2. Use the subjects created here for lesson creation tests
3. Focus on hours tracking validation and display

---

**Tester Name:** `[Your name]`  
**Test Date:** `[Date]`  
**Test Duration:** `[Duration]`  
**Environment:** `[Development/Staging/Production]`
