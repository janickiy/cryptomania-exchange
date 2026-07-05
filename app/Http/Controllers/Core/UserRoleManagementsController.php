<?php

namespace App\Http\Controllers\Core;

use App\DTO\Admin\UserRoleManagementData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRoleManagementRequest;
use App\Repositories\Core\Interfaces\UserRoleManagementInterface;
use App\Services\Core\DataListService;
use App\Services\User\Admin\UserRoleManagementService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;


class UserRoleManagementsController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела ролей и прав доступа.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly UserRoleManagementInterface $roleManagement,
        private readonly UserRoleManagementService $roleManagementService,
        private readonly DataListService $dataListService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела ролей и прав доступа.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $searchFields = [
            ['role_name', __('Role Name')],
        ];
        $orderFields = [
            ['id', __('Serial')],
            ['role_name', __('Role Name')],
        ];

        $query = $this->roleManagement->paginateWithFilters($searchFields, $orderFields);
        $data['list'] = $this->dataListService->dataList($query, $searchFields, $orderFields);
        $data['title'] = __('Role Management');
        $data['defaultRoles'] = config('commonconfig.fixed_roles');
        if (!is_array($data['defaultRoles'])) {
            $data['defaultRoles'] = [];
        }

        return view('backend.userRoleManagements.index', $data);
    }

    /**
     * Назначение: показывает форму создания записи в разделе ролей и прав доступа.
     *
     * Действие: подготавливает справочные данные для формы и возвращает представление создания.
     */
    public function create(): View|Factory|Application
    {
        $data['routes'] = config('permissionRoutes.configurable_routes');
        $data['title'] = __('Create User Role');

        return view('backend.userRoleManagements.create', $data);
    }

    /**
     * Назначение: создает новую запись в разделе ролей и прав доступа.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(UserRoleManagementRequest $request): RedirectResponse
    {
        if ($userRoleManagement = $this->roleManagementService->create(UserRoleManagementData::fromArray($request->validated()))) {
            cache()->forever("userRoleManagement" . $userRoleManagement->id, $userRoleManagement->route_group);
            return redirect()->route('user-role-managements.edit', $userRoleManagement->id)->with(SERVICE_RESPONSE_SUCCESS, __('User role has been created successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to create user role.'));
    }

    /**
     * Назначение: показывает форму редактирования записи в разделе ролей и прав доступа.
     *
     * Действие: загружает запись и справочные данные, затем возвращает представление формы редактирования.
     */
    public function edit(int|string $id): View|Factory|Application
    {
        $data['routes'] = config('permissionRoutes.configurable_routes');
        $data['userRoleManagement'] = $this->roleManagement->findOrFailById($id);
        $data['title'] = __('Edit User Role');

        return view('backend.userRoleManagements.edit', $data);
    }

    /**
     * Назначение: обновляет запись в разделе ролей и прав доступа.
     *
     * Действие: принимает валидированный запрос, передает изменения в сервис или репозиторий и возвращает ответ с результатом.
     */
    public function update(UserRoleManagementRequest $request, int|string $id): RedirectResponse
    {
        if ($this->roleManagementService->update((int) $id, UserRoleManagementData::fromArray($request->validated()))) {
            return redirect()->route('user-role-managements.edit', $id)->with(SERVICE_RESPONSE_SUCCESS, __('User role has been updated successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user role.'));
    }

    /**
     * Назначение: удаляет запись в разделе ролей и прав доступа.
     *
     * Действие: проверяет возможность удаления, выполняет операцию через сервис или репозиторий и возвращает результат.
     */
    public function destroy(int|string $id): RedirectResponse
    {
        if ($this->roleManagement->deleteById($id)) {
            return redirect()->route('user-role-managements.index')->with(SERVICE_RESPONSE_SUCCESS, __('User role has been deleted successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('This role cannot be deleted.'));
    }

    /**
     * Назначение: изменяет статус записи в разделе ролей и прав доступа.
     *
     * Действие: переключает статус через сервисный слой и возвращает результат операции.
     */
    public function changeStatus(int|string $id): RedirectResponse
    {
        if ($updatedState = $this->roleManagement->toggleStatusById($id)) {
            return redirect()->route('user-role-managements.index')->with(SERVICE_RESPONSE_SUCCESS, __('User role has been :state successfully', ['state' => $updatedState]));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('User role status can not be changed'));
    }
}
