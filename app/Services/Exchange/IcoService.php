<?php

namespace App\Services\Exchange;

use App\DTO\Exchange\IcoPurchaseData;
use App\Models\User\Wallet;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Admin\Interfaces\StockItemInterface;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\DataListService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IcoService
{
    public function __construct(
        private readonly StockItemInterface $stockItems,
        private readonly StockPairInterface $stockPairs,
        private readonly WalletInterface $wallets,
        private readonly StockOrderInterface $stockOrders,
        private readonly StockExchangeInterface $stockExchanges,
        private readonly TransactionInterface $transactions,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function indexData(): array
    {
        $searchFields = [];
        $orderFields = [];
        $where = [
            'stock_items.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_items.is_ico' => ACTIVE_STATUS_ACTIVE,
        ];
        $select = [
            'stock_items.*',
            'stock_pairs.id as stock_pair_id',
            'last_price',
            'ico_total_sold',
            'ico_total_earned',
            'base_items.item_name as base_item_name',
            'base_items.item as base_item',
        ];
        $joinArray = [
            ['stock_pairs', 'stock_pairs.stock_item_id', '=', 'stock_items.id'],
            ['stock_items as base_items', 'base_items.id', '=', 'stock_pairs.base_item_id'],
        ];
        $query = $this->stockItems->paginateWithFilters($searchFields, $orderFields, $where, $select, $joinArray);

        return [
            'title' => __('Exchange'),
            'list' => $this->dataListService->dataList($query, $searchFields, $orderFields),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function buyData(int|string $stockPairId): array
    {
        $wallets = [];

        if (Auth::check()) {
            foreach ($this->wallets->getByConditions(['user_id' => Auth::id()]) as $wallet) {
                $wallets[$wallet->stock_item_id] = $wallet->primary_balance;
            }
        }

        return [
            'stockPair' => $this->stockPairs->findOrFailByConditions(['id' => $stockPairId], ['stockItem', 'baseItem']),
            'wallets' => $wallets,
            'title' => __('Trade ICO'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function purchase(IcoPurchaseData $data): array
    {
        $user = Auth::user();

        if (empty($user) || $user->userInfo->is_id_verified != ID_STATUS_VERIFIED) {
            return $this->response(false, __("Your account must be ID verified to make any order."));
        }

        $stockPair = $this->stockPairs->getFirstStockPairDetailByConditions([
            'stock_pairs.id' => $data->stockPairId,
            'stock_pairs.is_active' => ACTIVE_STATUS_ACTIVE,
            'stock_item.is_active' => ACTIVE_STATUS_ACTIVE,
            'base_item.is_active' => ACTIVE_STATUS_ACTIVE,
        ]);

        if (empty($stockPair)) {
            return $this->response(false, __('Invalid request.'));
        }

        $icoFee = admin_settings('ico_fee');
        $totalAmount = bcmul($data->amount, $stockPair->last_price);
        $feeAmount = bcdiv(bcmul($totalAmount, $icoFee), '100');
        $totalAmountToCharge = bcadd($totalAmount, $feeAmount);

        try {
            DB::beginTransaction();

            if ($this->wallets->bulkUpdate($this->walletUpdates($stockPair, $data->amount, $totalAmountToCharge)) < 2) {
                DB::rollBack();

                return $this->response(false, __('Failed to buy.'));
            }

            $buyStockOrder = $this->stockOrders->create([
                'user_id' => Auth::id(),
                'stock_pair_id' => $stockPair->id,
                'category' => CATEGORY_ICO,
                'exchange_type' => EXCHANGE_BUY,
                'price' => $stockPair->last_price,
                'amount' => $data->amount,
                'maker_fee' => $icoFee,
                'status' => STOCK_ORDER_COMPLETED,
            ]);

            $stockExchange = $this->stockExchanges->create([
                'user_id' => Auth::id(),
                'stock_order_id' => $buyStockOrder->id,
                'stock_pair_id' => $buyStockOrder->stock_pair_id,
                'amount' => $data->amount,
                'price' => $stockPair->last_price,
                'total' => $totalAmountToCharge,
                'fee' => $feeAmount,
                'exchange_type' => $buyStockOrder->exchange_type,
                'is_maker' => 1,
            ]);

            $this->transactions->insert(
                $this->transactionAttributes($stockPair, $buyStockOrder, $stockExchange, $data->amount, $totalAmount, $feeAmount, $totalAmountToCharge)
            );

            if (!$this->stockPairs->updateByConditions($this->stockPairTotals($data->amount, $feeAmount, $totalAmountToCharge), ['id' => $stockPair->id])) {
                DB::rollBack();

                return $this->response(false, __('Failed to buy.'));
            }

            DB::commit();

            return $this->response(true, __('Your buy request is completed.'));
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->response(false, __("You don't have enough balance."));
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function walletUpdates(mixed $stockPair, string $amount, string $totalAmountToCharge): array
    {
        return [
            [
                'conditions' => ['user_id' => Auth::id(), 'stock_item_id' => $stockPair->stock_item_id],
                'fields' => [
                    'primary_balance' => ['increment', $amount],
                ],
            ],
            [
                'conditions' => ['user_id' => Auth::id(), 'stock_item_id' => $stockPair->base_item_id],
                'fields' => [
                    'primary_balance' => ['decrement', $totalAmountToCharge],
                ],
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function transactionAttributes(
        mixed $stockPair,
        mixed $buyStockOrder,
        mixed $stockExchange,
        string $amount,
        string $totalAmount,
        string $feeAmount,
        string $totalAmountToCharge,
    ): array {
        $date = now();

        return [
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->base_item_id,
                'model_name' => get_class(new Wallet()),
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($totalAmountToCharge, '-1'),
                'journal' => DECREASED_FROM_WALLET_ON_ORDER_PLACE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->base_item_id,
                'model_name' => get_class($buyStockOrder),
                'model_id' => $buyStockOrder->id,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $totalAmountToCharge,
                'journal' => INCREASED_TO_ORDER_ON_ORDER_PLACE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->base_item_id,
                'model_name' => get_class($buyStockOrder),
                'model_id' => $buyStockOrder->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($totalAmountToCharge, '-1'),
                'journal' => DECREASED_FROM_ORDER_ON_SUCCESSFUL_TRANSACTION,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->base_item_id,
                'model_name' => get_class($stockExchange),
                'model_id' => $stockExchange->id,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $totalAmountToCharge,
                'journal' => INCREASED_TO_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->base_item_id,
                'model_name' => get_class($stockExchange),
                'model_id' => $stockExchange->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($totalAmount, '-1'),
                'journal' => DECREASED_FROM_EXCHANGE_ON_SUCCESSFUL_TRANSACTION,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->base_item_id,
                'model_name' => null,
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $totalAmount,
                'journal' => INCREASED_TO_SYSTEM_ON_SUCCESSFUL_TRANSACTION,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->base_item_id,
                'model_name' => get_class($stockExchange),
                'model_id' => $stockExchange->id,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($feeAmount, '-1'),
                'journal' => DECREASED_FROM_EXCHANGE_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->base_item_id,
                'model_name' => null,
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $feeAmount,
                'journal' => INCREASED_TO_SYSTEM_AS_SERVICE_FEE_ON_SUCCESSFUL_TRANSACTION,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->stock_item_id,
                'model_name' => null,
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_DEBIT,
                'amount' => bcmul($amount, '-1'),
                'journal' => DECREASED_FROM_SYSTEM_ON_ICO_SALE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
            [
                'user_id' => Auth::id(),
                'stock_item_id' => $stockPair->stock_item_id,
                'model_name' => get_class(new Wallet()),
                'model_id' => null,
                'transaction_type' => TRANSACTION_TYPE_CREDIT,
                'amount' => $amount,
                'journal' => INCREASED_TO_WALLET_FROM_SYSTEM_ON_ICO_PURCHASE,
                'created_at' => $date,
                'updated_at' => $date,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function stockPairTotals(string $amount, string $feeAmount, string $totalAmountToCharge): array
    {
        return [
            'ico_total_sold' => DB::raw('ico_total_sold + ' . $amount),
            'ico_total_earned' => DB::raw('ico_total_earned + ' . $totalAmountToCharge),
            'ico_fee_earned' => DB::raw('ico_fee_earned + ' . $feeAmount),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function response(bool $status, string $message): array
    {
        return [
            SERVICE_RESPONSE_STATUS => $status,
            SERVICE_RESPONSE_MESSAGE => $message,
        ];
    }
}
