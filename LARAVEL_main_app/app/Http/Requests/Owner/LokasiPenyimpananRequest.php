<?php

namespace App\Http\Requests\Owner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LokasiPenyimpananRequest extends FormRequest
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
            'nama_lokasi_penyimpanan' => ['required', 'string', Rule::unique('lokasi_penyimpanans', 'name')->whereNull('deleted_at')],
            'suhu_default' => ['required', 'decimal:0,2', 'between:-1000,1000'],
            'deskripsi' => ['required', 'string']
        ];
    }
}
