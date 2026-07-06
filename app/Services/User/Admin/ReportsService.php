<?php

namespace App\Services\User\Admin;

use App\Models\User\Wallet;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Trader\Interfaces\DepositInterface;
use App\Repositories\User\Trader\Interfaces\ReferralEarningInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportsService
{
    /**
     * Purpose: initializes report dependencies used by admin and trader report screens.
     *
     * Action: receives repositories and helper services so report queries stay outside controllers.
     */
    public function __construct(
        private readonly DepositInterface $depositRepository,
        private readonly WithdrawalInterface $withdrawalRepository,
        private readonly StockExchangeInterface $stockExchangeRepository,
        private readonly StockOrderInterface $stockOrderRepository,
        private readonly UserInterface $userRepository,
        private readonly ReferralEarningInterface $referralEarningRepository,
        private readonly WalletInterface $walletRepository,
        private readonly UserInfoInterface $userInfoRepository,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: prepares the admin-wide deposits report data.
     *
     * Action: builds a filtered deposits list and metadata for the admin deposits view.
     */
    public function adminAllDepositsData(?string $paymentTransactionType = null): array
    {
        return $this->paymentReportData(
            $this->deposits(null, null, $paymentTransactionType),
            __('Deposits'),
            $paymentTransactionType
        );
    }

    /**
     * Purpose: prepares the admin wallet deposits report data.
     *
     * Action: loads the wallet and deposit rows required by the wallet deposits view.
     */
    public function adminDepositsData(int|string $walletId, ?string $paymentTransactionType = null): array
    {
        return [
            'wallet' => $this->wallet($walletId),
            ...$this->paymentReportData(
                $this->deposits(null, $walletId, $paymentTransactionType),
                __('Deposits'),
                $paymentTransactionType
            ),
        ];
    }

    /**
     * Purpose: prepares the admin-wide withdrawals report data.
     *
     * Action: builds a filtered withdrawals list and metadata for the admin withdrawals view.
     */
    public function adminAllWithdrawalsData(?string $paymentTransactionType = null): array
    {
        return $this->paymentReportData(
            $this->withdrawals(null, null, $paymentTransactionType),
            __('Withdrawals'),
            $paymentTransactionType
        );
    }

    /**
     * Purpose: prepares the admin wallet withdrawals report data.
     *
     * Action: loads the wallet and withdrawal rows required by the wallet withdrawals view.
     */
    public function adminWithdrawalsData(int|string $walletId, ?string $paymentTransactionType = null): array
    {
        return [
            'wallet' => $this->wallet($walletId),
            ...$this->paymentReportData(
                $this->withdrawals(null, $walletId, $paymentTransactionType),
                __('Withdrawals'),
                $paymentTransactionType
            ),
        ];
    }

    /**
     * Purpose: prepares the admin-wide trades report data.
     *
     * Action: builds a filtered trades list and metadata for the admin trades view.
     */
    public function adminAllTradesData(?string $categoryType = null): array
    {
        return $this->tradeReportData($this->trades(null, $categoryType), $categoryType);
    }

    /**
     * Purpose: prepares trades report data for one user.
     *
     * Action: filters trade rows by user and category for the admin trades view.
     */
    public function adminTradesData(int|string $userId, ?string $categoryType = null): array
    {
        return $this->tradeReportData($this->trades($userId, $categoryType), $categoryType);
    }

    /**
     * Purpose: prepares open order data for one user.
     *
     * Action: filters pending orders by user and hides duplicate user columns in the admin view.
     */
    public function adminOpenOrdersData(int|string $userId): array
    {
        return [
            'list' => $this->openOrders($userId),
            'title' => __('Open Orders'),
            'hideUser' => $userId,
        ];
    }

    /**
     * Purpose: prepares trades report data for one stock pair.
     *
     * Action: filters trades by stock pair and category for the admin trades view.
     */
    public function adminStockPairTradesData(int|string $stockPairId, ?string $categoryType = null): array
    {
        return $this->tradeReportData($this->trades(null, $categoryType, $stockPairId), $categoryType);
    }

    /**
     * Purpose: prepares open order data for one stock pair.
     *
     * Action: filters pending orders by stock pair while keeping user columns visible.
     */
    public function adminStockPairOpenOrdersData(int|string $stockPairId): array
    {
        return [
            'list' => $this->openOrders(null, null, $stockPairId),
            'title' => __('Open Orders'),
            'hideUser' => false,
        ];
    }

    /**
     * Purpose: prepares the current trader's all-deposits report data.
     *
     * Action: limits deposits to the authenticated trader and builds view metadata.
     */
    public function traderAllDepositsData(?string $paymentTransactionType = null): array
    {
        return $this->paymentReportData(
            $this->deposits($this->currentUserId(), null, $paymentTransactionType),
            __('Deposits'),
            $paymentTransactionType
        );
    }

    /**
     * Purpose: prepares the current trader's wallet deposits report data.
     *
     * Action: validates wallet ownership and builds the wallet-specific deposits list.
     */
    public function traderDepositsData(int|string $walletId, ?string $paymentTransactionType = null): array
    {
        $userId = $this->currentUserId();

        return [
            'wallet' => $this->wallet($walletId, $userId),
            ...$this->paymentReportData(
                $this->deposits($userId, $walletId, $paymentTransactionType),
                __('Deposits'),
                $paymentTransactionType
            ),
        ];
    }

    /**
     * Purpose: prepares the current trader's all-withdrawals report data.
     *
     * Action: limits withdrawals to the authenticated trader and builds view metadata.
     */
    public function traderAllWithdrawalsData(?string $paymentTransactionType = null): array
    {
        return $this->paymentReportData(
            $this->withdrawals($this->currentUserId(), null, $paymentTransactionType),
            __('Withdrawals'),
            $paymentTransactionType
        );
    }

    /**
     * Purpose: prepares the current trader's wallet withdrawals report data.
     *
     * Action: validates wallet ownership and builds the wallet-specific withdrawals list.
     */
    public function traderWithdrawalsData(int|string $walletId, ?string $paymentTransactionType = null): array
    {
        $userId = $this->currentUserId();

        return [
            'wallet' => $this->wallet($walletId, $userId),
            ...$this->paymentReportData(
                $this->withdrawals($userId, $walletId, $paymentTransactionType),
                __('Withdrawals'),
                $paymentTransactionType
            ),
        ];
    }

    /**
     * Purpose: prepares the current trader's trades report data.
     *
     * Action: filters trades by authenticated user and optional category for the frontend view.
     */
    public function traderTradesData(?string $categoryType = null): array
    {
        return $this->tradeReportData($this->trades($this->currentUserId(), $categoryType), $categoryType);
    }

    /**
     * Purpose: prepares the current trader's referral users report data.
     *
     * Action: loads referred users and title metadata for the frontend referral report.
     */
    public function traderReferralUsersData(): array
    {
        return [
            'list' => $this->referralUsers($this->currentUserId()),
            'title' => __('Trades'),
        ];
    }

    /**
     * Purpose: prepares referral earning data for one referred user.
     *
     * Action: loads earning totals and referred user profile data for the frontend report.
     */
    public function traderReferralEarningData(int|string $referralUserId): array
    {
        return [
            'list' => $this->referralEarning($this->currentUserId(), $referralUserId),
            'referralUserInfo' => $this->userInfoRepository->findOrFailByConditions(['user_id' => $referralUserId]),
            'title' => __('Referral Earning'),
        ];
    }

    /**
     * Purpose: converts an encrypted referral query value into a user identifier.
     *
     * Action: returns null when the query value is missing or cannot be decrypted.
     */
    public function resolveReferralUserId(?string $encryptedReferralId): int|string|null
    {
        if (empty($encryptedReferralId)) {
            return null;
        }

        try {
            $referralUserId = decrypt($encryptedReferralId);
        } catch (\Throwable) {
            return null;
        }

        return is_int($referralUserId) || is_string($referralUserId) ? $referralUserId : null;
    }

    /**
     * Purpose: builds a filtered deposits report list.
     *
     * Action: queries deposit rows with shared report filters, search fields, and ordering rules.
     */
    public function deposits(int|string|null $userId = null, int|string|null $walletId = null, ?string $transactionType = null): array
    {
        $searchFields = $this->paymentSearchFields($walletId, $userId);
        $orderFields = $this->paymentOrderFields($walletId);
        $where = $this->paymentWhere($userId, $walletId, $transactionType);
        $select = ['deposits.*', 'item', 'item_name', 'email'];
        $joinArray = [
            ['stock_items', 'stock_items.id', '=', 'deposits.stock_item_id'],
            ['users', 'users.id', '=', 'deposits.user_id'],
        ];

        $query = $this->depositRepository->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);

        return $this->dataListService->dataList($query, $searchFields, $orderFields);
    }

    /**
     * Purpose: builds a filtered withdrawals report list.
     *
     * Action: queries withdrawal rows with shared report filters, search fields, and ordering rules.
     */
    public function withdrawals(int|string|null $userId = null, int|string|null $walletId = null, ?string $transactionType = null): array
    {
        $searchFields = $this->paymentSearchFields($walletId, $userId);
        $orderFields = $this->paymentOrderFields($walletId);
        $where = $this->paymentWhere($userId, $walletId, $transactionType);
        $select = ['withdrawals.*', 'item', 'item_name', 'email'];
        $joinArray = [
            ['stock_items', 'stock_items.id', '=', 'withdrawals.stock_item_id'],
            ['users', 'users.id', '=', 'withdrawals.user_id'],
        ];

        $query = $this->withdrawalRepository->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);

        return $this->dataListService->dataList($query, $searchFields, $orderFields);
    }

    /**
     * Purpose: builds a filtered trades report list.
     *
     * Action: queries executed exchanges by user, category, and stock pair report filters.
     */
    public function trades(int|string|null $userId = null, ?string $categoryType = null, int|string|null $stockPairId = null): array
    {
        $searchFields = [
            ['stock_exchanges.stock_pair_id', __('Market')],
        ];
        $orderFields = [
            ['stock_exchanges.created_at', __('Date')],
        ];
        $where = $this->tradeWhere($userId, $categoryType, $stockPairId);
        $select = [
            'stock_exchanges.*',
            'stock_orders.category',
            'stock_orders.maker_fee',
            'stock_orders.taker_fee',
            'stock_items.id as stock_item_id',
            'stock_items.item as stock_item_abbr',
            'stock_items.item_name as stock_item_name',
            'stock_items.item_type as stock_item_type',
            'base_items.id as base_item_id',
            'base_items.item as base_item_abbr',
            'base_items.item_name as base_item_name',
            'base_items.item_type as base_item_type',
            'email',
        ];
        $joinArray = [
            ['stock_pairs', 'stock_pairs.id', '=', 'stock_exchanges.stock_pair_id'],
            ['stock_orders', 'stock_orders.id', '=', 'stock_exchanges.stock_order_id'],
            ['stock_items', 'stock_items.id', '=', 'stock_pairs.stock_item_id'],
            ['stock_items as base_items', 'base_items.id', '=', 'stock_pairs.base_item_id'],
            ['users', 'users.id', '=', 'stock_exchanges.user_id'],
        ];

        $query = $this->stockExchangeRepository->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);

        return $this->dataListService->dataList($query, $searchFields, $orderFields);
    }

    /**
     * Purpose: builds a filtered open-orders report list.
     *
     * Action: queries pending stock orders by user, category, and stock pair filters.
     */
    public function openOrders(int|string|null $userId = null, ?string $categoryType = null, int|string|null $stockPairId = null): array
    {
        $searchFields = [
            ['stock_orders.stock_pair_id', __('Market')],
            ['stock_orders.price', __('Price')],
            ['stock_orders.amount', __('Amount')],
        ];

        if (is_null($userId)) {
            $searchFields[] = ['stock_orders.user_id', __('User')];
            $searchFields[] = ['email', __('Email')];
        }

        $orderFields = [
            ['stock_orders.price', __('Price')],
            ['stock_orders.amount', __('Amount')],
            ['stock_orders.created_at', __('Date')],
        ];
        $where = $this->openOrderWhere($userId, $categoryType, $stockPairId);
        $select = [
            'stock_orders.*',
            'stock_items.id as stock_item_id',
            'stock_items.item as stock_item_abbr',
            'stock_items.item_name as stock_item_name',
            'stock_items.item_type as stock_item_type',
            'base_items.id as base_item_id',
            'base_items.item as base_item_abbr',
            'base_items.item_name as base_item_name',
            'base_items.item_type as base_item_type',
            'email',
        ];
        $joinArray = [
            ['stock_pairs', 'stock_pairs.id', '=', 'stock_orders.stock_pair_id'],
            ['stock_items', 'stock_items.id', '=', 'stock_pairs.stock_item_id'],
            ['stock_items as base_items', 'base_items.id', '=', 'stock_pairs.base_item_id'],
            ['users', 'users.id', '=', 'stock_orders.user_id'],
        ];

        $query = $this->stockOrderRepository->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);

        return $this->dataListService->dataList($query, $searchFields, $orderFields);
    }

    /**
     * Purpose: builds the current user's referral list.
     *
     * Action: queries users referred by the provided user id and returns a filterable list.
     */
    public function referralUsers(int|string|null $id): array
    {
        $searchFields = [
            ['user_infos.first_name', __('First Name')],
            ['user_infos.last_name', __('Last Name')],
        ];
        $orderFields = [
            ['user_infos.first_name', __('First Name')],
            ['user_infos.last_name', __('Last Name')],
            ['users.created_at', __('Registration Date')],
        ];
        $where = [
            'users.referrer_id' => $id,
        ];
        $select = [
            'users.id',
            'users.created_at',
            'user_infos.first_name',
            'user_infos.last_name',
        ];
        $joinArray = [
            ['user_infos', 'users.id', '=', 'user_infos.user_id'],
        ];

        $query = $this->userRepository->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);

        return $this->dataListService->dataList($query, $searchFields, $orderFields);
    }

    /**
     * Purpose: builds referral earning totals for one referred user.
     *
     * Action: groups referral earnings by stock item and returns a filterable non-paginated list.
     */
    public function referralEarning(int|string|null $referrerUserId, int|string $referralUserId): array
    {
        $searchFields = [
            ['stock_items.item', __('Stock Item')],
        ];
        $orderFields = [
            ['stock_items.item', __('Stock Item')],
            ['amount', __('Amount')],
        ];
        $where = [
            'referrer_user_id' => $referrerUserId,
            'referral_user_id' => $referralUserId,
        ];
        $select = [
            'stock_items.item',
            'stock_items.item_name',
            'stock_items.item_emoji',
            DB::raw('sum(amount) as amount'),
        ];
        $joinArray = [
            ['stock_items', 'stock_items.id', '=', 'referral_earnings.stock_item_id'],
        ];

        $query = $this->referralEarningRepository->filters(
            $searchFields,
            $orderFields,
            $where,
            $select,
            $joinArray,
            ['stock_items.item', 'stock_items.item_name', 'stock_items.item_emoji']
        );

        return $this->dataListService->dataList($query, $searchFields, $orderFields, false, false);
    }

    /**
     * Purpose: returns the authenticated user identifier for trader report scopes.
     *
     * Action: centralizes Auth access so trader controllers do not depend on the facade.
     */
    private function currentUserId(): int|string|null
    {
        return Auth::id();
    }

    /**
     * Purpose: loads a wallet for admin or trader report pages.
     *
     * Action: applies optional user ownership checks before returning the wallet with stock item data.
     */
    private function wallet(int|string $walletId, int|string|null $userId = null): Wallet
    {
        $conditions = ['id' => $walletId];

        if (!is_null($userId)) {
            $conditions['user_id'] = $userId;
        }

        return $this->walletRepository->firstOrFail($conditions, 'stockItem');
    }

    /**
     * Purpose: builds common data for deposit and withdrawal report views.
     *
     * Action: keeps payment report metadata consistent across admin and trader views.
     */
    private function paymentReportData(array $list, string $title, ?string $status): array
    {
        return [
            'list' => $list,
            'title' => $title,
            'status' => $status,
        ];
    }

    /**
     * Purpose: builds common data for trades report views.
     *
     * Action: keeps trade report metadata consistent across admin and trader views.
     */
    private function tradeReportData(array $list, ?string $categoryType): array
    {
        return [
            'list' => $list,
            'title' => __('Trades'),
            'categoryType' => $categoryType,
        ];
    }

    /**
     * Purpose: defines searchable payment report fields.
     *
     * Action: adds stock-name search only when the report is not scoped to a single wallet.
     */
    private function paymentSearchFields(int|string|null $walletId, int|string|null $userId): array
    {
        $searchFields = [
            ['ref_id', __('Reference ID')],
            ['amount', __('Amount')],
            ['address', __('Address')],
            ['txn_id', __('Transaction ID')],
        ];

        if (is_null($walletId)) {
            $searchFields[] = ['item_name', __('Stock Name')];
        }

        if (is_null($userId)) {
            $searchFields[] = ['email', __('Email')];
        }

        return $searchFields;
    }

    /**
     * Purpose: defines sortable payment report fields.
     *
     * Action: adds stock-name sorting only when the report is not scoped to a single wallet.
     */
    private function paymentOrderFields(int|string|null $walletId): array
    {
        $orderFields = [
            ['created_at', __('Date')],
        ];

        if (is_null($walletId)) {
            $orderFields[] = ['item_name', __('Stock Name')];
        }

        return $orderFields;
    }

    /**
     * Purpose: builds payment report query conditions.
     *
     * Action: combines optional user, wallet, and transaction status filters for deposits and withdrawals.
     */
    private function paymentWhere(int|string|null $userId, int|string|null $walletId, ?string $transactionType): ?array
    {
        $where = [];

        if (!is_null($userId)) {
            $where['user_id'] = $userId;
        }

        if (!is_null($transactionType)) {
            $where['status'] = config('commonconfig.payment_slug.' . $transactionType);
        }

        if (!is_null($walletId)) {
            $where['wallet_id'] = $walletId;
        }

        return empty($where) ? null : $where;
    }

    /**
     * Purpose: builds trade report query conditions.
     *
     * Action: combines optional user, category, and stock pair filters for exchange rows.
     */
    private function tradeWhere(int|string|null $userId, ?string $categoryType, int|string|null $stockPairId): ?array
    {
        $where = [];

        if (!is_null($userId)) {
            $where['stock_exchanges.user_id'] = $userId;
        }

        if (!is_null($categoryType)) {
            $where['stock_orders.category'] = config('commonconfig.category_slug.' . $categoryType);
        }

        if (!is_null($stockPairId)) {
            $where['stock_orders.stock_pair_id'] = $stockPairId;
        }

        return empty($where) ? null : $where;
    }

    /**
     * Purpose: builds open-order report query conditions.
     *
     * Action: combines pending-order status with optional user, category, and stock pair filters.
     */
    private function openOrderWhere(int|string|null $userId, ?string $categoryType, int|string|null $stockPairId): array
    {
        $where = [
            ['stock_orders.status', '<', STOCK_ORDER_COMPLETED],
        ];

        if (!is_null($userId)) {
            $where[] = ['stock_orders.user_id' => $userId];
        }

        if (!is_null($stockPairId)) {
            $where[] = ['stock_orders.stock_pair_id' => $stockPairId];
        }

        if (!is_null($categoryType)) {
            $where[] = ['stock_orders.category' => config('commonconfig.category_slug.' . $categoryType)];
        }

        return $where;
    }
}
