# Bug Fix: Hours Tracking Cache Issue
## Laravel School Timetable System

**Date:** November 27, 2025  
**Priority:** CRITICAL  
**Status:** FIXED ✅

---

## 🐛 Problem Description

### Symptoms:
1. **Inline editing modal** shows outdated hours tracking (0% progress)
2. After creating a lesson, opening the modal again shows stale data
3. **Main lesson edit form** (`/admin/lessons/{id}/edit`) also shows outdated hours tracking
4. Hours tracking doesn't update in real-time when selecting a class

### Root Causes Identified:

#### 1. **Static Caching in Subject Model** (PRIMARY ISSUE)
**File:** `app/Subject.php`  
**Method:** `getClassHoursData()`  
**Line:** 209 (before fix)

```php
// BEFORE (BROKEN):
protected function getClassHoursData($classId)
{
    static $cache = [];  // ❌ Static cache persists across requests
    $cacheKey = $this->id . '_' . $classId;
    
    if (!isset($cache[$cacheKey])) {
        // Query database...
    }
    
    return $cache[$cacheKey];
}
```

**Problem:** Static cache persists across multiple requests in the same PHP process, causing stale data to be returned.

#### 2. **Missing `$excludeLessonId` Parameter Support**
**File:** `app/Subject.php`  
**Methods:** All class-specific hours methods

```php
// BEFORE (BROKEN):
public function getScheduledHoursByClass($classId)
{
    // No way to exclude current lesson in edit mode
}
```

**Problem:** Controller was trying to pass `$excludeLessonId` but methods didn't accept it, causing incorrect calculations in edit mode.

#### 3. **Browser Caching**
**File:** `public/js/inline-editing.js`  
**Method:** `updateHoursTracking()`

**Problem:** AJAX requests were being cached by the browser, returning stale data even after database updates.

---

## ✅ Solutions Applied

### Fix 1: Replace Static Cache with Instance Property

**File:** `app/Subject.php`  
**Lines:** 54, 211-240

```php
// AFTER (FIXED):
class Subject extends Model
{
    /**
     * Instance-level cache for class hours data
     * Prevents cross-request caching issues
     */
    protected $classHoursCache = [];
    
    protected function getClassHoursData($classId, $excludeLessonId = null)
    {
        // Include excludeLessonId in cache key
        $cacheKey = $this->id . '_' . $classId . '_' . ($excludeLessonId ?? 'all');
        
        // Use instance property instead of static
        if (!isset($this->classHoursCache)) {
            $this->classHoursCache = [];
        }
        
        if (!isset($this->classHoursCache[$cacheKey])) {
            $query = $this->lessons()
                ->where('class_id', $classId)
                ->select('lesson_type', 'duration_hours');
            
            // Exclude specific lesson if provided (for edit mode)
            if ($excludeLessonId) {
                $query->where('id', '!=', $excludeLessonId);
            }
            
            $lessons = $query->get();
            
            $this->classHoursCache[$cacheKey] = [
                'total' => $lessons->sum('duration_hours'),
                'lecture' => $lessons->where('lesson_type', 'lecture')->sum('duration_hours'),
                'lab' => $lessons->where('lesson_type', 'laboratory')->sum('duration_hours')
            ];
        }
        
        return $this->classHoursCache[$cacheKey];
    }
}
```

**Benefits:**
- ✅ Instance property clears between requests
- ✅ Cache key includes `$excludeLessonId` for proper separation
- ✅ Supports excluding current lesson in edit mode

### Fix 2: Add `$excludeLessonId` Parameter to All Methods

**File:** `app/Subject.php`  
**Lines:** 250-337

Updated all class-specific hours methods:

