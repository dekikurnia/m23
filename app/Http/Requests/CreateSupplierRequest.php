<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateSupplierRequest extends FormRequest
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
            'kode'          => Rule::unique('suppliers', 'kode')->ignore($this->supplier),
            'nama'          => 'required',
            'telepon'       => 'max:13'
        ];
    }

    public function messages()
    {
        return [
            'kode.unique'            => 'Kode supplier sudah digunakan',
            'nama.required'          => 'Nama supplier wajib diisi.',
            'telepon.max'            => 'Telepon maksimal 13 digit.',
        ];
    }
}
