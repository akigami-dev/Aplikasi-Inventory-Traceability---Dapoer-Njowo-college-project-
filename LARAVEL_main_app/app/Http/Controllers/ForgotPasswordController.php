<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Services\ForgotPasswordService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Severity;

class ForgotPasswordController extends Controller
{
    protected $forgotPasswordService;
    public function __construct(ForgotPasswordService $forgotPasswordService)
    {
        $this->forgotPasswordService = $forgotPasswordService;
    }
    public function index()
    {
        return Inertia::render('auth/ForgotPassword', [
            'title' => 'Forgot Password',
        ]);
    }

    public function check_email(Request $request)
    {
        $validate = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);
        return Inertia::render('auth/ForgotPassword',[
            'title' => 'Reset Password',
            'email' => $validate['email']
        ]);
    }

    public function update_password(ForgotPasswordRequest $request)
    {
        $result = $this->forgotPasswordService->updatePassword($request->validated());
        if(!$result) back()->with('toast', toast(Severity::Error, 'Reset Password', 'Reset Password Failed'));
        return redirect()->route('login')->with('toast', toast(Severity::Success, 'Reset Password', 'Reset Password Success'));
    }
}
