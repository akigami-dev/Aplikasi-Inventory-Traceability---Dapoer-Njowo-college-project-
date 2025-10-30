<?php

$avatarPath = env('STORAGE_AVATAR_PATH', 'images/avatars');
$productImagePath = env('STORAGE_PRODUCT_IMAGE_PATH', 'images/products');
$sertifikasiImagePath = env('STORAGE_SERTIFIKASI_IMAGE_PATH', 'images/sertifikasi');
$produksiImagePath = env('STORAGE_PRODUKSI_IMAGE_PATH', 'images/produksi');
$appUrlForTrace = env('APP_URL_FOR_TRACE', 'http://localhost:8000');
$role = [
    'staf admin' => 'staf admin',
    'staf produksi' => 'staf produksi',
    'owner' => 'owner',
];

return [
    "storage_avatar_path" => $avatarPath,
    "storage_product_image_path" => $productImagePath,
    "storage_sertifikasi_image_path" => $sertifikasiImagePath,
    "storage_produksi_image_path" => $produksiImagePath,
    "role" => $role,
    "app_url_for_trace" => $appUrlForTrace
];