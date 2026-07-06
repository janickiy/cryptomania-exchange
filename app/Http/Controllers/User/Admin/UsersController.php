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
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class UsersController extends Controller
{
    /**
     * Purpose: initializes the UsersController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly UserInterface $user,
        private readonly UserRoleManagementInterface $userRoleManagement,
        private readonly WalletInterface $wallets,
        private readonly UserManagementService $userManagementService,
        private readonly WalletService $walletService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(): View|Factory|Application
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
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Users');

        return view('backend.users.index', $data);
    }

    /**
     * Purpose: shows the detail page for the selected record.
     *
     * Action: loads the record by identifier and passes it to the view.
     *
     */
    public function show(int|string $id): View
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['title'] = __('View User');

        return view('backend.users.show', $data);
    }

    /**
     * Purpose: shows the form for creating a new record.
     *
     * Action: prepares form data and returns the create view.
     *
     */
    public function create(): View
    {
        $data['userRoleManagements'] = $this->userRoleManagement->getUserRoles();
        $data['title'] = __('Create User');

        return view('backend.users.create', $data);
    }

    /**
     * Purpose: creates a new record from request data.
     *
     * Action: passes validated data to the service layer and returns the operation result.
     *
     */
    public function store(UserRequest $request): RedirectResponse
    {
        if ($user = $this->userManagementService->create(UserAccountData::fromArray($request->validated()))) {
            return redirect()->route('users.show', $user->id)->with(SERVICE_RESPONSE_SUCCESS, __("User has been created successfully."));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create user.'));
    }

    /**
     * Purpose: shows the edit form for the selected record.
     *
     * Action: loads current data and returns the edit view.
     *
     */
    public function edit(int|string $id): View
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['userRoleManagements'] = $this->userRoleManagement->getUserRoles();
        $data['title'] = __('Edit User');

        return view('backend.users.edit', $data);
    }

    /**
     * Purpose: updates the selected record from request data.
     *
     * Action: passes changes to the service layer and returns a result message.
     * @param UserRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function update(UserRequest $request, int|string $id): RedirectResponse
    {
        if ($this->userManagementService->update((int) $id, UserAccountData::fromArray($request->validated()))) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('User has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user'));
    }

    /**
     * Purpose: handles the edit status action in UsersController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     * @param int|string $id
     * @return View
     */
    public function editStatus(int|string $id): View
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['title'] = __('Edit User Status');

        return view('backend.users.edit_status', $data);
    }

    /**
     * Purpose: handles the update status action in UsersController.
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     * param UserStatusRequest $request
     *
     * @param UserStatusRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updateStatus(UserStatusRequest $request, int|string $id): RedirectResponse
    {
        [$status, $message] = $this->userManagementService->updateStatus((int) $id, UserStatusData::fromArray($request->validated()));

        return redirect()->route('users.edit.status', $id)->with($status, $message);
    }

    /**
     * Purpose: handles the wallets action in UsersController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     */
    public function wallets(int|string $id): View
    {
        $data['list'] = $this->walletService->getWallets($id);
        $data['title'] = __('Wallets');

        return view('backend.users.wallets.index', $data);
    }

    /**
     * Purpose: handles the edit wallet balance action in UsersController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     *
     * @param int|string $id
     * @param int|string $walletId
     * @return View
     */
    public function editWalletBalance(int|string $id, int|string $walletId): View
    {
        $data['wallet'] = $this->wallets->getFirstByConditions(['id' => $walletId, 'user_id' => $id]);
        $data['title'] = __('Modify Wallet Balance');

        return view('backend.users.wallets.edit', $data);
    }

    /**
     * Purpose: handles the update wallet balance action in UsersControlle
     *
     * Action: connects the HTTP request to services or views so the controller remains thin
     *
     * @param UpdateWalletBalanceRequest $request
     * @param int|string $id
     * @param int|string $walletId
     * @return RedirectResponse
     */
    public function updateWalletBalance(UpdateWalletBalanceRequest $request, int|string $id, int|string $walletId): RedirectResponse
    {
        try {
            if ($this->userManagementService->updateWalletBalance((int) $id, (int) $walletId, WalletBalanceData::fromArray($request->validated()))) {
                return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The wallet balance has been updated successfully.'));
            }
        } catch (\Exception $exception) {
            logs()->error('Failed to update wallet balance: ' . $exception->getMessage());
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to update the wallet balance.'));
    }
}
