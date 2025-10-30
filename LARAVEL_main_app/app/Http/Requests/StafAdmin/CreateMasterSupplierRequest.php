<?php

namespace App\Http\Requests\StafAdmin;

use Illuminate\Foundation\Http\FormRequest;

class CreateMasterSupplierRequest extends FormRequest
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
            'nama_supplier' => 'required|string|unique:suppliers,nama_supplier,NULL,id,is_archived,false',
            'email' => 'required|string|email|unique:suppliers,email,NULL,id,is_archived,false',
            'no_telp' => 'required|string|unique:suppliers,no_telpon,NULL,id,is_archived,false',
            'alamat' => 'required|string',
        ];
    }
}
