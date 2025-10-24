<?php

namespace App\Http\Requests;

use App\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('user_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        return [
            'name'     => [
                'required'],
            'email'    => [
                'required',
                'email',
                'regex:/^[\w\.-]+@[\w\.-]+\.edu\.ph$/i',
                'unique:users,email'],
            'password' => [
                'required',
                'min:8',
                'confirmed'],
            'roles.*'  => [
                'integer'],
            'roles'    => [
                'required',
                'array'],
        ];
    }

    public function messages()
    {
        return [
            'email.regex' => 'Email must be a valid institutional email address ending with .edu.ph (e.g., user@school.edu.ph)',
        ];
    }
}
