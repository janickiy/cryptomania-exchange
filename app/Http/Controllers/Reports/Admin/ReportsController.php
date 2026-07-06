<?php

namespace App\Http\Controllers\Reports\Admin;

use App\Http\Controllers\Controller;
use App\Services\User\Admin\ReportsService;
use Illuminate\Contracts\View\View;

class ReportsController extends Controller
{
    /**
     * Purpose: initializes the ReportsController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(
        private readonly ReportsService $reportsService,
    ) {
    }

    /**
     * Purpose: displays all user deposit report rows for admins.
     *
     * Action: delegates report data preparation to the service and renders the admin deposits view.
     */
    public function allDeposits(?string $paymentTransactionType = null): View
    {
        return view('backend.reports.all_deposit', $this->reportsService->adminAllDepositsData($paymentTransactionType));
    }

    /**
     * Purpose: displays deposit report rows for a specific wallet.
     *
     * Action: delegates wallet lookup and report data preparation to the service.
     */
    public function deposits(int|string $id, ?string $paymentTransactionType = null): View
    {
        return view('backend.reports.deposit', $this->reportsService->adminDepositsData($id, $paymentTransactionType));
    }

    /**
     * Purpose: displays all user withdrawal report rows for admins.
     *
     * Action: delegates report data preparation to the service and renders the admin withdrawals view.
     */
    public function allWithdrawals(?string $paymentTransactionType = null): View
    {
        return view('backend.reports.all_withdrawal', $this->reportsService->adminAllWithdrawalsData($paymentTransactionType));
    }

    /**
     * Purpose: displays withdrawal report rows for a specific wallet.
     *
     * Action: delegates wallet lookup and report data preparation to the service.
     */
    public function withdrawals(int|string $id, ?string $paymentTransactionType = null): View
    {
        return view('backend.reports.withdrawal', $this->reportsService->adminWithdrawalsData($id, $paymentTransactionType));
    }

    /**
     * Purpose: displays all executed trade report rows for admins.
     *
     * Action: delegates category filtering and list preparation to the report service.
     */
    public function allTrades(?string $categoryType = null): View
    {
        return view('backend.reports.trades', $this->reportsService->adminAllTradesData($categoryType));
    }

    /**
     * Purpose: displays executed trade report rows for a specific user.
     *
     * Action: delegates user and category filtering to the report service.
     */
    public function trades(int|string $userId, ?string $categoryType = null): View
    {
        return view('backend.reports.trades', $this->reportsService->adminTradesData($userId, $categoryType));
    }

    /**
     * Purpose: displays open order report rows for a specific user.
     *
     * Action: delegates pending-order filtering to the report service.
     */
    public function openOrders(int|string $userId): View
    {
        return view('backend.reports.open_orders', $this->reportsService->adminOpenOrdersData($userId));
    }

    /**
     * Purpose: displays executed trade report rows for a specific stock pair.
     *
     * Action: delegates stock-pair and category filtering to the report service.
     */
    public function tradesByStockPairId(int|string $id, ?string $categoryType = null): View
    {
        return view('backend.reports.trades', $this->reportsService->adminStockPairTradesData($id, $categoryType));
    }

    /**
     * Purpose: displays open order report rows for a specific stock pair.
     *
     * Action: delegates stock-pair pending-order filtering to the report service.
     */
    public function openOrdersByStockPairId(int|string $id): View
    {
        return view('backend.reports.open_orders', $this->reportsService->adminStockPairOpenOrdersData($id));
    }
}
