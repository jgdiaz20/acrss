# TESTING DATA SPECIFICATIONS

**Purpose:** Comprehensive test data for Hours Tracking & Credit System testing  
**Last Updated:** December 14, 2025

---

## 📋 PREPARATION STEPS

### **STEP 1: Clean Existing Data**

**Delete existing subjects and lessons:**

```sql
-- Run these SQL commands in your database
DELETE FROM lessons;
DELETE FROM subjects;

-- Reset auto-increment (optional)
ALTER TABLE lessons AUTO_INCREMENT = 1;
ALTER TABLE subjects AUTO_INCREMENT = 1;
```

**OR use Laravel Tinker:**

```php
php artisan tinker

// Delete all lessons and subjects
\App\Lesson::query()->forceDelete();
\App\Subject::query()->forceDelete();
```

---

## 🎓 TEST DATA TO CREATE

### **SUBJECTS - Complete Set**

#### **Lab Mode Subjects (Pure Laboratory)**

| # | Name | Code | Credits | Lab Units | Total Hours | Purpose |
|---|------|------|---------|-----------|-------------|---------|
| 1 | Computer Programming | COMPROG | 3 | 3 | 9h | Full credit lab testing |
| 2 | Database Systems | DBSYS | 2 | 2 | 6h | Mid-range lab testing |
| 3 | Physics | PHYSICS | 3 | 3 | 9h | Mode switching test |
| 4 | Chemistry | CHEM | 2 | 2 | 6h | Mode switching test |

**Creation Steps:**
1. Navigate to `/admin/subjects/create`
2. For each subject:
   - Enter Name and Code
   - Set Credits (1-3)
   - Select **Scheduling Mode:** `Lab (Pure Laboratory)`
   - Select **Subject Type:** `Major Subject`
   - Click **Create Subject**
3. Verify total hours = Credits × 3

---

#### **Lecture Mode Subjects (Pure Lecture)**

| # | Name | Code | Credits | Lecture Units | Total Hours | Purpose |
|---|------|------|---------|---------------|-------------|---------|
| 5 | Mathematics | MATH | 3 | 3 | 3h | Full credit lecture testing |
| 6 | English | ENG | 2 | 2 | 2h | Mid-range lecture testing |
| 7 | History | HIST | 1 | 1 | 1h | Minimum credit testing |

**Creation Steps:**
1. For each subject:
   - Enter Name and Code
   - Set Credits (1-3)
   - Select **Scheduling Mode:** `Lecture (Pure Lecture)`
   - Select **Subject Type:** `Major Subject`
   - Click **Create Subject**
2. Verify total hours = Credits × 1

---

#### **Flexible Mode Subjects (Mixed)**

| # | Name | Code | Lecture Units | Lab Units | Total Credits | Total Hours | Purpose |
|---|------|------|---------------|-----------|---------------|-------------|---------|
| 8 | Web Development | WEBDEV | 2 | 1 | 3 | 5h (2L+3Lab) | Lecture-heavy flexible |
| 9 | Data Structures | DATASTRUCT | 1 | 2 | 3 | 7h (1L+6Lab) | Lab-heavy flexible |
| 10 | Software Engineering | SOFTENG | 1 | 1 | 2 | 4h (1L+3Lab) | Balanced flexible |

**Creation Steps:**
1. For each subject:
   - Enter Name and Code
   - Select **Scheduling Mode:** `Flexible (Mixed)`
   - Set **Lecture Units** and **Laboratory Units**
   - Verify auto-calculated credits and hours
   - Select **Subject Type:** `Major Subject`
   - Click **Create Subject**

---

## 📚 LESSONS - Initial Test Set

### **For Computer Programming (COMPROG) - DIT-1A**

| Lesson | Weekday | Start Time | End Time | Duration | Purpose |
|--------|---------|------------|----------|----------|---------|
| 1 | Monday | 08:00 | 11:00 | 3h | First lesson (within limit) |
| 2 | Wednesday | 08:00 | 12:00 | 4h | Second lesson (cumulative) |
| 3 | Friday | 08:00 | 10:00 | 2h | Third lesson (complete 9h) |

**Total:** 9h / 9h (100% scheduled)

**Creation Steps:**
1. Navigate to `/admin/lessons/create`
2. For each lesson:
   - Select **Class:** `DIT-1A`
   - Select **Subject:** `Computer Programming (COMPROG)`
   - Verify **Lesson Type:** `Laboratory` (auto-selected, disabled)
   - Select **Teacher:** Any available teacher
   - Select **Room:** Computer Lab
   - Set Weekday and Times
   - Verify hours tracking updates
   - Click **Save**

---

### **For Database Systems (DBSYS) - DIT-1B**

| Lesson | Weekday | Start Time | End Time | Duration | Purpose |
|--------|---------|------------|----------|----------|---------|
| 1 | Tuesday | 13:00 | 16:00 | 3h | Partial scheduling |

**Total:** 3h / 6h (50% scheduled, 3h remaining)

---

### **For Mathematics (MATH) - DIT-1A**

| Lesson | Weekday | Start Time | End Time | Duration | Purpose |
|--------|---------|------------|----------|----------|---------|
| 1 | Tuesday | 09:00 | 10:30 | 1.5h | Partial scheduling |

**Total:** 1.5h / 3h (50% scheduled, 1.5h remaining)

---

### **For English (ENG) - DIT-1B**

| Lesson | Weekday | Start Time | End Time | Duration | Purpose |
|--------|---------|------------|----------|----------|---------|
| 1 | Thursday | 10:00 | 12:00 | 2h | Full scheduling |

