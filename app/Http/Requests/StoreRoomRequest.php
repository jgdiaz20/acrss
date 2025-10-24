<?php

namespace App\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreRoomRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('room_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:rooms,name',
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
            ],
            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:500',
            ],
            'is_lab' => [
                'required',
                'boolean',
            ],
            'has_equipment' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Room name is required',
            'name.string' => 'Room name must be a valid text',
            'name.max' => 'Room name cannot exceed 255 characters',
            'name.unique' => 'A room with this name already exists',
            'description.string' => 'Description must be a valid text',
            'description.max' => 'Description cannot exceed 500 characters',
            'capacity.required' => 'Room capacity is required',
            'capacity.integer' => 'Capacity must be a valid number',
            'capacity.min' => 'Capacity must be at least 1 student',
            'capacity.max' => 'Capacity cannot exceed 500 students',
            'is_lab.required' => 'Room type selection is required',
            'is_lab.boolean' => 'Room type must be either classroom or laboratory',
            'has_equipment.boolean' => 'Equipment availability must be yes or no',
        ];
    }
}
