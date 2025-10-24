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
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->ignore($this->subject->id),
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
}