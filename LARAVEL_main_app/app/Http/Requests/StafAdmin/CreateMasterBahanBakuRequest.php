<?php

namespace App\Http\Requests\StafAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateMasterBahanBakuRequest extends FormRequest
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
        'nama_bahan' => [
            'required',
            'string',
            Rule::unique('master_bahan_bakus', 'nama_bahan')
                ->where('supplier_id', $this->input('supplier'))
                ->where('is_archived', false),
        ],
        'supplier' => 'required|integer|exists:suppliers,id',
    ];
    }
}
