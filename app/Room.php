<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public $table = 'rooms';

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_lab' => 'boolean',
        'has_equipment' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'is_lab',
        'has_equipment',
        'created_at',
        'updated_at',
    ];

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'room_id', 'id');
    }

    public function getDisplayNameAttribute()
    {
        return $this->name . ($this->description ? ' (' . $this->description . ')' : '');
    }

    /**
     * Get room type based on lab status
     */
    public function getTypeAttribute()
    {
        if ($this->is_lab) {
            return 'lab';
        }
        return 'classroom';
    }

    /**
     * Get equipment information
     */
    public function getEquipmentAttribute()
    {
        if ($this->has_equipment) {
            return 'Available';
        }
        return 'Not Available';
    }
}