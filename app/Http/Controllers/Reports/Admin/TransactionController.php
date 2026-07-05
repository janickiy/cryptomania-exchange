<?php

namespace App\Http\Controllers\Reports\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Services\Core\DataListService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела отчетов по транзакциям.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly TransactionInterface $transactions,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает отчет по транзакциям пользователя.
     *
     * Действие: формирует данные журнала для выбранного пользователя и возвращает отчетное представление.
     */
    public function user(int|string $userId, ?string $journalType = null): View|Factory|Application
    {

        $data = $this->generateTransaction($userId, $journalType);
        $data['title'] = __('Transaction');
        $data['journalType'] = $journalType;
        $data['userId'] = $userId;

        return view('backend.transactions.all_users', $data);
    }

    /**
     * Назначение: формирует данные отчета по транзакциям.
     *
     * Действие: собирает фильтры, выбирает операции из репозитория и возвращает массив данных для представления.
     */
    private function generateTransaction(int|string|null $userId = null, ?string $journalType = null): array
    {
        $searchFields = [
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['item', __('Stock Item')],
        ];


        $orderFields = [
            ['amount', __('Amount')],
            ['transactions.created_at', __('Date')],
        ];


        $where = null;

        if (!is_null($userId)) {
            $where['transactions.user_id'] = $userId;
        }

        if (!is_null($journalType)) {
            $where['journal'] = config('commonconfig.journal_type.' . $journalType);
        }

        $select = ['transactions.*', 'first_name', 'last_name', 'email', 'item'];
        $joinArray = [
            ['stock_items', 'stock_items.id', '=', 'transactions.stock_item_id'],
            ['users', 'users.id', '=', 'transactions.user_id'],
            ['user_infos', 'users.id', '=', 'user_infos.user_id'],
        ];

        $query = $this->transactions->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);
        $select = ['stock_items.item', 'journal', DB::raw('sum(amount) as amount')];
        $data['summary'] = $this->transactions->filters($searchFields, $orderFields, $where, $select, $joinArray, ['stock_items.item', 'journal']);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        return $data;
    }

    /**
     * Назначение: показывает отчет по транзакциям всех пользователей.
     *
     * Действие: формирует общий журнал транзакций с учетом типа операции и возвращает отчет.
     */
    public function allUser(?string $journalType = null): View|Factory|Application
    {

        $data = $this->generateTransaction(null, $journalType);
        $data['title'] = __('Transaction');
        $data['journalType'] = $journalType;
        return view('backend.transactions.all_users', $data);
    }
}
