<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Repository\DashboardRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(DashboardRepository $dashboardRepository): \Inertia\Response|\Inertia\ResponseFactory
    {
        $user = Auth::user();

        return inertia('Dashboard', [
            'permissions' => $user?->getPermissionsViaRoles()?->pluck('name')?->toArray(),
            'roles'       => $user?->getRoleNames()?->toArray(),
            'overdue'     => $dashboardRepository->getOverdue(),
            'overdueNext' => $dashboardRepository->getOverdue(7, false),
            'totals'      => $dashboardRepository->getTotals()
        ]);
    }
}
