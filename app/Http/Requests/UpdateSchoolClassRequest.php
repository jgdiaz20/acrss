<?php

namespace App\Http\Requests;

use App\SchoolClass;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateSchoolClassRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('school_class_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'program_id' => [
                'required',
                'exists:academic_programs,id'
            ],
            'grade_level_id' => [
                'nullable',
                'exists:grade_levels,id'
            ],
            'section' => [
                'nullable',
                'string',
                'max:50'
            ],
            'is_active' => [
                'nullable',
                'boolean'
            ],
        ];

        // Make grade_level_id required for senior high school programs and validate it belongs to the program
        if ($this->has('program_id')) {
            $program = \App\AcademicProgram::find($this->input('program_id'));
            if ($program && $program->type === 'senior_high') {
                $rules['grade_level_id'] = [
                    'required',
                    'exists:grade_levels,id',
                    function ($attribute, $value, $fail) {
                        $programId = $this->input('program_id');
                        $gradeLevel = \App\GradeLevel::where('id', $value)
                            ->where('program_id', $programId)
                            ->first();
                        if (!$gradeLevel) {
                            $fail('The selected grade level does not belong to the selected program.');
                        }
                    },
                ];
            }
        }

        return $rules;
    }
}
