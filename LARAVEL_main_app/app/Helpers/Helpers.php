<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

if(!function_exists('toast')){
    enum Severity: string {
        case Success = 'success';
        case Info    = 'info';
        case Warning = 'warning';
        case Error   = 'error';
    }
    function toast(Severity $severity, $summary, $detail, $life = 5000) {
        return [
            'severity' => $severity,
            'summary' => $summary,
            'detail' => $detail,
            'life' => $life,
        ];
    }
}

if (!function_exists('capitalized')) {
    // Capitalize first letter
    function capitalized($string) {
        return ucwords(strtolower($string));
    }
}

if (!function_exists('uppercase')) {
    // All uppercase
    function uppercase($string) {
        return strtoupper($string);
    }
}

if (!function_exists('lowercase')) {
    // All lowercase
    function lowercase($string) {
        return strtolower($string);
    }
}

if (!function_exists('titlecase')) {
    // Capitalize first letter of each word
    function titlecase($string) {
        return ucwords(strtolower($string));
    }
}

if(!function_exists('deleteImage')) {
    function deleteImage($folder, $filename) {
        if(Storage::disk('public')->exists($folder. "/". $filename))
        {
            Storage::disk('public')->delete($folder. "/". $filename);
        }
    }
}

if(!function_exists('imageURL')) {
    function imageURL($folder, $filename) {
        if(Storage::disk('public')->exists($folder. "/". $filename) && isset($filename))
        {
            return Storage::url($folder. "/". $filename);
        }
    }
}

if(!function_exists('parseDate')) {
    function parseDate($date, $format = 'Y-m-d H:i:s', $addDays = 0, $addMonths = 0, $addYears = 0) {
        return Carbon::parse($date)->addDays($addDays)->addMonths($addMonths)->addYears($addYears)->format($format);
    }
}

if(!function_exists('getStorageImage')) {
    function getStorageImage($folder, $filename) {
        $imgPath = $folder. "/". $filename;
        $imageContent = Storage::disk('public')->get($imgPath);
        return $imageContent;
    }
}

if(!function_exists('removeImage')) {
    function removeImage($folder, $filename) {
        $imgPath = $folder. "/". $filename;
        if(!Storage::disk('public')->exists($imgPath)) return false;
        Storage::disk('public')->delete($imgPath);
        return true;
    }
}

if(!function_exists('createLabel')) {
    function createLabel($imageContent, $filename,$nama_produk, $tanggal_kadaluarsa, $tanggal_produksi, $nomor_batch, $bahanBaku = null){
        $tanggal_kadaluarsa = Carbon::parse($tanggal_kadaluarsa)->format('d M Y');
        $tanggal_produksi = Carbon::parse($tanggal_produksi)->format('d M Y');

        $bahanText = '';
        if($bahanBaku){
            foreach($bahanBaku as $index => $p){
                $bahanText .= $index+1 . '. ' .$p['nama_bahan'] . ': ' . cleanDecimal($p['jumlah']) . ' ' . $p['satuan'] . ' [' . $p['nama_supplier'] ."]\n";
            }
        }

        $traceUrl = config('constants.app_url_for_trace');

        // $text = "Nama Produk: {$nama_produk}\nTanggal Produksi: {$tanggal_produksi}\nKadaluarsa: {$tanggal_kadaluarsa}\nNomor Batch: {$nomor_batch}\nBahan Baku:\n{$bahanText}\nTrace: {$traceUrl}/trace/{$nomor_batch}\n\nDiproduksi oleh DAPOER NJOWO";
        $text = "Nomor Batch: {$nomor_batch}\nTrace: {$traceUrl}/trace/{$nomor_batch}\nDiproduksi oleh DAPOER NJOWO";

        $response = Http::attach(
            'image', 
            $imageContent,
            $filename
        )->post('http://localhost:8080/generate_qr_code', [
            'text' => $text
        ]);
        return $response;
    }
}

if(!function_exists('formatNumber')){
    function formatNumber($number)
    {
        return rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.');
    }
}
if(!function_exists('cleanDecimal')){
    function cleanDecimal($number)
    {
        return strval(floatval($number));
    }
}
if(!function_exists('formatDecimal')){
    function formatDecimal($number, $minFractionDigits = 0, $maxFractionDigits = 2)
    {
        if (!is_numeric($number)) {
            return $number;
        }
        
        // Format with maximum decimal places
        $formatted = number_format($number, $maxFractionDigits, '.', '');
        
        // Remove trailing zeros
        $formatted = rtrim($formatted, '0');
        $formatted = rtrim($formatted, '.');
        
        // Check if we need to add zeros to meet minimum fraction digits
        $parts = explode('.', $formatted);
        $integerPart = $parts[0];
        $fractionPart = isset($parts[1]) ? $parts[1] : '';
        
        // Pad fraction part to meet minimum digits
        if ($minFractionDigits > 0) {
            $fractionPart = str_pad($fractionPart, $minFractionDigits, '0', STR_PAD_RIGHT);
            return $integerPart . '.' . $fractionPart;
        }
        
        // If no minimum required and no fraction part, return integer
        if (empty($fractionPart)) {
            return $integerPart;
        }
        
        return $integerPart . '.' . $fractionPart;
    }
}