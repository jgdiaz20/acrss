<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public $table = 'subjects';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
        'requires_lab' => 'boolean',
        'requires_equipment' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'code',
        'description',
        'credits',
        'lecture_units',
        'lab_units',
        'scheduling_mode',
        'type',
        'requires_lab',
        'requires_equipment',
        'equipment_requirements',
        'is_active',
        'created_at',
        'updated_at',
    ];

    const SUBJECT_TYPES = [
        'minor' => 'Minor Subject',
        'major' => 'Major Subject',
    ];

    const SCHEDULING_MODES = [
        'lab' => 'Lab (Pure Laboratory)',
        'lecture' => 'Lecture (Pure Lecture)',
        'flexible' => 'Flexible (Mixed)',
    ];

    /**
     * Instance-level cache for class hours data
     * Prevents cross-request caching issues
     */
    protected $classHoursCache = [];

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'subject_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_subjects', 'subject_id', 'teacher_id')
                    ->withPivot('is_active')
                    ->withTimestamps();
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'subject_id', 'id');
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    public function getFullDescriptionAttribute()
    {
        $description = $this->name . ' (' . $this->code . ')';
        if ($this->description) {
            $description .= ' - ' . $this->description;
        }
        $description .= ' - ' . $this->credits . ' credits';
        return $description;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRequiresLab($query)
    {
        return $query->where('requires_lab', true);
    }

    public function scopeRequiresEquipment($query)
    {
        return $query->where('requires_equipment', true);
    }

    public function scopeMinor($query)
    {
        return $query->where('type', 'minor');
    }

    public function scopeMajor($query)
    {
        return $query->where('type', 'major');
    }

    /**
     * Get total lecture hours (1 hour per unit)
     */
    public function getTotalLectureHoursAttribute()
    {
        return $this->lecture_units * 1;
    }

    /**
     * Get total lab hours (3 hours per unit)
     */
    public function getTotalLabHoursAttribute()
    {
        return $this->lab_units * 3;
    }

    /**
     * Get total hours for the subject
     */
    public function getTotalHoursAttribute()
    {
        return $this->total_lecture_hours + $this->total_lab_hours;
    }

    /**
     * Get scheduled lecture hours
     */
    public function getScheduledLectureHoursAttribute()
    {
        return $this->lessons()->where('lesson_type', 'lecture')->sum('duration_hours') ?? 0;
    }

    /**
     * Get scheduled lab hours
     */
    public function getScheduledLabHoursAttribute()
    {
        return $this->lessons()->where('lesson_type', 'laboratory')->sum('duration_hours') ?? 0;
    }

    /**
     * Get total scheduled hours
     */
    public function getScheduledHoursAttribute()
    {
        return $this->scheduled_lecture_hours + $this->scheduled_lab_hours;
    }

    /**
     * Get remaining lecture hours
     */
    public function getRemainingLectureHoursAttribute()
    {
        return max(0, $this->total_lecture_hours - $this->scheduled_lecture_hours);
    }

    /**
     * Get remaining lab hours
     */
    public function getRemainingLabHoursAttribute()
    {
        return max(0, $this->total_lab_hours - $this->scheduled_lab_hours);
    }

    /**
     * Get remaining total hours
     */
    public function getRemainingHoursAttribute()
    {
        return max(0, $this->total_hours - $this->scheduled_hours);
    }

    /**
     * Check if subject is fully scheduled
     */
    public function isFullyScheduled()
    {
        return $this->remaining_hours <= 0;
    }

    /**
     * Get scheduling progress percentage
     */
    public function getSchedulingProgressAttribute()
    {
        if ($this->total_hours == 0) {
            return 0;
        }
        return min(100, round(($this->scheduled_hours / $this->total_hours) * 100, 1));
    }

    /**
     * OPTIMIZED: Get all scheduled hours data for a class in one query
     * This method caches the result to avoid redundant queries
     * 
     * @param int $classId The class ID
     * @param int|null $excludeLessonId Optional lesson ID to exclude (for edit mode)
     * @return array Array with 'total', 'lecture', and 'lab' hours
     */
    protected function getClassHoursData($classId, $excludeLessonId = null)
    {
        // Include excludeLessonId in cache key to ensure separate caching
        $cacheKey = $this->id . '_' . $classId . '_' . ($excludeLessonId ?? 'all');
        
        // Use instance property instead of static to avoid cross-request caching
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

    /**
     * Get scheduled hours for a specific class
     * 
     * @param int $classId The class ID
     * @param int|null $excludeLessonId Optional lesson ID to exclude (for edit mode)
     * @return float Total scheduled hours
     */
    public function getScheduledHoursByClass($classId, $excludeLessonId = null)
    {
        $data = $this->getClassHoursData($classId, $excludeLessonId);
        return $data['total'] ?? 0;
    }

    /**
     * Get scheduled lecture hours for a specific class
     * 
     * @param int $classId The class ID
     * @param int|null $excludeLessonId Optional lesson ID to exclude (for edit mode)
     * @return float Scheduled lecture hours
     */
    public function getScheduledLectureHoursByClass($classId, $excludeLessonId = null)
    {
        $data = $this->getClassHoursData($classId, $excludeLessonId);
        return $data['lecture'] ?? 0;
    }

    /**
     * Get scheduled lab hours for a specific class
     * 
     * @param int $classId The class ID
     * @param int|null $excludeLessonId Optional lesson ID to exclude (for edit mode)
     * @return float Scheduled lab hours
     */
    public function getScheduledLabHoursByClass($classId, $excludeLessonId = null)
    {
        $data = $this->getClassHoursData($classId, $excludeLessonId);
        return $data['lab'] ?? 0;
    }

    /**
     * Get remaining hours for a specific class
     * 
     * @param int $classId The class ID
     * @param int|null $excludeLessonId Optional lesson ID to exclude (for edit mode)
     * @return float Remaining hours
     */
    public function getRemainingHoursByClass($classId, $excludeLessonId = null)
    {
        $total = $this->getTotalHoursAttribute();
        $scheduled = $this->getScheduledHoursByClass($classId, $excludeLessonId);
        return max(0, $total - $scheduled);
    }

    /**
     * Get remaining lecture hours for a specific class
     * 
     * @param int $classId The class ID
     * @param int|null $excludeLessonId Optional lesson ID to exclude (for edit mode)
     * @return float Remaining lecture hours
     */
    public function getRemainingLectureHoursByClass($classId, $excludeLessonId = null)
    {
        $scheduled = $this->getScheduledLectureHoursByClass($classId, $excludeLessonId);
        return max(0, $this->total_lecture_hours - $scheduled);
    }

    /**
     * Get remaining lab hours for a specific class
     * 
     * @param int $classId The class ID
     * @param int|null $excludeLessonId Optional lesson ID to exclude (for edit mode)
     * @return float Remaining lab hours
     */
    public function getRemainingLabHoursByClass($classId, $excludeLessonId = null)
    {
        $scheduled = $this->getScheduledLabHoursByClass($classId, $excludeLessonId);
        return max(0, $this->total_lab_hours - $scheduled);
    }

    /**
     * Get scheduling progress percentage for a specific class
     * 
     * @param int $classId The class ID
     * @param int|null $excludeLessonId Optional lesson ID to exclude (for edit mode)
     * @return float Progress percentage (0-100)
     */
    public function getProgressPercentageByClass($classId, $excludeLessonId = null)
    {
        if ($this->total_hours == 0) {
            return 0;
        }
        
        $scheduled = $this->getScheduledHoursByClass($classId, $excludeLessonId);
        return min(100, round(($scheduled / $this->total_hours) * 100, 1));
    }

    /**
     * Get mode badge HTML
     */
    public function getModeBadgeAttribute()
    {
        $modes = [
            'lab' => '<span class="badge badge-info">LAB</span>',
            'lecture' => '<span class="badge badge-success">LECTURE</span>',
            'flexible' => '<span class="badge badge-warning">FLEXIBLE</span>'
        ];
        
        return $modes[$this->scheduling_mode] ?? '';
    }
}