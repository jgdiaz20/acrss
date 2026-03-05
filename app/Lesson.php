<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    public $table = 'lessons';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'weekday',
        'class_id',
        'room_id',
        'subject_id',
        'lesson_type',
        'duration_hours',
        'end_time',
        'teacher_id',
        'start_time',
        'created_at',
        'updated_at',
    ];

    const WEEK_DAYS = [
        '1' => 'Monday',
        '2' => 'Tuesday',
        '3' => 'Wednesday',
        '4' => 'Thursday',
        '5' => 'Friday',
        '6' => 'Saturday',
        '7' => 'Sunday',
    ];

    const LESSON_TYPES = [
        'lecture' => 'Lecture',
        'laboratory' => 'Laboratory',
    ];

    public function getDifferenceAttribute()
    {
        return Carbon::parse($this->end_time)->diffInMinutes($this->start_time);
    }

    public function getStartTimeAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        try {
            return Carbon::createFromFormat('H:i:s', $value)->format(config('panel.lesson_time_format', 'g:i A'));
        } catch (\Exception $e) {
            \Log::warning('Invalid start_time format in Lesson model', ['value' => $value, 'error' => $e->getMessage()]);
            return $value;
        }
    }

    public function setStartTimeAttribute($value)
    {
        if (!$value) {
            $this->attributes['start_time'] = null;
            return;
        }
        
        try {
            $this->attributes['start_time'] = Carbon::createFromFormat(config('panel.lesson_time_format', 'g:i A'), $value)->format('H:i:s');
        } catch (\Exception $e) {
            \Log::warning('Invalid start_time format when setting Lesson model', ['value' => $value, 'error' => $e->getMessage()]);
            $this->attributes['start_time'] = $value;
        }
    }

    public function getEndTimeAttribute($value)
    {
        if (!$value) {
            return null;
        }
        
        try {
            return Carbon::createFromFormat('H:i:s', $value)->format(config('panel.lesson_time_format', 'g:i A'));
        } catch (\Exception $e) {
            \Log::warning('Invalid end_time format in Lesson model', ['value' => $value, 'error' => $e->getMessage()]);
            return $value;
        }
    }

    public function setEndTimeAttribute($value)
    {
        if (!$value) {
            $this->attributes['end_time'] = null;
            return;
        }
        
        try {
            $this->attributes['end_time'] = Carbon::createFromFormat(config('panel.lesson_time_format', 'g:i A'), $value)->format('H:i:s');
        } catch (\Exception $e) {
            \Log::warning('Invalid end_time format when setting Lesson model', ['value' => $value, 'error' => $e->getMessage()]);
            $this->attributes['end_time'] = $value;
        }
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public static function isTimeAvailable($weekday, $startTime, $endTime, $class, $teacher, $room, $lesson = null)
    {
        $lessons = self::where('weekday', $weekday)
            ->when($lesson, function ($query) use ($lesson) {
                $query->where('id', '!=', $lesson);
            })
            ->where(function ($query) use ($class, $teacher, $room) {
                $query->where('class_id', $class)
                    ->orWhere('teacher_id', $teacher)
                    ->orWhere('room_id', $room);
            })
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where([
                    ['start_time', '<', $endTime],
                    ['end_time', '>', $startTime],
                ]);
            })
            ->count();

        return !$lessons;
    }

    /**
     * Get detailed conflict information for this lesson
     */
    public function getConflicts($excludeId = null)
    {
        $conflictService = app(\App\Services\SchedulingConflictService::class);
        return $conflictService->checkConflicts(
            $this->weekday,
            $this->getRawOriginal('start_time'),
            $this->getRawOriginal('end_time'),
            $this->class_id,
            $this->teacher_id,
            $this->room_id,
            $excludeId
        );
    }

    /**
     * Check if this lesson has any conflicts
     */
    public function hasConflicts($excludeId = null)
    {
        return !empty($this->getConflicts($excludeId));
    }

    public function scopeCalendarByRoleOrClassId($query)
    {
        return $query->when(!request()->input('class_id'), function ($query) {
            $query->when(auth()->user()->is_teacher, function ($query) {
                $query->where('teacher_id', auth()->user()->id);
            })
                ->when(auth()->user()->is_student, function ($query) {
                    $query->where('class_id', auth()->user()->class_id ?? '0');
                });
        })
            ->when(request()->input('class_id'), function ($query) {
                $query->where('class_id', request()->input('class_id'));
            });
    }

    /**
     * Calculate duration in hours from start and end time
     */
    public static function calculateDuration($startTime, $endTime)
    {
        try {
            $start = Carbon::parse($startTime);
            $end = Carbon::parse($endTime);
            $minutes = $end->diffInMinutes($start);
            
            // Round to nearest 30 minutes
            $roundedMinutes = round($minutes / 30) * 30;
            $hours = $roundedMinutes / 60;
            
            return $hours;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get formatted duration display
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_hours) {
            return 'N/A';
        }
        
        $hours = floor($this->duration_hours);
        $minutes = ($this->duration_hours - $hours) * 60;
        
        if ($minutes > 0) {
            return $hours . 'h ' . round($minutes) . 'm';
        }
        return $hours . 'h';
    }
}