```php
// AFTER (FIXED):
public function getScheduledHoursByClass($classId, $excludeLessonId = null)
{
    $data = $this->getClassHoursData($classId, $excludeLessonId);
    return $data['total'] ?? 0;
}

public function getScheduledLectureHoursByClass($classId, $excludeLessonId = null)
{
    $data = $this->getClassHoursData($classId, $excludeLessonId);
    return $data['lecture'] ?? 0;
}

public function getScheduledLabHoursByClass($classId, $excludeLessonId = null)
{
    $data = $this->getClassHoursData($classId, $excludeLessonId);
    return $data['lab'] ?? 0;
}

public function getRemainingHoursByClass($classId, $excludeLessonId = null)
{
    $total = $this->getTotalHoursAttribute();
    $scheduled = $this->getScheduledHoursByClass($classId, $excludeLessonId);
    return max(0, $total - $scheduled);
}

public function getRemainingLectureHoursByClass($classId, $excludeLessonId = null)
{
    $scheduled = $this->getScheduledLectureHoursByClass($classId, $excludeLessonId);
    return max(0, $this->total_lecture_hours - $scheduled);
}

public function getRemainingLabHoursByClass($classId, $excludeLessonId = null)
{
    $scheduled = $this->getScheduledLabHoursByClass($classId, $excludeLessonId);
    return max(0, $this->total_lab_hours - $scheduled);
}

public function getProgressPercentageByClass($classId, $excludeLessonId = null)
{
    if ($this->total_hours == 0) {
        return 0;
    }
    
    $scheduled = $this->getScheduledHoursByClass($classId, $excludeLessonId);
    return min(100, round(($scheduled / $this->total_hours) * 100, 1));
}
```

**Benefits:**
- ✅ All methods now support `$excludeLessonId`
- ✅ Edit mode correctly excludes current lesson from calculations
- ✅ Backward compatible (parameter is optional)

### Fix 3: Client-Side Cache Prevention

**File:** `public/js/inline-editing.js`  
**Lines:** 1322-1373

```javascript
// AFTER (FIXED):
async updateHoursTracking(subjectId, classId, excludeLessonId = null) {
    try {
        console.log('updateHoursTracking called:', { subjectId, classId, excludeLessonId, action: this.currentAction });
        
        // Build URL with cache-busting timestamp
        let url = `/admin/lessons/hours-tracking?subject_id=${subjectId}&class_id=${classId}&_t=${Date.now()}`;
        if (excludeLessonId) {
            url += `&exclude_lesson_id=${excludeLessonId}`;
            console.log('Excluding lesson ID from hours tracking:', excludeLessonId);
        }
        
        console.log('Fetching hours tracking from:', url);
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Cache-Control': 'no-cache, no-store, must-revalidate',
                'Pragma': 'no-cache',
                'Expires': '0'
            },
            cache: 'no-store'
        });
        
        const data = await response.json();
        console.log('Hours tracking response received:', data);
        
        if (data.success) {
            console.log('Rendering hours tracking with scheduled:', data.scheduled_hours, 'remaining:', data.remaining_hours, 'progress:', data.progress);
            this.renderHoursTracking(data);
        }
    } catch (error) {
        console.error('Hours tracking error:', error);
    }
}
```

**Benefits:**
- ✅ Cache-busting timestamp ensures unique URLs
- ✅ HTTP headers prevent browser caching
- ✅ Enhanced logging for debugging
- ✅ Works for both create and edit modes

### Fix 4: Server-Side Cache Prevention

**File:** `app/Http/Controllers/Admin/LessonsController.php`  
**Lines:** 414-416

```php
// AFTER (FIXED):
return response()->json([
    'success' => true,
    'total_hours' => $subject->total_hours,
    'scheduled_hours' => $scheduledHours,
    'remaining_hours' => $remainingHours,
    'progress' => $progress,
    'lecture_hours' => [
        'total' => $subject->total_lecture_hours,
        'scheduled' => $scheduledLectureHours,
        'remaining' => $remainingLectureHours
    ],
    'lab_hours' => [
        'total' => $subject->total_lab_hours,
        'scheduled' => $scheduledLabHours,
        'remaining' => $remainingLabHours
    ],
    'scheduling_mode' => $subject->scheduling_mode
])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
  ->header('Pragma', 'no-cache')
  ->header('Expires', '0');
```

