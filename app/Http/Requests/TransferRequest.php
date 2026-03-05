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
            'identifier' => 'required', // email or phone
            'amount' => 'required|integer|min:1',
            'pin' => 'required|string|size:6',
        ];
    }

    public function messages()
    {
        return [
            'identifier.required' => 'Penerima tidak boleh kosong.',
            'amount.required' => 'Nominal tidak boleh kosong.',
            'amount.integer' => 'Nominal harus berupa angka.',
            'amount.min' => 'Nominal harus minimal 1.',
            'pin.required' => 'PIN tidak boleh kosong.',
            'pin.size' => 'PIN harus 6 digit.',
        ];
    }
}
?>
