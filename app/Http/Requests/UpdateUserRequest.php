<?php

namespace App\Http\Requests;

use App\User;
use Gate;
use Illuminate\Foundation\Http\FormRequest;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        abort_if(Gate::denies('user_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return true;
    }

    public function rules()
    {
        $user = request()->route('user');
        
        // Build email validation rules
        $emailRules = [
            'required',
            'email',
            'unique:users,email,' . $user->id,
        ];
        
        // Only enforce .edu.ph format if email is being changed
        if ($this->input('email') !== $user->email) {
            $emailRules[] = 'regex:/^[\w\.-]+@[\w\.-]+\.edu\.ph$/i';
        }
        
        return [
            'name'    => [
                'required'],
            'email'   => $emailRules,
            'password' => [
                'nullable',
                'min:8',
                'confirmed',
                function ($attribute, $value, $fail) use ($user) {
                    if (!empty($value) && password_verify($value, $user->password)) {
                        $fail('The new password must be different from your current password.');
                    }
                }],
            'roles.*' => [
                'integer'],
            'roles'   => [
                'required',
                'array'],
        ];
    }

    public function messages()
    {
        return [
            'email.regex' => 'New email must be a valid institutional email address ending with .edu.ph (e.g., user@school.edu.ph)',
        ];
    }
}
