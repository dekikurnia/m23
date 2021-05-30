<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreatePurchaseRequest extends FormRequest
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
            'tanggal'          => 'required',
            'supplier_id'   => 'required',
            'cara_bayar'   => 'required',
            'tanggal_lunas'   => 'required',
        ];
    }

    public function messages()
    {
        return [
            'tanggal.required'       => 'Tanggal pembelian wajib diisi.',
            'supplier_id.required'   => 'Supplier wajib diisi.',
            'cara_bayar.required'   => 'Cara bayar wajib diisi.',
            'tanggal_lunas.required'   => 'Tanggal pelunasan wajib diisi.',
        ];
    }
}
