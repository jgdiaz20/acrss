<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('subject_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects', 'name')->ignore($this->subject->id),
                'regex:/^[a-zA-Z0-9\s\-\(\)]+$/',
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->ignore($this->subject->id),
                'regex:/^[A-Z0-9\-]+$/',
            ],
            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'credits' => [
                'required',
                'integer',
                'min:1',
                'max:3',
            ],
            'scheduling_mode' => [
                'required',
                'string',
                'in:lab,lecture,flexible',
            ],
            'lecture_units' => [
                'nullable',
                'integer',
                'min:0',
                'max:10',
            ],
            'lab_units' => [
                'nullable',
                'integer',
                'min:0',
                'max:10',
            ],
            'type' => [
                'required',
                'string',
                'in:minor,major',
            ],
            'requires_lab' => [
                'boolean',
            ],
            'requires_equipment' => [
                'boolean',
            ],
            'equipment_requirements' => [
                'nullable',
                'string',
                'max:500',
            ],
            'is_active' => [
                'boolean',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'Subject name may only contain letters, numbers, spaces, hyphens, and parentheses.',
            'code.regex' => 'Subject code must be uppercase letters, numbers, and hyphens only (e.g., CS-101, MATH-201).',
        ];
    }
}