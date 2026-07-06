<?php

namespace App\Http\Controllers\Core;

use App\DTO\Admin\UserRoleManagementData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRoleManagementRequest;
use App\Models\Core\UserRoleManagement;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Services\Core\DataListService;
use App\Services\User\Admin\UserRoleManagementService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserRoleManagementsController extends Controller
{
    /**
     * Purpose: initializes the UserRoleManagementsController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(
        private readonly UserRoleManagementInterface $roleManagement,
        private readonly UserRoleManagementService $roleManagementService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     */
    public function index(): View
    {
        $searchFields = $this->searchFields();
        $orderFields = $this->orderFields();

        return view('backend.userRoleManagements.index', [
            'list' => $this->dataListService->dataList(
                $this->roleManagement->paginateWithFilters($searchFields, $orderFields),
                $searchFields,
                $orderFields
            ),
            'title' => __('Role Management'),
            'defaultRoles' => $this->defaultRoles(),
        ]);
    }

    /**
     * Purpose: shows the form for creating a new record.
     *
     * Action: prepares form data and returns the create view.
     */
    public function create(): View
    {
        return view('backend.userRoleManagements.create', $this->formData(__('Create User Role')));
    }

    /**
     * Purpose: creates a new record from request data.
     *
     * Action: passes validated data to the service layer and returns the operation result.
     */
    public function store(UserRoleManagementRequest $request): RedirectResponse
    {
        return $this->createdRoleResponse(
            $this->roleManagementService->create($this->roleData($request))
        );
    }

    /**
     * Purpose: shows the edit form for the selected record.
     *
     * Action: loads current data and returns the edit view.
     */
    public function edit(int|string $id): View
    {
        return view('backend.userRoleManagements.edit', $this->formData(__('Edit User Role'), [
            'userRoleManagement' => $this->roleManagement->findOrFailById($id),
        ]));
    }

    /**
     * Purpose: updates the selected record from request data.
     *
     * Action: passes changes to the service layer and returns a result message.
     */
    public function update(UserRoleManagementRequest $request, int|string $id): RedirectResponse
    {
        return $this->editResponse(
            $this->roleManagementService->update((int) $id, $this->roleData($request)),
            $id,
            __('User role has been updated successfully.'),
            __('Failed to update user role.')
        );
    }

    /**
     * Purpose: deletes the selected record.
     *
     * Action: performs deletion through a service or repository and redirects back with the result.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        return $this->indexResponse(
            $this->roleManagement->deleteById((int) $id),
            __('User role has been deleted successfully.'),
            __('This role cannot be deleted.')
        );
    }

    /**
     * Purpose: handles the change status action in UserRoleManagementsController.
     *
     * Action: connects the HTTP request to services or views so the controller remains thin.
     */
    public function changeStatus(int|string $id): RedirectResponse
    {
        return $this->statusResponse($this->roleManagement->toggleStatusById((int) $id));
    }

    /**
     * Purpose: returns fields available for user role search.
     *
     * Action: keeps filter field definitions out of the page action.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function searchFields(): array
    {
        return [
            ['role_name', __('Role Name')],
        ];
    }

    /**
     * Purpose: returns fields available for user role sorting.
     *
     * Action: keeps sort field definitions out of the page action.
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function orderFields(): array
    {
        return [
            ['id', __('Serial')],
            ['role_name', __('Role Name')],
        ];
    }

    /**
     * Purpose: returns role IDs that should be treated as fixed roles in the list view.
     *
     * Action: reads the fixed roles config and normalizes invalid configuration to an empty array.
     *
     * @return array<int, int>
     */
    private function defaultRoles(): array
    {
        $roles = config('commonconfig.fixed_roles', []);

        return is_array($roles) ? $roles : [];
    }

    /**
     * Purpose: prepares shared data for user role create and edit forms.
     *
     * Action: merges page title, configurable routes, and optional view data.
     *
     * @param array{userRoleManagement?: UserRoleManagement} $extra
     * @return array{title: string, routes: array<string, array<string, array<string, array<int, string>>>>, userRoleManagement?: UserRoleManagement}
     */
    private function formData(string $title, array $extra = []): array
    {
        return array_merge([
            'title' => $title,
            'routes' => $this->configurableRoutes(),
        ], $extra);
    }

    /**
     * Purpose: returns routes that can be assigned to a user role.
     *
     * Action: reads route permissions from configuration and falls back to an empty list when unavailable.
     *
     * @return array<string, array<string, array<string, array<int, string>>>>
     */
    private function configurableRoutes(): array
    {
        $routes = config('permissionRoutes.configurable_routes', []);

        return is_array($routes) ? $routes : [];
    }

    /**
     * Purpose: converts validated request data into a DTO.
     *
     * Action: keeps transport data creation in one controller helper.
     */
    private function roleData(UserRoleManagementRequest $request): UserRoleManagementData
    {
        return UserRoleManagementData::fromArray($request->validated());
    }

    /**
     * Purpose: redirects after creating a user role.
     *
     * Action: sends the admin to the edit page on success or back to the form on failure.
     */
    private function createdRoleResponse(UserRoleManagement|false $role): RedirectResponse
    {
        if ($role) {
            return redirect()
                ->route('user-role-managements.edit', $role->id)
                ->with(SERVICE_RESPONSE_SUCCESS, __('User role has been created successfully.'));
        }

        return redirect()
            ->back()
            ->with(SERVICE_RESPONSE_ERROR, __('Failed to create user role.'));
    }

    /**
     * Purpose: redirects after updating a user role.
     *
     * Action: returns a success redirect to the edit page or an error redirect back to the form.
     */
    private function editResponse(bool $success, int|string $id, string $successMessage, string $errorMessage): RedirectResponse
    {
        if ($success) {
            return redirect()
                ->route('user-role-managements.edit', $id)
                ->with(SERVICE_RESPONSE_SUCCESS, $successMessage);
        }

        return redirect()
            ->back()
            ->with(SERVICE_RESPONSE_ERROR, $errorMessage);
    }

    /**
     * Purpose: redirects after a user role index operation.
     *
     * Action: returns a success redirect to the list or an error redirect back to the current page.
     */
    private function indexResponse(bool $success, string $successMessage, string $errorMessage): RedirectResponse
    {
        if ($success) {
            return redirect()
                ->route('user-role-managements.index')
                ->with(SERVICE_RESPONSE_SUCCESS, $successMessage);
        }

        return redirect()
            ->back()
            ->with(SERVICE_RESPONSE_ERROR, $errorMessage);
    }

    /**
     * Purpose: redirects after toggling a user role status.
     *
     * Action: formats the activated or deactivated state message returned by the repository.
     */
    private function statusResponse(string|false $updatedState): RedirectResponse
    {
        if ($updatedState) {
            return redirect()
                ->route('user-role-managements.index')
                ->with(SERVICE_RESPONSE_SUCCESS, __('User role has been :state successfully', ['state' => $updatedState]));
        }

        return redirect()
            ->back()
            ->with(SERVICE_RESPONSE_ERROR, __('User role status can not be changed'));
    }
}
