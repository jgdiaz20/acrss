<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherSubjectRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('teacher_subject_edit');
    }

    public function rules()
    {
        return [
            'teacher_id' => [
                'required',
                'exists:users,id',
            ],
            'subject_id' => [
                'required',
                'exists:subjects,id',
            ],
            'is_primary' => [
                'boolean',
            ],
            'experience_years' => [
                'nullable',
                'integer',
                'min:0',
                'max:50',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }
}