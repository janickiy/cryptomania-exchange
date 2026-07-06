<?php

namespace Database\Seeders;

use App\Enums\CoreConstant;
use App\Models\Backend\Post;
use App\Models\Backend\StockItem;
use App\Models\Backend\StockPair;
use App\Models\User\Question;
use App\Models\User\User;
use App\Models\User\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    private const COMMENT_TARGET = 48;
    private const NOTIFICATION_TARGET = 24;
    private const DEPOSIT_TARGET = 28;
    private const WITHDRAWAL_TARGET = 20;
    private const ORDER_TARGET = 64;
    private const EXCHANGE_TARGET = 40;
    private const TRANSACTION_TARGET = 96;

    /**
     * Seed demo data for dashboards, reports, exchange screens, and community pages.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $users = User::query()->orderBy('id')->get();
            $stockItems = StockItem::query()->orderBy('id')->get();
            $stockPairs = StockPair::query()->orderBy('id')->get();
            $wallets = Wallet::query()->orderBy('id')->get();

            if ($users->isEmpty() || $stockItems->isEmpty() || $stockPairs->isEmpty() || $wallets->isEmpty()) {
                $this->command->warn('Base seed data is missing. Run DatabaseSeeder before TestDataSeeder.');
                return;
            }

            $this->refreshWalletBalances($wallets);
            $this->refreshStockPairSummaries($stockPairs);
            $this->seedComments($users);
            $this->seedNotifications($users);
            $this->seedDeposits($wallets);
            $this->seedWithdrawals($wallets);
            $this->seedStockOrders($users, $stockPairs);
            $this->seedStockExchanges();
            $this->seedTransactions($users, $stockItems);
            $this->seedGraphData($stockPairs);
        });
    }

    /**
     * Give every demo wallet stable balances for account and admin wallet screens.
     */
    private function refreshWalletBalances($wallets): void
    {
        foreach ($wallets as $index => $wallet) {
            $primaryBalance = $this->decimal(25000 + ($index * 137.45));
            $onOrderBalance = $this->decimal(250 + (($index % 8) * 19.75));

            DB::table('wallets')->where('id', $wallet->id)->update([
                'primary_balance' => $primaryBalance,
                'on_order_balance' => $onOrderBalance,
                'address' => $wallet->address ?: sprintf('demo-wallet-%s-%02d', $wallet->user_id, $wallet->stock_item_id),
                'updated_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Populate pair totals and 24-hour summaries so exchange widgets look alive.
     */
    private function refreshStockPairSummaries($stockPairs): void
    {
        $now = Carbon::now();

        foreach ($stockPairs as $index => $pair) {
            $basePrice = (float) $pair->last_price > 0 ? (float) $pair->last_price : (0.01 + ($index * 0.07));
            $lastPrice = $this->decimal($basePrice * (1 + (($index % 5) * 0.012)));
            $amount = $this->decimal(18 + ($index * 2.75));
            $total = $this->decimal((float) $lastPrice * (float) $amount);
            $exchange24 = [];

            for ($point = 5; $point >= 0; $point--) {
                $time = $now->copy()->subHours($point * 4)->timestamp;
                $price = $this->decimal((float) $lastPrice * (1 + (($point - 3) * 0.004)));
                $volume = $this->decimal((float) $amount / (6 - min($point, 5)));

                $exchange24[$time] = [
                    'price' => $price,
                    'amount' => $volume,
                    'total' => $this->decimal((float) $price * (float) $volume),
                ];
            }

            DB::table('stock_pairs')->where('id', $pair->id)->update([
                'exchange_24' => json_encode($exchange24),
                'last_price' => $lastPrice,
                'base_item_buy_order_volume' => $this->decimal(120 + ($index * 8.5)),
                'stock_item_buy_order_volume' => $this->decimal(24 + ($index * 1.35)),
                'base_item_sale_order_volume' => $this->decimal(96 + ($index * 6.25)),
                'stock_item_sale_order_volume' => $this->decimal(19 + ($index * 1.1)),
                'exchanged_buy_total' => $total,
                'exchanged_sale_total' => $this->decimal((float) $total * 0.92),
                'exchanged_amount' => $amount,
                'exchanged_maker_total' => $this->decimal((float) $total * 0.62),
                'exchanged_buy_fee' => $this->decimal((float) $total * 0.001),
                'exchanged_sale_fee' => $this->decimal((float) $total * 0.0012),
                'ico_total_sold' => $this->decimal(30 + ($index * 2.25)),
                'ico_total_earned' => $this->decimal((float) $total * 0.15),
                'ico_fee_earned' => $this->decimal((float) $total * 0.0015),
                'updated_at' => $now,
            ]);
        }
    }

    /**
     * Add sample comments to posts and questions for community moderation screens.
     */
    private function seedComments($users): void
    {
        $existing = DB::table('comments')->count();
        $missing = max(0, self::COMMENT_TARGET - $existing);

        if ($missing === 0) {
            return;
        }

        $posts = Post::query()->pluck('id')->all();
        $questions = Question::query()->pluck('id')->all();

        if (empty($posts) && empty($questions)) {
            return;
        }

        $rows = [];
        $now = Carbon::now();

        for ($i = 0; $i < $missing; $i++) {
            $usePost = !empty($posts) && ($i % 2 === 0 || empty($questions));
            $rows[] = [
                'user_id' => $users[$i % $users->count()]->id,
                'commentable_id' => $usePost ? $posts[$i % count($posts)] : $questions[$i % count($questions)],
                'commentable_type' => $usePost ? Post::class : Question::class,
                'content' => sprintf('Demo discussion note #%02d with a practical trading observation.', $existing + $i + 1),
                'created_at' => $now->copy()->subMinutes($i * 11),
                'updated_at' => $now->copy()->subMinutes($i * 11),
            ];
        }

        DB::table('comments')->insert($rows);
    }

    /**
     * Add notification records for header counters and user notification pages.
     */
    private function seedNotifications($users): void
    {
        $existing = DB::table('notifications')->count();
        $missing = max(0, self::NOTIFICATION_TARGET - $existing);

        if ($missing === 0) {
            return;
        }

        $rows = [];
        $now = Carbon::now();

        for ($i = 0; $i < $missing; $i++) {
            $rows[] = [
                'user_id' => $users[$i % $users->count()]->id,
                'data' => json_encode([
                    'title' => sprintf('Demo market alert #%02d', $existing + $i + 1),
                    'message' => 'A test exchange event was generated for the local environment.',
                    'url' => '/dashboard',
                ]),
                'read_at' => $i % 3 === 0 ? $now->copy()->subHours($i + 1) : null,
                'created_at' => $now->copy()->subHours($i + 1),
                'updated_at' => $now->copy()->subHours($i + 1),
            ];
        }

        DB::table('notifications')->insert($rows);
    }

    /**
     * Add deposit history for wallet and report pages.
     */
    private function seedDeposits($wallets): void
    {
        $existing = DB::table('deposits')->count();
        $missing = max(0, self::DEPOSIT_TARGET - $existing);

        if ($missing === 0) {
            return;
        }

        $statuses = [
            CoreConstant::PAYMENT_COMPLETED,
            CoreConstant::PAYMENT_PENDING,
            CoreConstant::PAYMENT_REVIEWING,
            CoreConstant::PAYMENT_FAILED,
        ];

        $rows = [];
        $now = Carbon::now();

        for ($i = 0; $i < $missing; $i++) {
            $wallet = $wallets[$i % $wallets->count()];
            $amount = $this->decimal(12 + (($existing + $i) * 1.37));

            $rows[] = [
                'ref_id' => (string) Str::uuid(),
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'stock_item_id' => $wallet->stock_item_id,
                'amount' => $amount,
                'network_fee' => $this->decimal((float) $amount * 0.001),
                'system_fee' => $this->decimal((float) $amount * 0.0005),
                'address' => sprintf('deposit-demo-address-%02d', $existing + $i + 1),
                'txn_id' => sprintf('demo-deposit-txn-%04d', $existing + $i + 1),
                'payment_method' => 1,
                'status' => $statuses[$i % count($statuses)],
                'created_at' => $now->copy()->subDays($i % 12)->subMinutes($i * 7),
                'updated_at' => $now->copy()->subDays($i % 12)->subMinutes($i * 7),
            ];
        }

        DB::table('deposits')->insert($rows);
    }

    /**
     * Add withdrawal history for review and wallet pages.
     */
    private function seedWithdrawals($wallets): void
    {
        $existing = DB::table('withdrawals')->count();
        $missing = max(0, self::WITHDRAWAL_TARGET - $existing);

        if ($missing === 0) {
            return;
        }

        $statuses = [
            CoreConstant::PAYMENT_PENDING,
            CoreConstant::PAYMENT_REVIEWING,
            CoreConstant::PAYMENT_COMPLETED,
            CoreConstant::PAYMENT_DECLINED,
        ];

        $rows = [];
        $now = Carbon::now();

        for ($i = 0; $i < $missing; $i++) {
            $wallet = $wallets[($i + 3) % $wallets->count()];
            $amount = $this->decimal(4 + (($existing + $i) * 0.71));

            $rows[] = [
                'ref_id' => (string) Str::uuid(),
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'stock_item_id' => $wallet->stock_item_id,
                'amount' => $amount,
                'network_fee' => $this->decimal((float) $amount * 0.002),
                'system_fee' => $this->decimal((float) $amount * 0.001),
                'address' => sprintf('withdrawal-demo-address-%02d', $existing + $i + 1),
                'txn_id' => sprintf('demo-withdrawal-txn-%04d', $existing + $i + 1),
                'payment_method' => 1,
                'status' => $statuses[$i % count($statuses)],
                'created_at' => $now->copy()->subDays($i % 10)->subMinutes($i * 13),
                'updated_at' => $now->copy()->subDays($i % 10)->subMinutes($i * 13),
            ];
        }

        DB::table('withdrawals')->insert($rows);
    }

    /**
     * Add open, completed, and canceled orders for order books and reports.
     */
    private function seedStockOrders($users, $stockPairs): void
    {
        $existing = DB::table('stock_orders')->count();
        $missing = max(0, self::ORDER_TARGET - $existing);

        if ($missing === 0) {
            return;
        }

        $statuses = [
            CoreConstant::STOCK_ORDER_PENDING,
            CoreConstant::STOCK_ORDER_COMPLETED,
            CoreConstant::STOCK_ORDER_CANCELED,
        ];

        $rows = [];
        $now = Carbon::now();

        for ($i = 0; $i < $missing; $i++) {
            $pair = $stockPairs[$i % $stockPairs->count()];
            $price = $this->decimal(max(0.00000001, (float) $pair->last_price) * (1 + (($i % 9) - 4) * 0.006));
            $amount = $this->decimal(0.5 + (($existing + $i) % 12) * 0.27);
            $status = $statuses[$i % count($statuses)];
            $exchanged = $status === CoreConstant::STOCK_ORDER_COMPLETED ? $amount : $this->decimal((float) $amount * 0.25);
            $canceled = $status === CoreConstant::STOCK_ORDER_CANCELED ? $this->decimal((float) $amount * 0.75) : '0.00000000';

            $rows[] = [
                'user_id' => $users[$i % $users->count()]->id,
                'stock_pair_id' => $pair->id,
                'category' => $i % 6 === 0 ? CoreConstant::CATEGORY_ICO : CoreConstant::CATEGORY_EXCHANGE,
                'exchange_type' => $i % 2 === 0 ? CoreConstant::EXCHANGE_BUY : CoreConstant::EXCHANGE_SELL,
                'price' => $price,
                'amount' => $amount,
                'exchanged' => $exchanged,
                'canceled' => $canceled,
                'stop_limit' => $i % 5 === 0 ? $this->decimal((float) $price * 0.97) : null,
                'maker_fee' => '0.10000000',
                'taker_fee' => '0.20000000',
                'status' => $status,
                'created_at' => $now->copy()->subHours($i + 1),
                'updated_at' => $now->copy()->subHours($i + 1),
            ];
        }

        DB::table('stock_orders')->insert($rows);
    }

    /**
     * Add matched trades for exchange history and market-trade widgets.
     */
    private function seedStockExchanges(): void
    {
        $existing = DB::table('stock_exchanges')->count();
        $missing = max(0, self::EXCHANGE_TARGET - $existing);

        if ($missing === 0) {
            return;
        }

        $orders = DB::table('stock_orders')->orderBy('id')->get();

        if ($orders->isEmpty()) {
            return;
        }

        $rows = [];
        $now = Carbon::now();

        for ($i = 0; $i < $missing; $i++) {
            $order = $orders[$i % $orders->count()];
            $amount = $this->decimal(max(0.00000001, (float) $order->amount * 0.35));
            $price = $this->decimal((float) $order->price);
            $total = $this->decimal((float) $price * (float) $amount);

            $rows[] = [
                'user_id' => $order->user_id,
                'stock_exchange_group_id' => null,
                'stock_order_id' => $order->id,
                'stock_pair_id' => $order->stock_pair_id,
                'amount' => $amount,
                'price' => $price,
                'total' => $total,
                'fee' => $this->decimal((float) $total * 0.001),
                'referral_earning' => $this->decimal((float) $total * 0.0002),
                'exchange_type' => $order->exchange_type,
                'related_order_id' => null,
                'is_maker' => $i % 2,
                'created_at' => $now->copy()->subHours($i + 2),
                'updated_at' => $now->copy()->subHours($i + 2),
            ];
        }

        DB::table('stock_exchanges')->insert($rows);
    }

    /**
     * Add ledger transactions linked to deposits, withdrawals, and trades.
     */
    private function seedTransactions($users, $stockItems): void
    {
        $existing = DB::table('transactions')->count();
        $missing = max(0, self::TRANSACTION_TARGET - $existing);

        if ($missing === 0) {
            return;
        }

        $deposits = DB::table('deposits')->pluck('id')->all();
        $withdrawals = DB::table('withdrawals')->pluck('id')->all();
        $exchanges = DB::table('stock_exchanges')->pluck('id')->all();
        $journals = [
            CoreConstant::INCREASED_TO_WALLET_ON_DEPOSIT_CONFIRMATION,
            CoreConstant::DECREASED_FROM_WALLET_ON_WITHDRAWAL_REQUEST,
            CoreConstant::DECREASED_FROM_ORDER_ON_SUCCESSFUL_TRANSACTION,
            CoreConstant::INCREASED_TO_WALLET_ON_SUCCESSFUL_TRANSACTION,
        ];
        $rows = [];
        $now = Carbon::now();

        for ($i = 0; $i < $missing; $i++) {
            [$modelName, $modelId] = $this->transactionModel($i, $deposits, $withdrawals, $exchanges);

            $rows[] = [
                'user_id' => $users[$i % $users->count()]->id,
                'stock_item_id' => $stockItems[$i % $stockItems->count()]->id,
                'model_name' => $modelName,
                'model_id' => $modelId,
                'transaction_type' => $i % 2 === 0 ? CoreConstant::TRANSACTION_TYPE_CREDIT : CoreConstant::TRANSACTION_TYPE_DEBIT,
                'amount' => $this->decimal(2.5 + (($existing + $i) * 0.43)),
                'journal' => $journals[$i % count($journals)],
                'created_at' => $now->copy()->subHours($i + 1),
                'updated_at' => $now->copy()->subHours($i + 1),
            ];
        }

        DB::table('transactions')->insert($rows);
    }

    /**
     * Resolve a transaction parent model for demo ledger records.
     */
    private function transactionModel(int $index, array $deposits, array $withdrawals, array $exchanges): array
    {
        if ($index % 3 === 0 && !empty($deposits)) {
            return ['App\\Models\\User\\Deposit', $deposits[$index % count($deposits)]];
        }

        if ($index % 3 === 1 && !empty($withdrawals)) {
            return ['App\\Models\\User\\Withdrawal', $withdrawals[$index % count($withdrawals)]];
        }

        if (!empty($exchanges)) {
            return ['App\\Models\\Backend\\StockExchange', $exchanges[$index % count($exchanges)]];
        }

        return [null, null];
    }

    /**
     * Add chart data for every trading pair so exchange charts have local history.
     */
    private function seedGraphData($stockPairs): void
    {
        $now = Carbon::now();
        $start = $now->copy()->startOfDay();
        $timestamps = [
            '5min' => 5,
            '15min' => 15,
            '30min' => 30,
            '2hr' => 120,
            '4hr' => 240,
            '1day' => 1440,
        ];

        foreach ($stockPairs as $index => $pair) {
            $attributes = [
                'stock_pair_id' => $pair->id,
                'created_at' => $start,
                'updated_at' => $now,
            ];

            foreach ($timestamps as $column => $minutes) {
                $attributes[$column] = json_encode($this->graphSeries($start, $now, $minutes, (float) $pair->last_price, $index));
            }

            DB::table('stock_graph_datas')->updateOrInsert(
                [
                    'stock_pair_id' => $pair->id,
                    'created_at' => $start,
                ],
                $attributes
            );
        }
    }

    /**
     * Build OHLC points in the format expected by the chart service.
     */
    private function graphSeries(Carbon $start, Carbon $end, int $stepMinutes, float $basePrice, int $pairIndex): array
    {
        $series = [];
        $cursor = $start->copy();
        $point = 0;
        $basePrice = max(0.00000001, $basePrice);

        while ($cursor <= $end) {
            $wave = (($point % 8) - 4) * 0.003;
            $open = $this->decimal($basePrice * (1 + $wave));
            $close = $this->decimal($basePrice * (1 + $wave + 0.002));
            $low = $this->decimal(min((float) $open, (float) $close) * 0.996);
            $high = $this->decimal(max((float) $open, (float) $close) * (1.004 + ($pairIndex % 3) * 0.001));

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
