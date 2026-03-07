<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TopUpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'required|integer|min:1|max:50000000',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Nominal tidak boleh kosong.',
            'amount.integer' => 'Nominal harus berupa angka.',
            'amount.min'     => 'Nominal tidak boleh negatif atau nol.',
            'amount.max'     => 'Nominal melebihi batas maksimum transaksi.',
        ];
    }
}
?>
