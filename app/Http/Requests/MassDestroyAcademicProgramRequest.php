<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassDestroyAcademicProgramRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('academic_program_delete');
    }

    public function rules()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'exists:academic_programs,id',
        ];
    }
}