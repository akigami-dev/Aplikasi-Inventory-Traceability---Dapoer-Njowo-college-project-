<?php

// app/Services/ProductService.php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ForgotPasswordService
{
    public function updatePassword($request)
    {
        try {
            $user = User::where('email', $request['email'])->first();
            $user->password = Hash::make($request['password']);
            $user->save();
            return $user;
        } catch (\Throwable $th) {
            dd($th);
            return false;
        }
    }
}