<?php

namespace App\Http\Requests\StafAdmin;

use Illuminate\Foundation\Http\FormRequest;

class RecallProdukRequest extends FormRequest
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
            'distribusi_id' => ['required', 'integer', 'exists:distribusis,id'],
            'tanggal_recall' => ['required', 'date'],
            'jumlah_recall' => ['required', 'integer', 'min:1'],
            'alasan_recall' => ['required', 'string'],
            'keterangan' => ['required', 'string'],
        ];
    }
}
