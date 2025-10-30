<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\settings\UserPasswordController;
use App\Http\Controllers\Settings\UserProfileController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\TraceController;
use Illuminate\Support\Facades\Route;

Route::get("trace", [TraceController::class, "index"])->name("trace");
Route::get("trace/{trace}", [TraceController::class, "guestIndex"])->name("trace.guest");

Route::middleware(['auth'])->group(function () {
    Route::redirect('/', 'dashboard')->name('home');
    Route::redirect('settings', '/settings/profile')->name('settings');
    Route::get('settings/profile', [UserProfileController::class, 'index'])->name('settings.profile');
    Route::get('settings/password', [UserPasswordController::class, 'index'])->name('settings.password');
    Route::post('settings/password/change_password', [UserPasswordController::class, 'store'])->name('settings.password.change_password');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('testing', [TestingController::class, 'index'])->name('testing');

    Route::put('/user/update', [UserProfileController::class, 'update'])->name('users.update');
    
    Route::get('testing', [TestingController::class, 'index'])->name('testing');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/owner.php';
require __DIR__.'/staf_admin.php';
require __DIR__.'/staf_produksi.php';
require __DIR__.'/mixed.php';

