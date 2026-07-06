<?php

namespace App\Services\User\Admin;

use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Services\Core\DataListService;
use Illuminate\Database\Eloquent\Collection;

class TransactionReportService
{
    /**
     * Purpose: initializes dependencies for admin transaction reports.
     *
     * Action: receives the transaction repository and data-list helper used to build report screens.
     */
    public function __construct(
        private readonly TransactionInterface $transactions,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: prepares transaction report data for all users.
     *
     * Action: builds the paginated list, summary totals, and page metadata for the admin view.
     */
    public function allUsersData(?string $journalType = null): array
    {
        return $this->reportData(null, $journalType);
    }

    /**
     * Purpose: prepares transaction report data for a specific user.
     *
     * Action: scopes the report by user id while keeping summary and filter data consistent.
     */
    public function userData(int|string $userId, ?string $journalType = null): array
    {
        return [
            ...$this->reportData($userId, $journalType),
            'userId' => $userId,
        ];
    }

    /**
     * Purpose: builds the complete transaction report payload.
     *
     * Action: combines transaction rows, summary rows, and view metadata into one response array.
     */
    private function reportData(int|string|null $userId = null, ?string $journalType = null): array
    {
        $searchFields = $this->searchFields();
        $orderFields = $this->orderFields();
        $where = $this->where($userId, $journalType);
        $select = ['transactions.*', 'first_name', 'last_name', 'email', 'item'];
        $joinArray = $this->joinArray();
        $listRepository = clone $this->transactions;
        $summaryRepository = clone $this->transactions;
        $query = $listRepository->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);

        return [
            'summary' => $this->summary($summaryRepository, $searchFields, $orderFields, $where, $joinArray),
            'list' => $this->dataListService->dataList($query, $searchFields, $orderFields),
            'title' => __('Transaction'),
            'journalType' => $journalType,
        ];
    }

    /**
     * Purpose: defines searchable columns for transaction reports.
     *
     * Action: keeps the filter field list in one place for list and summary queries.
     */
    private function searchFields(): array
    {
        return [
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['item', __('Stock Item')],
        ];
    }

    /**
     * Purpose: defines sortable columns for transaction reports.
     *
     * Action: keeps ordering options consistent for report tables.
     */
    private function orderFields(): array
    {
        return [
            ['amount', __('Amount')],
            ['transactions.created_at', __('Date')],
        ];
    }

    /**
     * Purpose: builds transaction report query conditions.
     *
     * Action: applies optional user and journal filters while allowing all-user reports.
     */
    private function where(int|string|null $userId, ?string $journalType): ?array
    {
        $where = [];

        if (!is_null($userId)) {
            $where['transactions.user_id'] = $userId;
        }

        if (!is_null($journalType)) {
            $where['journal'] = config('commonconfig.journal_type.' . $journalType);
        }

        return empty($where) ? null : $where;
    }

    /**
     * Purpose: defines joins required by transaction reports.
     *
     * Action: attaches stock item, user, and profile data to transaction rows.
     */
    private function joinArray(): array
    {
        return [
            ['stock_items', 'stock_items.id', '=', 'transactions.stock_item_id'],
            ['users', 'users.id', '=', 'transactions.user_id'],
            ['user_infos', 'users.id', '=', 'user_infos.user_id'],
        ];
    }

    /**
     * Purpose: builds grouped transaction totals.
     *
     * Action: summarizes transaction amounts by stock item and journal for the report footer section.
     */
    private function summary(TransactionInterface $transactions, array $searchFields, array $orderFields, ?array $where, array $joinArray): Collection
    {
        return $transactions->summary($searchFields, $orderFields, $where, $joinArray);
    }
}
