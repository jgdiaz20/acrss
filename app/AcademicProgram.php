<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicProgram extends Model
{
    use HasFactory;

    public $table = 'academic_programs';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'code',
        'type',
        'duration_years',
        'description',
        'is_active',
        'created_at',
        'updated_at',
    ];

    const PROGRAM_TYPES = [
        'senior_high' => 'Senior High School',
        'college' => 'College',
        'diploma' => 'Diploma Program (TESDA)',
    ];

    const DURATION_BY_TYPE = [
        'senior_high' => 2,
        'college' => 4,
        'diploma' => 3,
    ];

    public function gradeLevels()
    {
        return $this->hasMany(GradeLevel::class, 'program_id', 'id');
    }

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class, 'program_id', 'id');
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}