<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Services\User\Admin\DashboardService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class DashboardController extends Controller
{
    /**
     * Purpose: initializes the DashboardController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly StockPairInterface $stockPairs,
        private readonly StockItemInterface $stockItems,
        private readonly UserInterface $users,
    ) {
    }

    /**
     * Purpose: handles calling the DashboardController instance as a single action.
     *
     * Action: is used by a route or caller when the class has one primary handler.
     *
     */
    public function __invoke(): View|Factory|Application
    {
        $data['title'] = __('Dashboard');

        $data['cpuUsages'] = $this->dashboardService->getCpuUsages();
        $data['stockPairs'] = $this->stockPairs->getAllStockPairDetailByConditions(['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE]);
        $data['totalStockItem'] = $this->stockItems->getCountByConditions(['is_active'=>ACTIVE_STATUS_ACTIVE]);
        $data['totalUser'] = $this->users->getCountByConditions(['is_active'=>ACTIVE_STATUS_ACTIVE]);

        return view('backend.dashboard.superadmin', $data);
    }
}
