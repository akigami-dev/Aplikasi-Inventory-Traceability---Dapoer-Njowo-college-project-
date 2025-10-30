<?php

namespace App\Http\Requests\StafProduksi;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProduksiRequest extends FormRequest
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
            'produk_id.id' => 'required|integer|exists:master_produks,id',
            'bahan_baku' => 'required|array',
            'bahan_baku.*.jumlah_pakai' => 'required|numeric|min:1',
            'bahan_baku.*.lot_bahan_baku.id' => 'required|integer|distinct',
            'jumlah_produksi' => 'required|integer',
            'lokasi_penyimpanan_id' => ['required', 'integer', 'exists:lokasi_penyimpanans,id'],
            'suhu_penyimpanan' => ['required', 'decimal:0,2', 'between:-1000,1000'],
        ];
    }

    public function formattedData()
    {
        $validated = $this->validated();

        // Transform bahan_baku
        $bahan_baku = collect($validated['bahan_baku'])->map(function ($item) {
            return [
                'jumlah_pakai' => $item['jumlah_pakai'],
                'lot_bahan_baku_id' => (int) $item['lot_bahan_baku']['id'],
            ];
        })->toArray();

        // Build the final data structure
        return [
            'produk_id' => (int) $validated['produk_id']['id'],
            'bahan_baku' => $bahan_baku,
            'kuantitas' => (int) $validated['jumlah_produksi'],
            'lokasi_penyimpanan_id' => (int) $validated['lokasi_penyimpanan_id'],
            'suhu_penyimpanan' => (float) $validated['suhu_penyimpanan'],
        ];
    }
}
