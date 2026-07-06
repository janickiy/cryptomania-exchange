<?php

namespace Database\Seeders;

use App\Enums\CoreConstant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExchangePageTestDataSeeder extends Seeder
{
    private const DASH_BTC_STOCK = 'DASH';
    private const DASH_BTC_BASE = 'BTC';

    /**
     * Seed focused demo data for the DASH/BTC exchange page and ICO listing.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $users = DB::table('users')->orderBy('id')->get();

            if ($users->isEmpty()) {
                $this->command->warn('Users are missing. Run the base seeders first.');

                return;
            }

            $dashBtcPair = $this->findPair(self::DASH_BTC_STOCK, self::DASH_BTC_BASE);

            if (empty($dashBtcPair)) {
                $this->command->warn('DASH/BTC stock pair is missing. Run the stock pair seeder first.');

                return;
            }

            $this->seedDashBtcPage($dashBtcPair, $users);
            $this->seedIcoPage($users);
        });
    }

    /**
     * Seed order book, public trades, pair summary, and chart data for DASH/BTC.
     */
    private function seedDashBtcPage(object $pair, $users): void
    {
        $basePrice = max(0.00000001, (float) $pair->last_price);
        $buyOrderIds = $this->seedDashBtcOrders($pair->id, $users, CoreConstant::EXCHANGE_BUY, $basePrice);
        $sellOrderIds = $this->seedDashBtcOrders($pair->id, $users, CoreConstant::EXCHANGE_SELL, $basePrice);

        $this->seedDashBtcTrades($pair->id, $users, array_merge($buyOrderIds, $sellOrderIds), $basePrice);
        $this->updateDashBtcPairSummary($pair->id, $basePrice);
        $this->seedDashBtcGraphData($pair->id, $basePrice);
    }

    /**
     * Seed deterministic pending orders for one side of the DASH/BTC order book.
     */
    private function seedDashBtcOrders(int $stockPairId, $users, int $exchangeType, float $basePrice): array
    {
        $orderIds = [];
        $now = Carbon::now();
        $step = 0.00000037;

        for ($i = 1; $i <= 12; $i++) {
            $priceMultiplier = $exchangeType === CoreConstant::EXCHANGE_BUY ? -1 : 1;
            $price = $this->decimal($basePrice + ($priceMultiplier * $step * $i));
            $amount = $this->decimal(1.4 + ($i * 0.42));
            $userId = $i % 3 === 0 ? $users->first()->id : $users[$i % $users->count()]->id;

            DB::table('stock_orders')->updateOrInsert(
                [
                    'user_id' => $userId,
                    'stock_pair_id' => $stockPairId,
                    'category' => CoreConstant::CATEGORY_EXCHANGE,
                    'exchange_type' => $exchangeType,
                    'price' => $price,
                ],
                [
                    'amount' => $amount,
                    'exchanged' => '0.00000000',
                    'canceled' => '0.00000000',
                    'stop_limit' => $i % 5 === 0 ? $this->decimal((float) $price * 0.985) : null,
                    'maker_fee' => '0.10000000',
                    'taker_fee' => '0.20000000',
                    'status' => CoreConstant::STOCK_ORDER_PENDING,
                    'created_at' => $now->copy()->subMinutes($i * 6),
                    'updated_at' => $now,
                ]
            );

            $order = DB::table('stock_orders')
                ->where('user_id', $userId)
                ->where('stock_pair_id', $stockPairId)
                ->where('category', CoreConstant::CATEGORY_EXCHANGE)
                ->where('exchange_type', $exchangeType)
                ->where('price', $price)
                ->first();

            if (!empty($order)) {
                $orderIds[] = $order->id;
            }
        }

        return $orderIds;
    }

    /**
     * Seed recent DASH/BTC trades for market trades and authenticated trade history.
     */
    private function seedDashBtcTrades(int $stockPairId, $users, array $orderIds, float $basePrice): void
    {
        if (empty($orderIds)) {
            return;
        }

        $now = Carbon::now();

        for ($i = 1; $i <= 18; $i++) {
            $orderId = $orderIds[($i - 1) % count($orderIds)];
            $exchangeType = $i % 2 === 0 ? CoreConstant::EXCHANGE_BUY : CoreConstant::EXCHANGE_SELL;
            $price = $this->decimal($basePrice * (1 + (($i % 7) - 3) * 0.0017));
            $amount = $this->decimal(0.35 + ($i * 0.08));
            $total = $this->decimal((float) $price * (float) $amount);

            DB::table('stock_exchanges')->updateOrInsert(
                [
                    'stock_order_id' => $orderId,
                    'stock_pair_id' => $stockPairId,
                    'price' => $price,
                    'amount' => $amount,
                    'exchange_type' => $exchangeType,
                ],
                [
                    'user_id' => $i % 4 === 0 ? $users->first()->id : $users[$i % $users->count()]->id,
                    'stock_exchange_group_id' => null,
                    'total' => $total,
                    'fee' => $this->decimal((float) $total * 0.001),
                    'referral_earning' => $this->decimal((float) $total * 0.0002),
                    'related_order_id' => null,
                    'is_maker' => 1,
                    'created_at' => $now->copy()->subMinutes($i * 9),
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * Update the DASH/BTC 24-hour summary used by the exchange header.
     */
    private function updateDashBtcPairSummary(int $stockPairId, float $basePrice): void
    {
        $now = Carbon::now();
        $exchange24 = [];

        for ($i = 7; $i >= 0; $i--) {
            $price = $this->decimal($basePrice * (1 + (($i - 3) * 0.0025)));
            $amount = $this->decimal(8 + ((7 - $i) * 1.75));

            $exchange24[$now->copy()->subHours($i * 3)->timestamp] = [
                'price' => $price,
                'amount' => $amount,
                'total' => $this->decimal((float) $price * (float) $amount),
            ];
        }

        DB::table('stock_pairs')->where('id', $stockPairId)->update([
            'exchange_24' => json_encode($exchange24),
            'last_price' => $this->decimal($basePrice),
            'base_item_buy_order_volume' => '14.85000000',
            'stock_item_buy_order_volume' => '43.26000000',
            'base_item_sale_order_volume' => '16.42000000',
            'stock_item_sale_order_volume' => '47.81000000',
            'exchanged_buy_total' => '18.75000000',
            'exchanged_sale_total' => '17.32000000',
            'exchanged_amount' => '128.40000000',
            'exchanged_maker_total' => '11.25000000',
            'exchanged_buy_fee' => '0.01875000',
            'exchanged_sale_fee' => '0.01732000',
            'updated_at' => $now,
        ]);
    }

    /**
     * Refresh DASH/BTC chart data with visible intraday movement.
     */
    private function seedDashBtcGraphData(int $stockPairId, float $basePrice): void
    {
        $now = Carbon::now();
        $start = $now->copy()->startOfDay();
        $columns = [
            '5min' => 5,
            '15min' => 15,
            '30min' => 30,
            '2hr' => 120,
            '4hr' => 240,
            '1day' => 1440,
        ];
        $attributes = [
            'stock_pair_id' => $stockPairId,
            'created_at' => $start,
            'updated_at' => $now,
        ];

        foreach ($columns as $column => $minutes) {
            $attributes[$column] = json_encode($this->graphSeries($start, $now, $minutes, $basePrice));
        }

        DB::table('stock_graph_datas')->updateOrInsert(
            ['stock_pair_id' => $stockPairId, 'created_at' => $start],
            $attributes
        );
    }

    /**
     * Seed ICO stock items, listing pairs, wallets, and completed purchase history.
     */
    private function seedIcoPage($users): void
    {
        $icos = [
            ['item' => 'CMN', 'name' => 'Cryptomania Network', 'base' => 'BTC', 'price' => 0.00025000, 'sold' => 125000, 'earned' => 31.25],
            ['item' => 'GLX', 'name' => 'Galaxy Token', 'base' => 'USD', 'price' => 0.03500000, 'sold' => 84000, 'earned' => 2940.00],
            ['item' => 'NEX', 'name' => 'Nexus Chain', 'base' => 'BTC', 'price' => 0.00012000, 'sold' => 210000, 'earned' => 25.20],
        ];

        foreach ($icos as $index => $ico) {
            $stockItemId = $this->upsertIcoStockItem($ico);
            $baseItem = DB::table('stock_items')->where('item', $ico['base'])->first();

            if (empty($baseItem)) {
                continue;
            }

            $stockPairId = $this->upsertIcoStockPair($stockItemId, (int) $baseItem->id, $ico, $index);
            $this->upsertIcoWallets($users, $stockItemId);
            $this->seedIcoPurchases($users, $stockPairId, $ico);
        }
    }

    /**
     * Create or update one ICO stock item.
     */
    private function upsertIcoStockItem(array $ico): int
    {
        $now = Carbon::now();

        DB::table('stock_items')->updateOrInsert(
            ['item' => $ico['item']],
            [
                'item_name' => $ico['name'],
                'item_type' => CURRENCY_CRYPTO,
                'item_emoji' => null,
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'exchange_status' => ACTIVE_STATUS_INACTIVE,
                'is_fee_applicable' => ACTIVE_STATUS_ACTIVE,
                'is_ico' => ACTIVE_STATUS_ACTIVE,
                'deposit_status' => ACTIVE_STATUS_INACTIVE,
                'deposit_fee' => '0.00',
                'withdrawal_status' => ACTIVE_STATUS_INACTIVE,
                'withdrawal_fee' => '0.00',
                'daily_withdrawal_limit' => '25000.00000000',
                'api_service' => null,
                'minimum_withdrawal_amount' => '0.00000000',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        return (int) DB::table('stock_items')->where('item', $ico['item'])->value('id');
    }

    /**
     * Create or update the ICO listing stock pair.
     */
    private function upsertIcoStockPair(int $stockItemId, int $baseItemId, array $ico, int $index): int
    {
        $now = Carbon::now();
        $total = $this->decimal($ico['earned']);

        DB::table('stock_pairs')->updateOrInsert(
            [
                'stock_item_id' => $stockItemId,
                'base_item_id' => $baseItemId,
            ],
            [
                'is_active' => ACTIVE_STATUS_ACTIVE,
                'is_default' => ACTIVE_STATUS_INACTIVE,
                'exchange_24' => json_encode([]),
                'last_price' => $this->decimal($ico['price']),
                'ico_total_sold' => $this->decimal($ico['sold']),
                'ico_total_earned' => $total,
                'ico_fee_earned' => $this->decimal((float) $total * 0.01),
                'exchanged_amount' => $this->decimal($ico['sold'] * 0.03),
                'exchanged_buy_total' => $this->decimal($ico['earned'] * 0.55),
                'exchanged_sale_total' => $this->decimal($ico['earned'] * 0.45),
                'exchanged_maker_total' => $this->decimal($ico['earned'] * 0.35),
                'exchanged_buy_fee' => $this->decimal($ico['earned'] * 0.001),
                'exchanged_sale_fee' => $this->decimal($ico['earned'] * 0.0012),
                'base_item_buy_order_volume' => $this->decimal(100 + ($index * 25)),
                'stock_item_buy_order_volume' => $this->decimal(5000 + ($index * 1500)),
                'base_item_sale_order_volume' => $this->decimal(80 + ($index * 20)),
                'stock_item_sale_order_volume' => $this->decimal(4200 + ($index * 1200)),
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return (int) DB::table('stock_pairs')
            ->where('stock_item_id', $stockItemId)
            ->where('base_item_id', $baseItemId)
            ->value('id');
    }

    /**
     * Create wallets for each user and new ICO item.
     */
    private function upsertIcoWallets($users, int $stockItemId): void
    {
        $now = Carbon::now();

        foreach ($users as $index => $user) {
            DB::table('wallets')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'stock_item_id' => $stockItemId,
                ],
                [
                    'primary_balance' => $this->decimal(1000 + ($index * 250)),
                    'on_order_balance' => '0.00000000',
                    'address' => sprintf('demo-ico-wallet-%s-%s', $user->id, $stockItemId),
                    'is_active' => ACTIVE_STATUS_ACTIVE,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * Seed completed ICO purchases so reports and detail screens have data.
     */
    private function seedIcoPurchases($users, int $stockPairId, array $ico): void
    {
        $now = Carbon::now();

        foreach ($users as $index => $user) {
            $amount = $this->decimal(500 + ($index * 175));
            $price = $this->decimal($ico['price']);
            $total = $this->decimal((float) $amount * (float) $price);

            DB::table('stock_orders')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'stock_pair_id' => $stockPairId,
                    'category' => CoreConstant::CATEGORY_ICO,
                    'exchange_type' => CoreConstant::EXCHANGE_BUY,
                    'price' => $price,
                ],
                [
                    'amount' => $amount,
                    'exchanged' => $amount,
                    'canceled' => '0.00000000',
                    'stop_limit' => null,
                    'maker_fee' => '1.00000000',
                    'taker_fee' => '0.00000000',
                    'status' => CoreConstant::STOCK_ORDER_COMPLETED,
                    'created_at' => $now->copy()->subDays($index + 1),
                    'updated_at' => $now,
                ]
            );

            $order = DB::table('stock_orders')
                ->where('user_id', $user->id)
                ->where('stock_pair_id', $stockPairId)
                ->where('category', CoreConstant::CATEGORY_ICO)
                ->where('price', $price)
                ->first();

            if (empty($order)) {
                continue;
            }

            DB::table('stock_exchanges')->updateOrInsert(
                [
                    'stock_order_id' => $order->id,
                    'stock_pair_id' => $stockPairId,
                    'exchange_type' => CoreConstant::EXCHANGE_BUY,
                ],
                [
                    'user_id' => $user->id,
                    'stock_exchange_group_id' => null,
                    'amount' => $amount,
                    'price' => $price,
                    'total' => $total,
                    'fee' => $this->decimal((float) $total * 0.01),
                    'referral_earning' => '0.00000000',
                    'related_order_id' => null,
                    'is_maker' => 1,
                    'created_at' => $now->copy()->subDays($index + 1),
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * Find a stock pair by stock and base item abbreviations.
     */
    private function findPair(string $stockItem, string $baseItem): ?object
    {
        return DB::table('stock_pairs')
            ->join('stock_items as stock_item', 'stock_item.id', '=', 'stock_pairs.stock_item_id')
            ->join('stock_items as base_item', 'base_item.id', '=', 'stock_pairs.base_item_id')
            ->where('stock_item.item', $stockItem)
            ->where('base_item.item', $baseItem)
            ->select('stock_pairs.*')
            ->first();
    }

    /**
     * Build chart OHLC points in the format expected by exchange charts.
     */
    private function graphSeries(Carbon $start, Carbon $end, int $stepMinutes, float $basePrice): array
    {
        $series = [];
        $cursor = $start->copy();
        $point = 0;

        while ($cursor <= $end) {
            $wave = (($point % 12) - 6) * 0.0018;
            $open = $this->decimal($basePrice * (1 + $wave));
            $close = $this->decimal($basePrice * (1 + $wave + 0.0012));
            $low = $this->decimal(min((float) $open, (float) $close) * 0.997);
            $high = $this->decimal(max((float) $open, (float) $close) * 1.004);

            $series[$cursor->timestamp] = [
                $cursor->toDateTimeString(),
                $open,
                $close,
                $low,
                $high,
            ];

            $cursor->addMinutes($stepMinutes);
            $point++;
        }

        return $series;
    }

    /**
     * Format numeric values for DECIMAL(19,8) columns.
     */
    private function decimal(float $value): string
    {
        return number_format(max(0, $value), 8, '.', '');
    }
}
