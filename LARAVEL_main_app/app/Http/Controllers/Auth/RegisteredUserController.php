<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Severity;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register', [
            'title' => 'Sign Up',
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|'. Rule::unique('users', 'email_unique'),
            'no_telpon' => 'required|string|min:14|max:17',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,nama_role', 'lowercase'],
        ]);

        $id = Role::where('nama_role', $request->role)->firstOrFail()->id;
        if(!$id) return back()->with('toast', toast(Severity::Error, 'Register', 'Register Failed'));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_telpon' => $request->no_telpon,
            'password' => Hash::make($request->password),
            'role_id' => $id
        ]);

        event(new Registered($user));

        // Auth::login($user);

        return back()->with('toast', toast(Severity::Success, 'Register', 'Register Success'));
    }
}
