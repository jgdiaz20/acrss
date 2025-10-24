<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    use HasFactory;

    public $table = 'grade_levels';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'program_id',
        'level_name',
        'level_code',
        'level_order',
        'description',
        'is_active',
        'created_at',
        'updated_at',
    ];

    public function program()
    {
        return $this->belongsTo(AcademicProgram::class, 'program_id', 'id');
    }

    public function schoolClasses()
    {
        return $this->hasMany(SchoolClass::class, 'grade_level_id', 'id');
    }
    
    public function programSchoolClasses()
    {
        return $this->hasMany(SchoolClass::class, 'grade_level_id', 'id')
                    ->where('program_id', $this->program_id);
    }

    public function getDisplayNameAttribute()
    {
        return $this->level_name . ' (' . $this->level_code . ')';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('level_order');
    }
}