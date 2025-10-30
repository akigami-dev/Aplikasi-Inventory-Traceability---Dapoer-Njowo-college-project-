<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserProfileController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        return Inertia::render('settings/profile', [
            'title' => "Profile Page",
        ]);
    }

    public function update(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'id' => 'required|integer',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users')->ignore($request->id)
            ],
            'no_telpon' => 'required|string|min:12|max:17',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $storageAvatarPath = config('constants.storage_avatar_path');

        $user = User::findOrFail($validated['id']);

        // HANDLE IMAGE
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');

            $filename = time() . "_{$user->id}_avatar." . $avatar->getClientOriginalExtension();

            // Save to storage/app/public/images/avatars/
            $path = $avatar->storeAs($storageAvatarPath, $filename, 'public');

            // Delete old avatar in Storage
            if($user->avatar !== "default.png"){
                Storage::disk('public')->delete("{$storageAvatarPath}/{$user->avatar}");
            }

            // Save path in the user table
            $user->avatar = $filename;
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_telpon = $request->no_telpon;
        $user->save();

        return to_route('settings.profile');
    }
}
