<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassDestroyTeacherSubjectRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('teacher_subject_delete');
    }

    public function rules()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'exists:teacher_subjects,id',
        ];
    }
}