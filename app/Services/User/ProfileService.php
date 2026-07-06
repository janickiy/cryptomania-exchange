<?php

namespace App\Services\User;

use App\Http\Requests\Core\PasswordUpdateRequest;
use App\Http\Requests\User\UserAvatarRequest;
use App\Repositories\Exchange\Interfaces\StockExchangeInterface;
use App\Repositories\User\Trader\Interfaces\StockOrderInterface;
use App\Repositories\User\Interfaces\NotificationInterface;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Interfaces\UserSettingInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\FileUploadService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Purpose: executes the profile service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function profile(): array
    {
        $data['user'] = Auth::user()->load('userRoleManagement');

        return $data;
    }

    /**
     * Purpose: executes the update password service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param PasswordUpdateRequest $request
     * @return array
     */
    public function updatePassword(PasswordUpdateRequest $request): array
    {
        $update = ['password' => Hash::make($request->new_password)];

        if (app(UserInterface::class)->update($update, Auth::id())) {
            $notification = ['user_id' => Auth::id(), 'data' => __("You just changed your account's password.")];
            app(NotificationInterface::class)->create($notification);

            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('Password has been changed successfully.')
            ];
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Failed to change password.')
        ];
    }

    /**
     * Purpose: executes the update personal info service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param array<string, mixed> $parameters
     * @return array<string, mixed>
     */
    public function updatePersonalInfo(array $parameters): array
    {
        if (app(UserInfoInterface::class)->update($parameters, Auth::id(), 'user_id')) {
            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('Profile has been updated successfully.'),
            ];
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Failed to update profile.'),
        ];
    }

    /**
     * Purpose: executes the update settings service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param array<string, mixed> $parameters
     * @return array<string, mixed>
     */
    public function updateSettings(array $parameters): array
    {
        if (app(UserSettingInterface::class)->update($parameters, Auth::id(), 'user_id')) {
            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('User setting has been updated successfully.'),
            ];
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Failed to update user setting.'),
        ];
    }

    /**
     * Purpose: executes the generate referral link service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @return array<string, mixed>
     */
    public function generateReferralLink(): array
    {
        $user = Auth::user();

        if (empty($user->referral_code)) {
            app(UserInterface::class)->update(['referral_code' => $user->id . random_string(8)], $user->id);

            return [
                SERVICE_RESPONSE_STATUS => true,
                SERVICE_RESPONSE_MESSAGE => __('Referral link has been generated successfully.'),
            ];
        }

        return [
            SERVICE_RESPONSE_STATUS => true,
            SERVICE_RESPONSE_MESSAGE => null,
        ];
    }

    /**
     * Purpose: executes the avatar upload service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param UserAvatarRequest $request
     * @return array
     */
    public function avatarUpload(UserAvatarRequest $request): array
    {
        $uploadedAvatar = app(FileUploadService::class)->upload($request->file('avatar'), config('commonconfig.path_profile_image'), 'avatar', 'user', Auth::id(), 'public', 300, 300);

        if ($uploadedAvatar) {
            $parameters = ['avatar' => $uploadedAvatar];

            if (app(UserInterface::class)->update($parameters, Auth::id())) {
                return [
                    SERVICE_RESPONSE_STATUS => true,
                    SERVICE_RESPONSE_MESSAGE => __('Avatar has been uploaded successfully.')
                ];
            }
        }

        return [
            SERVICE_RESPONSE_STATUS => false,
            SERVICE_RESPONSE_MESSAGE => __('Failed to upload the avatar.')
        ];
    }

    /**
     * Purpose: executes the user related info service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param $userId
     * @return array
     */
    public function userRelatedInfo(mixed $userId): array
    {
        $totalWallets = app(WalletInterface::class)->count(['user_id' => $userId]);
        $totalOpenOrders = app(StockOrderInterface::class)->count(['user_id' => $userId, 'status' => STOCK_ORDER_PENDING]);
        $totalTrades = app(StockExchangeInterface::class)->count(['user_id' => $userId]);

        return [
            'totalWallets' => $totalWallets,
            'totalOpenOrders' => $totalOpenOrders,
            'totalTrades' => $totalTrades,
        ];
    }

    /**
     * Purpose: executes the routes for admin service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param $userId
     * @return array
     */
    public function routesForAdmin(mixed $userId): array
    {
        $userRelatedInfo = $this->userRelatedInfo($userId);

        $info = [
            'walletRouteName' => 'admin.users.wallets',
            'walletRoute' => route('admin.users.wallets', ['id' => $userId]),
            'openOrderRouteName' => 'reports.admin.open-orders',
            'openOrderRoute' => route('reports.admin.open-orders', ['userId' => $userId]),
            'tradeHistoryRouteName' => 'reports.admin.trades',
            'tradeHistoryRoute' => route('reports.admin.trades', ['userId' => $userId]),
        ];

        return array_merge($userRelatedInfo, $info);
    }

    /**
     * Purpose: executes the routes for user service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     * @param $userId
     * @return array
     */
    public function routesForUser(mixed $userId): array
    {
        $userRelatedInfo = $this->userRelatedInfo($userId);

        $info = [
            'walletRouteName' => 'trader.wallets.index',
            'walletRoute' => route('trader.wallets.index'),
            'openOrderRouteName' => 'trader.orders.open-orders',
            'openOrderRoute' => route('trader.orders.open-orders'),
            'tradeHistoryRouteName' => 'reports.trader.trades',
            'tradeHistoryRoute' => route('reports.trader.trades'),
        ];

        return array_merge($userRelatedInfo, $info);
    }
}
