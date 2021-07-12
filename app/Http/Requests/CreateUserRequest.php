<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'usernama' => 'required',
            'name' => 'required',
            'email'  => 'required',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'username.required'      => 'Username wajib diisi.',
            'name.required'          => 'Nama pengguna wajib diisi.',
            'email.required'          => 'Email pengguna wajib diisi.',
            'password.required'          => 'Kata sandi wajib diisi.',
            'password.same'          => 'Kata sandi tidak sama dengan konfirmasi kata sandi.',
            'roles.required'            => 'Role/Peran wajib diisi.',
        ];
    }
}
