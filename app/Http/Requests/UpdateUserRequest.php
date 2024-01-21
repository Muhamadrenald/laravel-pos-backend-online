<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // return [
        //     'name' => 'required|max:100|min:3',
        //     'email' => 'required|email',
        //     'phone' => 'required',
        //     // 'roles' => 'required|in:admin,staff,user',
        //     'roles' => 'required',
        // ];
        return [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'nullable|min:8', // Validasi password baru
            'new_confirm_password' => 'nullable|min:8|same:password', // Validasi konfirmasi password baru
            'phone' => 'nullable|numeric',
            'roles' => 'required|in:admin,staff,user',
        ];
    }

    public function messages()
    {
        return [
            'password.min' => 'The password must be at least 8 characters.',
            'new_confirm_password.same' => 'The new password confirmation must match the entered password.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'new_confirm_password.required' => 'The new password confirmation field is required.',
            'new_confirm_password.min' => 'The new password confirmation must be at least 8 characters.',
        ];
    }
}
