<?php

namespace App\Http\Requests\StafAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMasterSertifikasiRequest extends FormRequest
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
            'nama_sertifikasi' => 'required|string',
            'badan_penerbit' => 'required|string',
            'kode_sertifikasi' => 'required|string|min:10|max:25|'. Rule::unique('sertifikasis', 'kode_sertifikasi')->whereNull('deleted_at')->ignore($this->sertifikasi),
            'logo' => 'nullable|image|max:2048',
        ];
    }
}
