<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Services\User\Admin\DashboardService;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Purpose: initializes the DashboardController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly DashboardService $dashboardService)
    {
    }

    /**
     * Purpose: displays the administrator dashboard.
     *
     * Action: delegates dashboard data preparation to the service layer and returns the dashboard view.
     *
     */
    public function __invoke(): View
    {
        return view('backend.dashboard.superadmin', $this->dashboardService->dashboardData());
    }
}
