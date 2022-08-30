<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {
        $user = Auth::user();

        return inertia('Dashboard', [
            'permissions' => $user?->getPermissionsViaRoles()?->pluck('name')?->toArray(),
            'roles'       => $user?->getRoleNames()?->toArray()
        ]);
    }
}
