<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\DTO\Admin\UserAccountData;
use App\DTO\Admin\UserStatusData;
use App\DTO\Admin\WalletBalanceData;
use App\Http\Requests\Admin\UpdateWalletBalanceRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\Admin\UserStatusRequest;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Repositories\User\Interfaces\UserInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\Core\DataListService;
use App\Services\User\Admin\UserManagementService;
use App\Services\User\Trader\WalletService;

class UsersController extends Controller
{
    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $searchFields = [
            ['username', __('Username')],
            ['email', __('Email')],
            ['first_name', __('First Name')],
            ['last_name', __('Last Name')],
        ];
        $orderFields = [
            ['first_name', __('First Name')],
            ['users.id', __('Serial')],
            ['last_name', __('Last Name')],
            ['email', __('Email')],
            ['username', __('Username')],
            ['users.created_at', __('Registered Date')],
        ];
        $joinArray = [
            ['user_role_managements', 'user_role_managements.id', '=', 'users.user_role_management_id'],
            ['user_infos', 'user_infos.user_id', '=', 'users.id'],
        ];
        $select = [
            'users.*', 'role_name', 'first_name', 'last_name'
        ];

        $query = $this->user->paginateWithFilters($searchFields, $orderFields, null, $select, $joinArray);
        $data['list'] = app(DataListService::class)->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Users');

        return view('backend.users.index', $data);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show($id)
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['title'] = __('View User');

        return view('backend.users.show', $data);
    }

    public function create()
    {
        $data['userRoleManagements'] = app(UserRoleManagementInterface::class)->getUserRoles();
        $data['title'] = __('Create User');

        return view('backend.users.create', $data);
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request)
    {
        if ($user = app(UserManagementService::class)->create(UserAccountData::fromArray($request->validated()))) {
            return redirect()->route('users.show', $user->id)->with(SERVICE_RESPONSE_SUCCESS, __("User has been created successfully."));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create user.'));
    }

    /**
     * @param $id
     * @param UserRoleManagementInterface $userRoleManagement
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function edit($id, UserRoleManagementInterface $userRoleManagement)
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['userRoleManagements'] = $userRoleManagement->getUserRoles();
        $data['title'] = __('Edit User');

        return view('backend.users.edit', $data);
    }

    /**
     * @param UserRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, $id)
    {
        if (app(UserManagementService::class)->update((int) $id, UserAccountData::fromArray($request->validated()))) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('User has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function editStatus($id)
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['title'] = __('Edit User Status');

        return view('backend.users.edit_status', $data);
    }

    /**
     * @param UserStatusRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(UserStatusRequest $request, $id)
    {
        [$status, $message] = app(UserManagementService::class)->updateStatus((int) $id, UserStatusData::fromArray($request->validated()));

        return redirect()->route('users.edit.status', $id)->with($status, $message);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function wallets($id)
    {
        $data['list'] = app(WalletService::class)->getWallets($id);
        $data['title'] = __('Wallets');

        return view('backend.users.wallets.index', $data);
    }

    /**
     * @param $id
     * @param $walletId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function editWalletBalance($id, $walletId)
    {
        $data['wallet'] = app(WalletInterface::class)->getFirstByConditions(['id' => $walletId, 'user_id' => $id]);
        $data['title'] = __('Modify Wallet Balance');

        return view('backend.users.wallets.edit', $data);
    }

    /**
     * @param UpdateWalletBalanceRequest $request
     * @param $id
     * @param $walletId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateWalletBalance(UpdateWalletBalanceRequest $request, $id, $walletId)
    {
        try {
            if (app(UserManagementService::class)->updateWalletBalance((int) $id, (int) $walletId, WalletBalanceData::fromArray($request->validated()))) {
                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The wallet balance has been updated successfully.'));
            }
        } catch (\Exception $exception) {
            logs()->error('Failed to update wallet balance: ' . $exception->getMessage());
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to update the wallet balance.'));
    }
}
