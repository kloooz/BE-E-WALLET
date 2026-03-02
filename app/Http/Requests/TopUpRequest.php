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
            'amount' => 'required|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'Nominal tidak boleh kosong.',
            'amount.integer' => 'Nominal harus berupa angka.',
            'amount.min' => 'Nominal harus minimal 1.',
        ];
    }
}
?>
