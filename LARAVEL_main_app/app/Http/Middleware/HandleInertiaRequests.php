<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {   
        $user = null;
        $role = null;
        if(Auth::user()){
            $user = new UserResource(Auth::user());
            $role = $user->role->nama_role;
            
            $user = $user->resolve();
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'testing' => "YAHOOO",
            'sidebarItems' => $role ? config("sidebar.{$role}") : config("sidebar.empty"),

            // DEFAULT LARAVEL SHARED DATA
            // 
            // 'quote' => ['message' => trim($message), 'author' => trim($author)],
            // 'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'ziggy' => [
                // ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'toast' => fn () => $request->session()->get('toast'),
        ];
    }
}
