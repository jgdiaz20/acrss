<?php

namespace App\Http\Requests;

use App\Lesson;
use App\Rules\LessonTimeAvailabilityRule;
use App\Rules\SchoolHoursRule;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreLessonRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('lesson_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

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
            'lesson_type' => [
                'required',
                'string',
                'in:lecture,laboratory'],
            'weekday'    => [
                'required',
                'integer',
                'min:1',
                'max:7',
            ],
            'start_time' => [
                'required',
                new SchoolHoursRule(),
                new LessonTimeAvailabilityRule(),
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
            'room_id.required' => 'Please select a room for this class schedule. All rooms including labs and equipped rooms are available.',
            'room_id.exists' => 'The selected room is invalid. Please choose a valid room.',
            'room_id.available' => 'The selected room is not available for this time slot.',
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
