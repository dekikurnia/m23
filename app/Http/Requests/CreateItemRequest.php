<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateItemRequest extends FormRequest
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
            'provider_id'   => 'required',
            'nama'          => 'required',
            'category_id'   => 'required',
            'stok_toko'   => 'required',
            'stok_gudang'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'provider_id.required'   => 'Provider wajib diisi.',
            'nama.required'          => 'Nama barang wajib diisi.',
            'category_id.required'   => 'Kategori wajib diisi.',
            'stok_toko.required'     => 'Stok toko wajib diisi.',
            'stok_gudang.required'   => 'Stok gudang wajib diisi.',
        ];
    }
}
