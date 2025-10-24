<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSubject extends Model
{
    use HasFactory;

    public $table = 'teacher_subjects';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'is_primary',
        'experience_years',
        'notes',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function getExperienceLevelAttribute()
    {
        $years = $this->experience_years ?? 0;
        
        if ($years >= 10) {
            return 'Expert';
        } elseif ($years >= 5) {
            return 'Advanced';
        } elseif ($years >= 2) {
            return 'Intermediate';
        } else {
            return 'Beginner';
        }
    }
}