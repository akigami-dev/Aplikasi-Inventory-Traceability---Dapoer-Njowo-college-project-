<?php

use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    // Route::redirect('settings', '/settings/profile');
    Route::redirect('alksjf', '/setting/profile');

    Route::get('setting/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('setting/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('setting/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('setting/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('setting/password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('setting/appearance', function () {
        return Inertia::render('setting/Appearance');
    })->name('appearance');
});
