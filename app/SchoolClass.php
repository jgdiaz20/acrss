<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    public $table = 'school_classes';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'program_id',
        'grade_level_id', // Keep for backward compatibility but make optional
        'section',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function classLessons()
    {
        return $this->hasMany(Lesson::class, 'class_id', 'id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'class_id', 'id');
    }

    public function classUsers()
    {
        return $this->hasMany(User::class, 'class_id', 'id');
    }

    public function program()
    {
        return $this->belongsTo(AcademicProgram::class, 'program_id', 'id');
    }

    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class, 'grade_level_id', 'id');
    }

    public function getDisplayNameAttribute()
    {
        $name = $this->name;
        if ($this->program) {
            $name = $this->program->name . ' - ' . $name;
        }
        // Grade level info is now embedded in the class name itself
        if ($this->section) {
            $name .= ' (' . $this->section . ')';
        }
        return $name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeByGradeLevel($query, $gradeLevelId)
    {
        return $query->where('grade_level_id', $gradeLevelId);
    }
}
