<?php

namespace App\Http\Requests\StafAdmin;

use Illuminate\Foundation\Http\FormRequest;

class ReturProdukUpdateRequest extends FormRequest
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
            'distribusi_id' => 'required|integer|exists:distribusis,id',
            'tanggal_retur' => 'required|date',
            'jumlah_retur' => 'required|integer',
            'tindakan' => 'required|string',
            'keterangan' => 'required|string',
            'catatan_tambahan' => 'nullable|string'
        ];
    }
}
