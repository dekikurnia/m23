<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => ['required', Rule::unique('users', 'username')->ignore($this->user)],
            'name' => 'required',
            'password' => 'required|same:confirm_password',
            'roles' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'username.required'      => 'Username wajib diisi.',
            'username.unique'      => 'Username sudah digunakan.',
            'name.required'          => 'Nama pengguna wajib diisi.',
            //'email.required'          => 'Email pengguna wajib diisi.',
            //'email.unique'          => 'Email sudah digunakan.',
            //'email.email'          => 'Email tidak valid.',
            'password.required'          => 'Kata sandi wajib diisi.',
            'password.same'          => 'Kata sandi tidak sama dengan konfirmasi kata sandi.',
            'roles.required'            => 'Role wajib diisi.',
        ];
    }
}
