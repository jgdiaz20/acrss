<?php

namespace App\Http\Requests;

use App\AcademicProgram;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateAcademicProgramRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('academic_program_edit');
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:academic_programs,code,' . request()->route('academic_program')->id,
            ],
            'type' => [
                'required',
                'in:senior_high,diploma,college',
            ],
            'duration_years' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }
}