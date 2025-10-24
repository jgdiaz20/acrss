<?php

namespace App\Http\Requests;

use App\Lesson;
use App\Rules\LessonTimeAvailabilityRule;
use App\Rules\SchoolHoursRule;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateLessonRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('lesson_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'class_id'   => [
                'required',
                'integer',
                'exists:school_classes,id'],
            'teacher_id' => [
                'required',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $subjectId = $this->input('subject_id');
                    if ($subjectId && $value) {
                        // Check if teacher is assigned to this subject
                        $teacherSubject = \App\TeacherSubject::where('teacher_id', $value)
                            ->where('subject_id', $subjectId)
                            ->where('is_active', true)
                            ->first();
                        
                        if (!$teacherSubject) {
                            $teacher = \App\User::find($value);
                            $subject = \App\Subject::find($subjectId);
                            $fail("Teacher {$teacher->name} is not assigned to subject {$subject->name}. Please assign the teacher to this subject first.");
                        }
                    }
                }],
            'room_id'    => [
                'required',
                'integer',
                'exists:rooms,id'],
            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id'],
            'weekday'    => [
                'required',
                'integer',
                'min:1',
                'max:7',
                function ($attribute, $value, $fail) {
                    // Weekend validation: Only diploma programs can have Saturday/Sunday classes
                    if (in_array($value, [6, 7])) {
                        $class = \App\SchoolClass::find($this->input('class_id'));
                        if ($class && $class->program) {
                            if ($class->program->type !== 'diploma') {
                                $programTypeName = $class->program->type === 'senior_high' 
                                    ? 'Senior High School' 
                                    : ucfirst(str_replace('_', ' ', $class->program->type));
                                $fail('Weekend classes (Saturday/Sunday) are only available for Diploma Programs. This class belongs to ' . $programTypeName . ' program.');
                            }
                        }
                    }
                }],
            'start_time' => [
                'required',
                new SchoolHoursRule(),
                new LessonTimeAvailabilityRule($this->route('lesson')->id),
                'date_format:' . config('panel.lesson_time_format', 'g:i A')],
            'end_time'   => [
                'required',
                new SchoolHoursRule(),
                'after:start_time',
                'date_format:' . config('panel.lesson_time_format', 'g:i A')],
        ];
    }

    public function messages()
    {
        return [
            'subject_id.required' => 'Please select a subject for this class schedule.',
            'subject_id.exists' => 'The selected subject is invalid. Please choose a valid subject.',
            'teacher_id.required' => 'Please select a teacher for this class schedule.',
            'teacher_id.integer' => 'Please select a valid teacher.',
            'teacher_id.exists' => 'The selected teacher is invalid. Please choose a valid teacher.',
            'class_id.required' => 'Please select a class for this schedule.',
            'class_id.exists' => 'The selected class is invalid. Please choose a valid class.',
            'room_id.required' => 'Please select a room for this class schedule.',
            'room_id.exists' => 'The selected room is invalid. Please choose a valid room.',
            'weekday.required' => 'Please select a day of the week.',
            'weekday.min' => 'Please select a valid day of the week.',
            'weekday.max' => 'Please select a valid day of the week.',
            'start_time.required' => 'Please enter a start time for the class.',
            'start_time.date_format' => 'Please enter the start time in the correct format (e.g., 9:00 AM).',
            'end_time.required' => 'Please enter an end time for the class.',
            'end_time.after' => 'The end time must be after the start time.',
            'end_time.date_format' => 'Please enter the end time in the correct format (e.g., 10:00 AM).',
        ];
    }
}
