<?php

use App\Http\Controllers\TraceController;
use Illuminate\Support\Facades\Route;

// AUTH
Route::middleware(['auth'])->group(function () {

    // only OWNER and STAF ADMIN
    Route::middleware(['role:owner,staf admin'])->group(function () {
        Route::get('traceability', [TraceController::class, 'authIndex'])->name('auth.trace');
        Route::post('traceability/pdf_product', [TraceController::class, 'pdf_product'])->name('auth.trace.pdf_product');
    });
    
});