<?php

namespace App\Rules;

use App\TeacherSubject;
use Illuminate\Contracts\Validation\Rule;

class TeacherSubjectAssignmentRule implements Rule
{
    private $teacherId;
    private $subjectId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($teacherId, $subjectId)
    {
        $this->teacherId = $teacherId;
        $this->subjectId = $subjectId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!$this->teacherId || !$this->subjectId) {
            return true; // Let other validation rules handle missing values
        }

        // Check if teacher is assigned to this subject
        return TeacherSubject::where('teacher_id', $this->teacherId)
            ->where('subject_id', $this->subjectId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $teacher = \App\User::find($this->teacherId);
        $subject = \App\Subject::find($this->subjectId);
        
        if ($teacher && $subject) {
            return "Teacher {$teacher->name} is not assigned to subject {$subject->name}. Please assign the teacher to this subject first.";
        }
        
        return 'The selected teacher is not assigned to the selected subject.';
    }
}
