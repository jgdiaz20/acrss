<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeLevelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('grade_level_edit');
    }

    public function rules()
    {
        return [
            'program_id' => [
                'required',
                'exists:academic_programs,id',
            ],
            'level_name' => [
                'required',
                'string',
                'max:100',
            ],
            'level_code' => [
                'required',
                'string',
                'max:50',
            ],
            'level_order' => [
                'required',
                'integer',
                'min:1',
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