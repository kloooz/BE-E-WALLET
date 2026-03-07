<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'identifier' => 'required|string',
            'amount'     => 'required|integer|min:1|max:50000000',
            'pin'        => 'required|string|size:6',
        ];
    }

    public function messages()
    {
        return [
            'identifier.required' => 'Penerima tidak boleh kosong.',
            'identifier.string'   => 'Format penerima tidak valid.',
            'amount.required'     => 'Nominal tidak boleh kosong.',
            'amount.integer'      => 'Nominal harus berupa angka.',
            'amount.min'          => 'Nominal tidak boleh negatif atau nol.',
            'amount.max'          => 'Nominal melebihi batas maksimum transaksi.',
            'pin.required'        => 'PIN tidak boleh kosong.',
            'pin.size'            => 'PIN harus 6 digit.',
        ];
    }
}
?>
