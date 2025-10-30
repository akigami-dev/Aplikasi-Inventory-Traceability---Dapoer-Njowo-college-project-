<?php

namespace App\Http\Requests\StafAdmin;

use Illuminate\Foundation\Http\FormRequest;

class CreateRestokBahanBakuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bahan_baku_id' => 'required|integer|exists:master_bahan_bakus,id',
            'jumlah_restok' => 'required|numeric',
            'tanggal_restok' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date',
            'satuan' => 'required|string',
        ];
    }
}
