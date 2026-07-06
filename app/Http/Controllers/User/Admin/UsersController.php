<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWalletBalanceRequest;
use App\Http\Requests\Admin\UserRequest;
use App\Http\Requests\Admin\UserStatusRequest;
use App\Services\User\Admin\UserManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UsersController extends Controller
{
    /**
     * Purpose: initializes the user administration controller.
     *
     * Action: receives the management service that prepares user data and applies user changes.
     */
    public function __construct(
        private readonly UserManagementService $userManagementService,
    ) {
    }

    /**
     * Purpose: displays the admin user list.
     *
     * Action: delegates list filtering, sorting, and pagination data preparation to the service layer.
     */
    public function index(): View
    {
        return view('backend.users.index', $this->userManagementService->indexData());
    }

    /**
     * Purpose: displays a selected user's profile details.
     *
     * Action: asks the service to load the user and returns the read-only admin view.
     */
    public function show(int|string $id): View
    {
        return view('backend.users.show', $this->userManagementService->showData($id));
    }

    /**
     * Purpose: displays the user creation form.
     *
     * Action: asks the service for role options and other form data required to create a user.
     */
    public function create(): View
    {
        return view('backend.users.create', $this->userManagementService->createData());
    }

    /**
     * Purpose: stores a newly created user.
     *
     * Action: converts validated form input through the service layer and redirects to the created user on success.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        if ($user = $this->userManagementService->createFromValidatedData($request->validated())) {
            return redirect()->route('users.show', $user->id)->with(SERVICE_RESPONSE_SUCCESS, __("User has been created successfully."));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create user.'));
    }

    /**
     * Purpose: displays the user edit form.
     *
     * Action: asks the service to load the selected user and available role options.
     */
    public function edit(int|string $id): View
    {
        return view('backend.users.edit', $this->userManagementService->editData($id));
    }

    /**
     * Purpose: updates a user's account information.
     *
     * Action: passes validated input to the service and redirects back with the operation status.
     */
    public function update(UserRequest $request, int|string $id): RedirectResponse
    {
        if ($this->userManagementService->updateFromValidatedData($id, $request->validated())) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('User has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user'));
    }

    /**
     * Purpose: displays the user status edit form.
     *
     * Action: asks the service to load the user whose access and verification statuses will be edited.
     */
    public function editStatus(int|string $id): View
    {
        return view('backend.users.edit_status', $this->userManagementService->editStatusData($id));
    }

    /**
     * Purpose: updates a user's account status flags.
     *
     * Action: passes validated status input to the service and returns the service result to the edit screen.
     */
    public function updateStatus(UserStatusRequest $request, int|string $id): RedirectResponse
    {
        $response = $this->userManagementService->updateStatusFromValidatedData($id, $request->validated());

        return redirect()->route('users.edit.status', $id)->with($response['status'], $response[SERVICE_RESPONSE_MESSAGE]);
    }

    /**
     * Purpose: displays wallets that belong to a selected user.
     *
     * Action: delegates wallet list preparation to the service and returns the wallet index view.
     */
    public function wallets(int|string $id): View
    {
        return view('backend.users.wallets.index', $this->userManagementService->walletsData($id));
    }

    /**
     * Purpose: displays the wallet balance adjustment form.
     *
     * Action: asks the service to load the user's wallet and returns the edit view.
     */
    public function editWalletBalance(int|string $id, int|string $walletId): View
    {
        return view('backend.users.wallets.edit', $this->userManagementService->editWalletBalanceData($id, $walletId));
    }

    /**
     * Purpose: updates a user's wallet balance from an admin action.
     *
     * Action: passes validated wallet amount input to the service and redirects back with the result.
     */
    public function updateWalletBalance(UpdateWalletBalanceRequest $request, int|string $id, int|string $walletId): RedirectResponse
    {
        $response = $this->userManagementService->updateWalletBalanceFromValidatedData($id, $walletId, $request->validated());

        return redirect()->back()->with($response['status'], $response[SERVICE_RESPONSE_MESSAGE]);
    }
}
