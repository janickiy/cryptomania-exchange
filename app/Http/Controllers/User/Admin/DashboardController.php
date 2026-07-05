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
     * Назначение: инициализирует контроллер раздела административной панели.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly DashboardService $dashboardService,
        private readonly StockPairInterface $stockPairs,
        private readonly StockItemInterface $stockItems,
        private readonly UserInterface $users,
    ) {
    }

    /**
     * Назначение: обрабатывает основной маршрут раздела административной панели.
     *
     * Действие: подготавливает данные страницы и возвращает целевое представление.
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