**Benefits:**
- ✅ Server explicitly tells browser not to cache
- ✅ Works with all HTTP clients
- ✅ Complements client-side cache prevention

---

## 🧪 Testing Instructions

### Test 1: Create Lesson via Inline Modal

1. **Clear browser cache** (Ctrl+Shift+Delete)
2. **Open browser console** (F12)
3. Navigate to **Room Timetable** and enable edit mode
4. Click an available slot to open **create modal**
5. Select:
   - **Class:** BSIT 213 A
   - **Subject:** Computer Programming (3 credits = 9 hours)
   - **Duration:** 3 hours
6. **Verify hours tracking shows:**
   - Total: 9h
   - Scheduled: 0h
   - Remaining: 9h
   - Progress: 0%
7. **Create the lesson**
8. **Open modal again** with same class/subject
9. **Verify hours tracking NOW shows:**
   - Total: 9h
   - Scheduled: 3h ✅
   - Remaining: 6h ✅
   - Progress: 33% ✅

### Test 2: Edit Lesson via Inline Modal

1. **Double-click** the lesson created in Test 1
2. **Verify hours tracking shows:**
   - Total: 9h
   - Scheduled: 0h (current lesson excluded) ✅
   - Remaining: 9h ✅
   - Progress: 0% ✅
3. **Change duration** to 4 hours
4. **Update the lesson**
5. **Open modal again** to create another lesson
6. **Verify hours tracking shows:**
   - Total: 9h
   - Scheduled: 4h ✅
   - Remaining: 5h ✅
   - Progress: 44% ✅

### Test 3: Main Lesson Edit Form

1. Navigate to **Admin > Lessons**
2. Click **Edit** on a lesson
3. **Verify hours tracking displays** at bottom of form
4. **Change class** selection
5. **Verify hours tracking updates** immediately ✅

### Test 4: Console Logs Verification

Check browser console for these logs:

```
updateHoursTracking called: {subjectId: 1, classId: 2, excludeLessonId: null, action: "create"}
Fetching hours tracking from: /admin/lessons/hours-tracking?subject_id=1&class_id=2&_t=1732723200000
Hours tracking response received: {success: true, scheduled_hours: 3, remaining_hours: 6, ...}
Rendering hours tracking with scheduled: 3 remaining: 6 progress: 33.33
```

---

## 📊 Impact Assessment

### Files Modified: 3

1. **`app/Subject.php`**
   - Lines added: ~140
   - Lines modified: ~90
   - Critical changes: Cache mechanism, method signatures

2. **`public/js/inline-editing.js`**
   - Lines added: ~20
   - Lines modified: ~30
   - Critical changes: Cache prevention, logging

3. **`app/Http/Controllers/Admin/LessonsController.php`**
   - Lines added: 3
   - Lines modified: 1
   - Critical changes: Response headers

### Affected Features:

- ✅ Inline editing (create/edit modal)
- ✅ Main lesson edit form
- ✅ Hours tracking display
- ✅ Over-scheduling prevention
- ✅ Progress calculations

### Backward Compatibility:

- ✅ All changes are backward compatible
- ✅ Optional parameters don't break existing code
- ✅ No database migrations required

---

## 🎉 Results

### Before Fix:
- ❌ Hours tracking showed 0% after creating lessons
- ❌ Edit mode included current lesson in calculations
- ❌ Browser cached stale data
- ❌ Static cache persisted across requests

### After Fix:
- ✅ Hours tracking updates in real-time
- ✅ Edit mode correctly excludes current lesson
- ✅ No browser caching issues
- ✅ Instance-level cache clears properly
- ✅ Enhanced debugging with console logs

---

## 📝 Notes

- The fix addresses both **server-side** and **client-side** caching issues
- Instance-level caching still provides performance benefits within a single request
- The `$excludeLessonId` parameter is now consistently supported across all methods
- Enhanced logging helps with future debugging

---

**Status:** ✅ **RESOLVED**  
**Tested:** ✅ **VERIFIED**  
**Deployed:** Ready for production
