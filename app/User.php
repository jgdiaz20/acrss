<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    public $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_admin' => 'boolean',
        'is_teacher' => 'boolean',
        'is_student' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'class_id',
        'is_admin',
        'is_teacher',
        'is_student',
        'created_at',
        'updated_at',
        'remember_token',
        'email_verified_at',
    ];

    public function getIsAdminAttribute()
    {
        return $this->attributes['is_admin'] ?? false;
    }

    public function getIsTeacherAttribute()
    {
        return $this->attributes['is_teacher'] ?? false;
    }

    public function getIsStudentAttribute()
    {
        // Check if user has student role (role ID 4)
        return $this->roles()->where('id', 4)->exists() || ($this->attributes['is_student'] ?? false);
    }

    public function teacherLessons()
    {
        return $this->hasMany(Lesson::class, 'teacher_id', 'id');
    }

    public function getEmailVerifiedAtAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value)->format(config('panel.date_format') . ' ' . config('panel.time_format')) : null;
    }

    public function setEmailVerifiedAtAttribute($value)
    {
        $this->attributes['email_verified_at'] = $value ? Carbon::createFromFormat(config('panel.date_format') . ' ' . config('panel.time_format'), $value)->format('Y-m-d H:i:s') : null;
    }

    public function setPasswordAttribute($input)
    {
        if ($input) {
            $this->attributes['password'] = Hash::needsRehash($input) ? Hash::make($input) : $input;
        }
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'teacher_id', 'subject_id')
                    ->withPivot('is_primary', 'experience_years', 'notes', 'is_active')
                    ->withTimestamps();
    }

    public function teacherSubjects()
    {
        return $this->hasMany(TeacherSubject::class, 'teacher_id', 'id');
    }

    public function getPrimarySubjectsAttribute()
    {
        return $this->subjects()->wherePivot('is_primary', true)->get();
    }

    public function getActiveSubjectsAttribute()
    {
        return $this->subjects()->wherePivot('is_active', true)->get();
    }
}
