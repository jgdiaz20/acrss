<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassDestroyGradeLevelRequest extends FormRequest
{
    public function authorize()
    {
        return Gate::allows('grade_level_delete');
    }

    public function rules()
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'exists:grade_levels,id',
        ];
    }
}