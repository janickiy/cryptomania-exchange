<?php

namespace App\Http\Controllers\Reports\Admin;

use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\User\Admin\ReportsService;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class ReportsController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела отчетов.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly ReportsService $reportsService,
        private readonly WalletInterface $wallets,
    ) {
    }

    /**
     * Назначение: показывает общий отчет по депозитам.
     *
     * Действие: получает список депозитов с учетом типа транзакции и возвращает отчетное представление.
     */
    public function allDeposits(?string $paymentTransactionType = null): View|Factory|Application
    {
        $data['list'] = $this->reportsService->deposits(null, null, $paymentTransactionType);
        $data['title'] = __('Deposits');
        $data['status'] = $paymentTransactionType;

        return view('backend.reports.all_deposit', $data);
    }

    /**
     * Назначение: показывает отчет по депозитам конкретного пользователя.
     *
     * Действие: фильтрует депозиты по пользователю и типу транзакции, затем возвращает отчет.
     */
    public function deposits(int|string $id, ?string $paymentTransactionType = null): View|Factory|Application
    {
        $data['wallet'] = $this->wallets->firstOrFail(['id' => $id], 'stockItem');
        $data['list'] = $this->reportsService->deposits(null, $id, $paymentTransactionType);
        $data['title'] = __('Deposits');
        $data['status'] = $paymentTransactionType;

        return view('backend.reports.deposit', $data);
    }

    /**
     * Назначение: показывает общий отчет по выводам средств.
     *
     * Действие: получает список выводов с учетом типа транзакции и возвращает отчетное представление.
     */
    public function allWithdrawals(?string $paymentTransactionType = null): View|Factory|Application {
        $data['list'] = $this->reportsService->withdrawals(null, null, $paymentTransactionType);
        $data['title'] = __('Withdrawals');
        $data['status'] = $paymentTransactionType;

        return view('backend.reports.all_withdrawal', $data);
    }

    /**
     * Назначение: показывает отчет по выводам конкретного пользователя.
     *
     * Действие: фильтрует выводы по пользователю и типу транзакции, затем возвращает отчет.
     */
    public function withdrawals(int|string $id, ?string $paymentTransactionType = null): View|Factory|Application {
        $data['wallet'] = $this->wallets->firstOrFail(['id' => $id], 'stockItem');
        $data['list'] = $this->reportsService->withdrawals(null, $id, $paymentTransactionType);
        $data['title'] = __('Withdrawals');
        $data['status'] = $paymentTransactionType;

        return view('backend.reports.withdrawal', $data);
    }

    /**
     * Назначение: показывает общий отчет по сделкам.
     *
     * Действие: получает торговые операции с учетом категории и возвращает отчетное представление.
     */
    public function allTrades(?string $categoryType = null): View|Factory|Application {
        $data['list'] = $this->reportsService->trades(null, $categoryType);
        $data['title'] = __('Trades');
        $data['categoryType'] = $categoryType;

        return view('backend.reports.trades', $data);
    }

    /**
     * Назначение: показывает отчет по сделкам.
     *
     * Действие: фильтрует торговые операции по пользователю или категории и возвращает отчет.
     */
    public function trades(int|string $userId, ?string $categoryType = null): View|Factory|Application {
        $data['list'] = $this->reportsService->trades($userId, $categoryType);
        $data['title'] = __('Trades');
        $data['categoryType'] = $categoryType;

        return view('backend.reports.trades', $data);
    }

    /**
     * Назначение: показывает открытые ордера пользователя.
     *
     * Действие: запрашивает текущие открытые ордера и возвращает представление списка.
     */
    public function openOrders(int|string $userId): View|Factory|Application
    {
        $data['list'] = $this->reportsService->openOrders($userId);
        $data['title'] = __('Open Orders');
        $data['hideUser'] = $userId;

        return view('backend.reports.open_orders', $data);
    }

    /**
     * Назначение: показывает отчет по сделкам торговой пары.
     *
     * Действие: фильтрует сделки по торговой паре и категории, затем возвращает отчетное представление.
     */
    public function tradesByStockPairId(int|string $id, ?string $categoryType = null): View|Factory|Application {
        $data['list'] = $this->reportsService->trades(null, $categoryType, $id);
        $data['title'] = __('Trades');
        $data['categoryType'] = $categoryType;

        return view('backend.reports.trades', $data);
    }

    /**
     * Назначение: показывает открытые ордера торговой пары.
     *
     * Действие: фильтрует открытые ордера по торговой паре и возвращает отчетное представление.
     */
    public function openOrdersByStockPairId(int|string $id): View|Factory|Application
    {
        $data['list'] = $this->reportsService->openOrders(null, null, $id);
        $data['title'] = __('Open Orders');
        $data['hideUser'] = false;

        return view('backend.reports.open_orders', $data);
    }
}
