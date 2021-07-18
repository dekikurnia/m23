<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Rules\MatchOldPassword;

class ChangePasswordRequest extends FormRequest
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
            'current_password'   => ['required', new MatchOldPassword],
            'new_password'       => 'required|same:new_confirm_password',
        ];
    }

    public function messages()
    {
        return [
            'current_password.required'   => 'Password lama wajib diisi.',
            'new_password.required'       => 'Password baru wajib diisi.',
            'new_password.same'          => 'Kata sandi baru tidak sama dengan konfirmasi kata sandi baru.',
        ];
    }
}
