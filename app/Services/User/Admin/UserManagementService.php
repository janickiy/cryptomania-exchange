<?php

namespace App\Services\User\Admin;

use App\DTO\Admin\UserAccountData;
use App\DTO\Admin\UserStatusData;
use App\DTO\Admin\WalletBalanceData;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\User\UserService;
use App\Models\User\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserManagementService
{
    /**
     * Purpose: initializes the UserManagementService instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly UserInterface $user,
        private readonly UserInfoInterface $userInfo,
        private readonly NotificationInterface $notification,
        private readonly WalletInterface $wallet,
        private readonly TransactionInterface $transaction,
        private readonly UserService $userService,
    ) {
    }

    /**
     * Purpose: executes the create service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function create(UserAccountData $data): User|false
    {
        return $this->userService->generate($data->toArray());
    }

    /**
     * Purpose: executes the update service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function update(int $id, UserAccountData $data): bool
    {
        $parameters = $data->toArray();
        $notification = null;

        if (!in_array($id, config('commonconfig.fixed_users')) && $id !== Auth::id() && isset($parameters['user_role_management_id'])) {
            $this->user->update(['user_role_management_id' => $parameters['user_role_management_id']], $id);
            $notification = ['user_id' => $id, 'data' => __("Your account's role has been changed by admin.")];
        }

        $updated = $this->userInfo->update([
            'first_name' => $parameters['first_name'],
            'last_name' => $parameters['last_name'],
            'address' => $parameters['address'] ?? null,
        ], $id, 'user_id');

        if ($updated && $notification) {
            $this->notification->create($notification);
        }

        return (bool) $updated;
    }

    /**
     * Purpose: executes the update status service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function updateStatus(int $id, UserStatusData $data): array
    {
        if ($id === Auth::id()) {
            return [SERVICE_RESPONSE_WARNING, __('You cannot change your own status.')];
        }

        if (in_array($id, config('commonconfig.fixed_users'))) {
            return [SERVICE_RESPONSE_WARNING, __("You cannot change primary user's status.")];
        }

        $messages = [
            'is_email_verified' => __('Your email verification status has been changed by admin.'),
            'is_financial_active' => __("Your account's financial status has been changed by admin."),
            'is_accessible_under_maintenance' => __("Your account's maintenance mode access has been changed by admin."),
            'is_active' => __("Your account's status has been changed by admin."),
        ];

        $parameters = $data->toArray();
        $user = $this->user->getFirstById($id);

        if (!$this->user->update($parameters, $id)) {
            return [SERVICE_RESPONSE_ERROR, __('Failed to update user status.')];
        }

        $date = now();
        $notifications = [];

        foreach (array_keys($messages) as $field) {
            if ($user->{$field} != $parameters[$field]) {
                $notifications[] = ['user_id' => $id, 'data' => $messages[$field], 'created_at' => $date, 'updated_at' => $date];
            }
        }

        if (!empty($notifications)) {
            $this->notification->insert($notifications);
        }

        return [SERVICE_RESPONSE_SUCCESS, __('User status has been updated successfully.')];
    }

    /**
     * Purpose: executes the update wallet balance service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function updateWalletBalance(int $userId, int $walletId, WalletBalanceData $data): bool
    {
        return DB::transaction(function () use ($userId, $walletId, $data) {
            $wallet = $this->wallet->getFirstByConditions(['id' => $walletId, 'user_id' => $userId], ['stockItem']);

            if (empty($wallet)) {
                return false;
            }

            if (!$this->wallet->update(['primary_balance' => DB::raw('primary_balance + ' . $data->amount)], $walletId)) {
                return false;
            }

            $date = now();
            $this->transaction->insert([
                [
                    'user_id' => $wallet->user_id,
                    'stock_item_id' => $wallet->stock_item_id,
                    'model_name' => null,
                    'model_id' => null,
                    'transaction_type' => TRANSACTION_TYPE_DEBIT,
                    'amount' => bcmul($data->amount, '-1'),
                    'journal' => DECREASED_FROM_SYSTEM_ON_TRANSFER_BY_ADMIN,
                    'updated_at' => $date,
                    'created_at' => $date,
                ],
                [
                    'user_id' => $wallet->user_id,
                    'stock_item_id' => $wallet->stock_item_id,
                    'model_name' => get_class($wallet),
                    'model_id' => $wallet->id,
                    'transaction_type' => TRANSACTION_TYPE_CREDIT,
                    'amount' => bcmul($data->amount, '1'),
                    'journal' => INCREASED_TO_USER_WALLET_ON_TRANSFER_BY_ADMIN,
                    'updated_at' => $date,
                    'created_at' => $date,
                ],
            ]);

            $this->notification->create([
                'user_id' => $wallet->user_id,
                'data' => __("Your :currency wallet has been increased with :amount :currency by system.", [
                    'amount' => $data->amount,
                    'currency' => $wallet->stockItem->item,
                ]),
            ]);

            return true;
        });
    }
}
