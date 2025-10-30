<?php

namespace App\Http\Requests\StafProduksi;

use Illuminate\Foundation\Http\FormRequest;

class SelesaiProduksiRequest extends FormRequest
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
            'produksi_id' => 'required|integer|exists:produksis,id',
            'tanggal_kadaluarsa' => 'required|date',
            'selesai_produksi' => 'required|date',
        ];
    }
}
