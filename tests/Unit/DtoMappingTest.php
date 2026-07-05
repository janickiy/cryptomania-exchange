<?php

namespace Tests\Unit;

use App\DTO\Admin\StockItemData;
use App\DTO\Admin\StockPairData;
use App\DTO\Admin\SystemNoticeData;
use App\DTO\Admin\UserAccountData;
use App\DTO\Admin\UserRoleManagementData;
use App\DTO\Admin\UserStatusData;
use App\DTO\Admin\WalletBalanceData;
use App\DTO\Exchange\IcoPurchaseData;
use App\DTO\Wallet\WithdrawalData;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

final class DtoMappingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container();
        $container->instance('config', new Repository([
            'commonconfig' => [
                'currency_transferable' => [CURRENCY_REAL, CURRENCY_CRYPTO],
            ],
        ]));

        Container::setInstance($container);
    }

    public function test_stock_item_data_normalizes_exchange_item_payload(): void
    {
        $data = StockItemData::fromArray([
            'item' => ' btc ',
            'item_name' => ' Bitcoin ',
            'item_type' => CURRENCY_CRYPTO,
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_ico' => ACTIVE_STATUS_INACTIVE,
            'exchange_status' => ACTIVE_STATUS_ACTIVE,
            'deposit_status' => ACTIVE_STATUS_ACTIVE,
            'deposit_fee' => '0.01',
            'withdrawal_status' => ACTIVE_STATUS_ACTIVE,
            'withdrawal_fee' => '0.02',
            'minimum_withdrawal_amount' => '0.1',
            'daily_withdrawal_limit' => '10',
            'api_service' => API_BITCOIN,
            'item_emoji_path' => 'coins/btc.png',
        ]);

        self::assertSame([
            'item' => 'BTC',
            'item_name' => 'Bitcoin',
            'item_type' => CURRENCY_CRYPTO,
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_ico' => ACTIVE_STATUS_INACTIVE,
            'exchange_status' => ACTIVE_STATUS_ACTIVE,
            'deposit_status' => ACTIVE_STATUS_ACTIVE,
            'deposit_fee' => '0.01',
            'withdrawal_status' => ACTIVE_STATUS_ACTIVE,
            'withdrawal_fee' => '0.02',
            'minimum_withdrawal_amount' => '0.1',
            'daily_withdrawal_limit' => '10',
            'api_service' => API_BITCOIN,
            'item_emoji' => 'coins/btc.png',
        ], $data->toArray());

        self::assertSame('coins/eth.png', $data->withItemEmoji('coins/eth.png')->itemEmoji);
    }

    public function test_stock_item_data_disables_transfer_fields_for_ico_payload(): void
    {
        $data = StockItemData::fromArray([
            'item' => ' ico ',
            'item_name' => ' Token ',
            'item_type' => CURRENCY_CRYPTO,
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_ico' => ACTIVE_STATUS_ACTIVE,
            'exchange_status' => ACTIVE_STATUS_ACTIVE,
            'deposit_status' => ACTIVE_STATUS_ACTIVE,
            'deposit_fee' => '1',
            'withdrawal_status' => ACTIVE_STATUS_ACTIVE,
            'withdrawal_fee' => '2',
            'minimum_withdrawal_amount' => '3',
            'daily_withdrawal_limit' => '4',
            'api_service' => API_BITCOIN,
        ]);

        self::assertSame(ACTIVE_STATUS_INACTIVE, $data->exchangeStatus);
        self::assertSame(ACTIVE_STATUS_INACTIVE, $data->depositStatus);
        self::assertSame(ACTIVE_STATUS_INACTIVE, $data->withdrawalStatus);
        self::assertSame(0, $data->depositFee);
        self::assertSame(0, $data->withdrawalFee);
        self::assertSame(0, $data->minimumWithdrawalAmount);
        self::assertSame(0, $data->dailyWithdrawalLimit);
        self::assertNull($data->apiService);
    }

    public function test_admin_dtos_map_validated_payloads_to_repository_arrays(): void
    {
        self::assertSame([
            'stock_item_id' => 10,
            'base_item_id' => 20,
            'last_price' => '123.45',
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_default' => ACTIVE_STATUS_INACTIVE,
        ], StockPairData::fromArray([
            'stock_item_id' => '10',
            'base_item_id' => '20',
            'last_price' => '123.45',
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_default' => ACTIVE_STATUS_INACTIVE,
        ])->toArray());

        self::assertSame([
            'title' => 'Notice',
            'description' => 'Body',
            'start_at' => '2026-07-05 10:00:00',
            'end_at' => '2026-07-06 10:00:00',
            'status' => ACTIVE_STATUS_ACTIVE,
            'type' => 'info',
        ], SystemNoticeData::fromArray([
            'title' => ' Notice ',
            'description' => ' Body ',
            'start_at' => '2026-07-05 10:00:00',
            'end_at' => '2026-07-06 10:00:00',
            'status' => ACTIVE_STATUS_ACTIVE,
            'type' => 'info',
        ])->toArray());

        self::assertSame([
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'address' => 'London',
            'user_role_management_id' => USER_ROLE_USER,
            'email' => 'ada@example.test',
            'username' => 'ada',
            'is_email_verified' => ACTIVE_STATUS_ACTIVE,
            'is_financial_active' => ACTIVE_STATUS_ACTIVE,
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_accessible_under_maintenance' => ACTIVE_STATUS_INACTIVE,
        ], UserAccountData::fromArray([
            'first_name' => ' Ada ',
            'last_name' => ' Lovelace ',
            'address' => ' London ',
            'user_role_management_id' => USER_ROLE_USER,
            'email' => ' ada@example.test ',
            'username' => ' ada ',
            'is_email_verified' => ACTIVE_STATUS_ACTIVE,
            'is_financial_active' => ACTIVE_STATUS_ACTIVE,
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_accessible_under_maintenance' => ACTIVE_STATUS_INACTIVE,
        ])->toArray());
    }

    public function test_small_dtos_map_to_expected_arrays(): void
    {
        self::assertSame([
            'role_name' => 'Trader',
            'route_group' => ['wallets' => ['reader_access']],
        ], UserRoleManagementData::fromArray([
            'role_name' => ' Trader ',
            'roles' => ['wallets' => ['reader_access']],
        ])->toArray());

        self::assertSame([
            'is_email_verified' => ACTIVE_STATUS_ACTIVE,
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_financial_active' => ACTIVE_STATUS_ACTIVE,
            'is_accessible_under_maintenance' => ACTIVE_STATUS_INACTIVE,
        ], UserStatusData::fromArray([
            'is_email_verified' => ACTIVE_STATUS_ACTIVE,
            'is_active' => ACTIVE_STATUS_ACTIVE,
            'is_financial_active' => ACTIVE_STATUS_ACTIVE,
            'is_accessible_under_maintenance' => ACTIVE_STATUS_INACTIVE,
        ])->toArray());

        self::assertSame(['amount' => '42.5'], WalletBalanceData::fromArray(['amount' => 42.5])->toArray());
        self::assertSame(['stock_pair_id' => 5, 'amount' => '12.25'], IcoPurchaseData::fromArray(['stock_pair_id' => '5', 'amount' => 12.25])->toArray());
        self::assertSame(['amount' => '1.5', 'address' => 'wallet-address'], WithdrawalData::fromArray(['amount' => 1.5, 'address' => 'wallet-address'])->toArray());
    }
}
