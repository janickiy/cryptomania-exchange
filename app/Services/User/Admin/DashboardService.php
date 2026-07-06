<?php

namespace App\Services\User\Admin;

use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Interfaces\UserInterface;

class DashboardService
{
    /**
     * Purpose: initializes the dashboard service.
     *
     * Action: receives repositories required to prepare dashboard metrics and market overview data.
     */
    public function __construct(
        private readonly StockPairInterface $stockPairs,
        private readonly StockItemInterface $stockItems,
        private readonly UserInterface $users,
    ) {
    }

    /**
     * Purpose: prepares data for the admin dashboard.
     *
     * Action: loads system usage, active trading pairs, and summary counters for the dashboard view.
     *
     * @return array<string, mixed>
     */
    public function dashboardData(): array
    {
        $stockPairs = $this->stockPairs->getAllStockPairDetailByConditions(['stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE]);

        return [
            'title' => __('Dashboard'),
            'cpuUsages' => $this->getCpuUsages(),
            'stockPairs' => $stockPairs,
            'totalStockItem' => $this->stockItems->getCountByConditions(['is_active' => ACTIVE_STATUS_ACTIVE]),
            'totalUser' => $this->users->getCountByConditions(['is_active' => ACTIVE_STATUS_ACTIVE]),
        ];
    }

    /**
     * Purpose: reads memory usage for the dashboard resource widget.
     *
     * Action: parses the Linux free command output and returns a safe percentage value.
     */
    public function getCpuUsages(): float
    {
        $free = trim((string) shell_exec('free'));
        $rows = preg_split('/\R/', $free) ?: [];

        if (!isset($rows[1])) {
            return 0.0;
        }

        $memory = preg_split('/\s+/', trim($rows[1])) ?: [];
        $total = (float) ($memory[1] ?? 0);
        $used = (float) ($memory[2] ?? 0);

        if ($total <= 0) {
            return 0.0;
        }

        return round($used / $total * 100, 2);
    }
}
