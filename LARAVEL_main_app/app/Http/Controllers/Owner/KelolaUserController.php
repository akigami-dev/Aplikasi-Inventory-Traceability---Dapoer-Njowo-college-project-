<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Severity;
use Illuminate\Validation\Rules;

class KelolaUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $id = Auth::id();
        $user = User::where('id', '!=', $id)->orderBy('role_id', 'asc')->paginate(10);
        $user = UserResource::collection($user);
        return Inertia::render('owner/KelolaUser', [
            'title' => "Kelola User",
            'DataUser' => $user,
        ]);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update_role(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string|exists:roles,nama_role',
        ]);
        try {
            $user->role_id = Role::where('nama_role', $request->role)->firstOrFail()->id;
            $user->save();
            return redirect()->back()->with('toast', toast(Severity::Success, 'Update Role', 'Update Role Successfully'));
        } catch (\Throwable $th) {
            dd($th);
            return redirect()->back()->with('toast', toast(Severity::Error, 'Update Role', 'Update Role Failed'));
        }
    }

    public function create_user(Request $request): RedirectResponse
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

    public function update_user (Request $request, User $user) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|'. Rule::unique('users', 'email_unique')->ignore($user->id),
            'no_telpon' => 'required|string|min:14|max:17',
            'role' => ['required', 'string', 'exists:roles,nama_role', 'lowercase'],
        ]);

        $id = Role::where('nama_role', $request->role)->firstOrFail()->id;
        if(!$id) return back()->with('toast', toast(Severity::Error, 'Update', 'Update Failed'));

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_telpon' => $request->no_telpon,
            'role_id' => $id
        ]);

        return back()->with('toast', toast(Severity::Success, 'Update', 'Update Success'));
    }

    public function delete_user(User $user) {
        $user->delete();
        if($user->deleted_at) return back()->with('toast', toast(Severity::Success, 'Delete', 'Delete Success'));
        return back()->with('toast', toast(Severity::Success, 'Delete', 'Delete Failed'));
    }
}
