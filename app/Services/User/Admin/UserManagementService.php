<?php

namespace App\Services\User\Admin;

use App\DTO\Admin\UserAccountData;
use App\DTO\Admin\UserStatusData;
use App\DTO\Admin\WalletBalanceData;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Repositories\User\Admin\Interfaces\TransactionInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\DataListService;
use App\Services\User\UserService;
use App\Models\User\User;
use App\Services\User\Trader\WalletService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserManagementService
{
    /**
     * Purpose: initializes the user management service.
     *
     * Action: receives repositories and helper services used to prepare admin user screens and apply user changes.
     */
    public function __construct(
        private readonly UserInterface $user,
        private readonly UserInfoInterface $userInfo,
        private readonly NotificationInterface $notification,
        private readonly WalletInterface $wallet,
        private readonly TransactionInterface $transaction,
        private readonly UserService $userService,
        private readonly UserRoleManagementInterface $userRoleManagement,
        private readonly WalletService $walletService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: prepares data for the admin user index page.
     *
     * Action: builds the filter metadata, loads the paginated user query, and formats it for the shared list view.
     *
     * @return array<string, mixed>
     */
    public function indexData(): array
    {
        $searchFields = $this->searchFields();
        $orderFields = $this->orderFields();
        $query = $this->user->paginateWithFilters(
            $searchFields,
            $orderFields,
            null,
            $this->userListSelect(),
            $this->userListJoinArray()
        );

        return [
            'list' => $this->dataListService->dataList($query, $searchFields, $orderFields),
            'title' => __('Users'),
        ];
    }

    /**
     * Purpose: prepares data for the admin user detail page.
     *
     * Action: loads the selected user and adds the page title used by the view.
     *
     * @return array<string, mixed>
     */
    public function showData(int|string $id): array
    {
        return [
            'user' => $this->user->findOrFailById($id),
            'title' => __('View User'),
        ];
    }

    /**
     * Purpose: prepares data for the user creation form.
     *
     * Action: loads active role options and the page title required by the create view.
     *
     * @return array<string, mixed>
     */
    public function createData(): array
    {
        return [
            'userRoleManagements' => $this->userRoleManagement->getUserRoles(),
            'title' => __('Create User'),
        ];
    }

    /**
     * Purpose: prepares data for the user edit form.
     *
     * Action: loads the selected user, active role options, and the page title required by the edit view.
     *
     * @return array<string, mixed>
     */
    public function editData(int|string $id): array
    {
        return [
            'user' => $this->user->findOrFailById($id),
            'userRoleManagements' => $this->userRoleManagement->getUserRoles(),
            'title' => __('Edit User'),
        ];
    }

    /**
     * Purpose: prepares data for the user status edit form.
     *
     * Action: loads the selected user and the title used on the status view.
     *
     * @return array<string, mixed>
     */
    public function editStatusData(int|string $id): array
    {
        return [
            'user' => $this->user->findOrFailById($id),
            'title' => __('Edit User Status'),
        ];
    }

    /**
     * Purpose: prepares data for the selected user's wallet list.
     *
     * Action: delegates wallet list filtering and pagination preparation to the wallet service.
     *
     * @return array<string, mixed>
     */
    public function walletsData(int|string $id): array
    {
        return [
            'list' => $this->walletService->getWallets($id),
            'title' => __('Wallets'),
        ];
    }

    /**
     * Purpose: prepares data for the wallet balance edit form.
     *
     * Action: loads the wallet by user and wallet identifiers and adds the form title.
     *
     * @return array<string, mixed>
     */
    public function editWalletBalanceData(int|string $id, int|string $walletId): array
    {
        return [
            'wallet' => $this->wallet->getFirstByConditions(['id' => $walletId, 'user_id' => $id]),
            'title' => __('Modify Wallet Balance'),
        ];
    }

    /**
     * Purpose: creates a user from validated request data.
     *
     * Action: converts the validated array to a DTO before executing the create workflow.
     *
     * @param array<string, mixed> $validated
     */
    public function createFromValidatedData(array $validated): User|false
    {
        return $this->create(UserAccountData::fromArray($validated));
    }

    /**
     * Purpose: updates a user from validated request data.
     *
     * Action: converts the validated array to a DTO before executing the update workflow.
     *
     * @param array<string, mixed> $validated
     */
    public function updateFromValidatedData(int|string $id, array $validated): bool
    {
        return $this->update((int) $id, UserAccountData::fromArray($validated));
    }

    /**
     * Purpose: updates status flags from validated request data.
     *
     * Action: converts the validated array to a DTO and normalizes the service response for flash messages.
     *
     * @param array<string, mixed> $validated
     * @return array{status: string, message: string}
     */
    public function updateStatusFromValidatedData(int|string $id, array $validated): array
    {
        [$status, $message] = $this->updateStatus((int) $id, UserStatusData::fromArray($validated));

        return $this->response($status, $message);
    }

    /**
     * Purpose: updates a wallet balance from validated request data.
     *
     * Action: converts the validated array to a DTO, executes the wallet update, and logs failures centrally.
     *
     * @param array<string, mixed> $validated
     * @return array{status: string, message: string}
     */
    public function updateWalletBalanceFromValidatedData(int|string $userId, int|string $walletId, array $validated): array
    {
        try {
            if ($this->updateWalletBalance((int) $userId, (int) $walletId, WalletBalanceData::fromArray($validated))) {
                return $this->response(SERVICE_RESPONSE_SUCCESS, __('The wallet balance has been updated successfully.'));
            }
        } catch (Throwable $exception) {
            logs()->error('Failed to update wallet balance: ' . $exception->getMessage());
        }

        return $this->response(SERVICE_RESPONSE_ERROR, __('Failed to update the wallet balance.'));
    }

    /**
     * Purpose: creates a user account.
     *
     * Action: delegates the full account generation workflow to the shared user service.
     *
     */
    public function create(UserAccountData $data): User|false
    {
        return $this->userService->generate($data->toArray());
    }

    /**
     * Purpose: updates user profile and role data.
     *
     * Action: applies editable user fields, protects fixed/self role changes, and notifies the user when their role changes.
     *
     */
    public function update(int $id, UserAccountData $data): bool
    {
        $parameters = $data->toArray();
        $notification = null;

        if (!$this->isFixedUser($id) && $id !== Auth::id() && isset($parameters['user_role_management_id'])) {
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
     * Purpose: updates account status flags.
     *
     * Action: prevents unsafe status changes, saves status values, and creates notifications for changed fields.
     *
     */
    public function updateStatus(int $id, UserStatusData $data): array
    {
        if ($id === Auth::id()) {
            return [SERVICE_RESPONSE_WARNING, __('You cannot change your own status.')];
        }

        if ($this->isFixedUser($id)) {
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
     * Purpose: updates a user's wallet balance from an admin transfer.
     *
     * Action: changes the wallet balance, stores paired transaction rows, and notifies the wallet owner in one transaction.
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

    /**
     * Purpose: defines searchable fields for the user admin list.
     *
     * Action: keeps list metadata in the service instead of duplicating it in controllers.
     *
     * @return array<int, array<int, string>>
     */
    private function searchFields(): array
    {
        return [
            ['username', __('Username')],
            ['email', __('Email')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];
    }

    /**
     * Purpose: defines sortable fields for the user admin list.
     *
     * Action: keeps order metadata next to the query preparation code.
     *
     * @return array<int, array<int, string>>
     */
    private function orderFields(): array
    {
        return [
            ['first_name', __('First Name')],
            ['users.id', __('Serial')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['username', __('Username')],
            ['users.created_at', __('Registered Date')],
        ];
    }

    /**
     * Purpose: defines joins required by the user admin list.
     *
     * Action: centralizes list query structure for users, roles, and profile data.
     *
     * @return array<int, array<int, string>>
     */
    private function userListJoinArray(): array
    {
        return [
            ['user_role_managements', 'user_role_managements.id', '=', 'users.user_role_management_id'],
            ['user_infos', 'user_infos.user_id', '=', 'users.id'],
        ];
    }

    /**
     * Purpose: defines selected columns required by the user admin list.
     *
     * Action: limits the list query to user, role, and profile columns used by the view.
     *
     * @return array<int, string>
     */
    private function userListSelect(): array
    {
        return ['users.*', 'role_name', 'first_name', 'last_name'];
    }

    /**
     * Purpose: checks whether a user is protected from sensitive admin edits.
     *
     * Action: normalizes configured fixed user IDs before performing a strict membership check.
     */
    private function isFixedUser(int $id): bool
    {
        return in_array($id, array_map('intval', config('commonconfig.fixed_users', [])), true);
    }

    /**
     * Purpose: builds a normalized flash-message response.
     *
     * Action: gives controllers a consistent shape for redirect status and message data.
     *
     * @return array{status: string, message: string}
     */
    private function response(string $status, string $message): array
    {
        return [
            'status' => $status,
            SERVICE_RESPONSE_MESSAGE => $message,
        ];
    }
}