**Total:** 2h / 2h (100% scheduled, 0h remaining) - **FULLY SCHEDULED**

---

### **For Web Development (WEBDEV) - DIT-1A**

| Lesson | Type | Weekday | Start Time | End Time | Duration | Purpose |
|--------|------|---------|------------|----------|----------|---------|
| 1 | Lecture | Tuesday | 10:00 | 11:30 | 1.5h | Lecture hours |
| 2 | Laboratory | Thursday | 13:00 | 16:00 | 3h | Lab hours (full) |

**Total:** 
- Lecture: 1.5h / 2h (75%, 0.5h remaining)
- Lab: 3h / 3h (100%, 0h remaining)

---

## 🎯 TEST DATA SUMMARY

### **Subjects Created: 10**
- Lab Mode: 4 subjects
- Lecture Mode: 3 subjects
- Flexible Mode: 3 subjects

### **Lessons Created: 8**
- Computer Programming (DIT-1A): 3 lessons, 9h (100%)
- Database Systems (DIT-1B): 1 lesson, 3h (50%)
- Mathematics (DIT-1A): 1 lesson, 1.5h (50%)
- English (DIT-1B): 1 lesson, 2h (100%) ← **FULLY SCHEDULED**
- Web Development (DIT-1A): 2 lessons, 4.5h total

### **Test Coverage**
- ✅ Fully scheduled subjects (100%)
- ✅ Partially scheduled subjects (50%)
- ✅ Unscheduled subjects (0%)
- ✅ Lab, Lecture, and Flexible modes
- ✅ Multiple classes (DIT-1A, DIT-1B)
- ✅ Various durations (1.5h - 4h)

---

## 📊 EXPECTED HOURS TRACKING STATE

### **After Creating All Test Data**

| Subject | Class | Scheduled | Total | Remaining | Progress | Status |
|---------|-------|-----------|-------|-----------|----------|--------|
| Computer Programming | DIT-1A | 9h | 9h | 0h | 100% | FULL |
| Database Systems | DIT-1B | 3h | 6h | 3h | 50% | PARTIAL |
| Mathematics | DIT-1A | 1.5h | 3h | 1.5h | 50% | PARTIAL |
| English | DIT-1B | 2h | 2h | 0h | 100% | FULL |
| Web Development (Lec) | DIT-1A | 1.5h | 2h | 0.5h | 75% | PARTIAL |
| Web Development (Lab) | DIT-1A | 3h | 3h | 0h | 100% | FULL |
| Physics | - | 0h | 9h | 9h | 0% | EMPTY |
| Chemistry | - | 0h | 6h | 6h | 0% | EMPTY |
| History | - | 0h | 1h | 1h | 0% | EMPTY |
| Data Structures | - | 0h | 7h | 7h | 0% | EMPTY |
| Software Engineering | - | 0h | 4h | 4h | 0% | EMPTY |

---

## 🔍 VERIFICATION CHECKLIST

### **After Creating Subjects**
- [ ] All 10 subjects created successfully
- [ ] Lab subjects show correct hours (Credits × 3)
- [ ] Lecture subjects show correct hours (Credits × 1)
- [ ] Flexible subjects show correct mixed hours
- [ ] No validation errors
- [ ] All subjects appear in subjects list

### **After Creating Lessons**
- [ ] All 8 lessons created successfully
- [ ] Hours tracking updates correctly after each lesson
- [ ] Fully scheduled subjects show 0h remaining
- [ ] Partially scheduled subjects show correct remaining hours
- [ ] Progress bars display accurately
- [ ] No duplicate lessons (same time/room conflicts)

### **Ready for Testing**
- [ ] Can test exceeding hours (Computer Programming, English)
- [ ] Can test within limits (Database Systems, Mathematics)
- [ ] Can test flexible mode (Web Development)
- [ ] Can test mode switching (Physics, Chemistry)
- [ ] Can test class-specific tracking (DIT-1A vs DIT-1B)

---

## 🚀 QUICK START COMMANDS

### **Create Subjects via Tinker (Optional)**

```php
php artisan tinker

// Lab Mode Subjects
\App\Subject::create([
    'name' => 'Computer Programming',
    'code' => 'COMPROG',
    'credits' => 3,
    'lecture_units' => 0,
    'lab_units' => 3,
    'scheduling_mode' => 'lab',
    'type' => 'major',
    'is_active' => true
]);

// Repeat for other subjects...
```

### **Verify Data**

```php
// Count subjects
\App\Subject::count(); // Should be 10

// Count lessons
\App\Lesson::count(); // Should be 8

// Check hours for a subject
$subject = \App\Subject::where('code', 'COMPROG')->first();
$subject->total_hours; // Should be 9
$subject->getScheduledHoursByClass(1); // Replace 1 with DIT-1A class ID
```

---

## 📝 NOTES

### **Important Considerations**
- Use consistent teacher and room assignments
- Ensure no time conflicts within same room
- Verify class IDs match your database
- Check that all subjects are active
- Confirm lesson types match subject modes

### **Common Issues**
- **Conflict errors:** Check room/teacher/class availability
- **Hours not updating:** Clear cache, refresh page
- **Validation errors:** Verify duration limits (Lab: 3-5h, Lecture: 1-3h)

---

**Data Preparation Date:** `[Date]`  
**Prepared By:** `[Name]`  
**Verification Status:** `[PENDING/COMPLETE]`
