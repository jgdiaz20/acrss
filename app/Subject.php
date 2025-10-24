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

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'subject_id', 'id');
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'teacher_subjects', 'subject_id', 'teacher_id')
                    ->withPivot('is_primary', 'experience_years', 'notes', 'is_active')
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
}