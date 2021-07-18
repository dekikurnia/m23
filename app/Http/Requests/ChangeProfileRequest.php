<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ChangeProfileRequest extends FormRequest
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
            'username'   => 'required',
            'name'       => 'required',
        ];
    }

    public function messages()
    {
        return [
            'username.required'   => 'Username wajib diisi.',
            'name.required'       => 'Nama pengguna baru wajib diisi.',
        ];
    }
}
