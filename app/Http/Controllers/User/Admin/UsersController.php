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
     * Назначение: инициализирует контроллер раздела пользователей.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
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
     * Назначение: показывает основную страницу или список раздела пользователей.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
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
     * Назначение: показывает детальную страницу записи в разделе пользователей.
     *
     * Действие: находит запись по идентификатору, подготавливает связанные данные и возвращает представление просмотра.
     */
    public function show(int|string $id): View|Factory|Application
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['title'] = __('View User');

        return view('backend.users.show', $data);
    }

    /**
     * Назначение: показывает форму создания записи в разделе пользователей.
     *
     * Действие: подготавливает справочные данные для формы и возвращает представление создания.
     */
    public function create(): View|Factory|Application
    {
        $data['userRoleManagements'] = $this->userRoleManagement->getUserRoles();
        $data['title'] = __('Create User');

        return view('backend.users.create', $data);
    }

    /**
     * Назначение: создает новую запись в разделе пользователей.
     *
     * Действие: принимает валидированный запрос, передает данные в сервис или репозиторий и возвращает результат операции.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        if ($user = $this->userManagementService->create(UserAccountData::fromArray($request->validated()))) {
            return redirect()->route('users.show', $user->id)->with(SERVICE_RESPONSE_SUCCESS, __("User has been created successfully."));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to create user.'));
    }

    /**
     * Назначение: показывает форму редактирования записи в разделе пользователей.
     *
     * Действие: загружает запись и справочные данные, затем возвращает представление формы редактирования.
     */
    public function edit(int|string $id): View|Factory|Application
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['userRoleManagements'] = $this->userRoleManagement->getUserRoles();
        $data['title'] = __('Edit User');

        return view('backend.users.edit', $data);
    }

    /**
     * Назначение: обновляет запись в разделе пользователей.
     *
     * Действие: принимает валидированный запрос, передает изменения в сервис или репозиторий и возвращает ответ с результатом.
     */
    public function update(UserRequest $request, int|string $id): RedirectResponse
    {
        if ($this->userManagementService->update((int) $id, UserAccountData::fromArray($request->validated()))) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('User has been updated successfully.'));
        }

        return redirect()->back()->withInput()->with(SERVICE_RESPONSE_ERROR, __('Failed to update user'));
    }

    /**
     * Назначение: показывает форму изменения статуса пользователя.
     *
     * Действие: загружает пользователя и возвращает административную форму статусов.
     */
    public function editStatus(int|string $id): View|Factory|Application
    {
        $data['user'] = $this->user->findOrFailById($id);
        $data['title'] = __('Edit User Status');

        return view('backend.users.edit_status', $data);
    }

    /**
     * Назначение: обновляет статус пользователя.
     *
     * Действие: принимает валидированные статусы аккаунта, передает их в сервис и возвращает результат.
     */
    public function updateStatus(UserStatusRequest $request, int|string $id): RedirectResponse
    {
        [$status, $message] = $this->userManagementService->updateStatus((int) $id, UserStatusData::fromArray($request->validated()));

        return redirect()->route('users.edit.status', $id)->with($status, $message);
    }

    /**
     * Назначение: показывает кошельки выбранного пользователя.
     *
     * Действие: загружает пользователя и связанные кошельки, затем возвращает административное представление.
     */
    public function wallets(int|string $id): View|Factory|Application
    {
        $data['list'] = $this->walletService->getWallets($id);
        $data['title'] = __('Wallets');

        return view('backend.users.wallets.index', $data);
    }

    /**
     * Назначение: показывает форму изменения баланса кошелька.
     *
     * Действие: загружает пользователя и кошелек, затем возвращает форму ручной корректировки баланса.
     */
    public function editWalletBalance(int|string $id, int|string $walletId): View|Factory|Application
    {
        $data['wallet'] = $this->wallets->getFirstByConditions(['id' => $walletId, 'user_id' => $id]);
        $data['title'] = __('Modify Wallet Balance');

        return view('backend.users.wallets.edit', $data);
    }

    /**
     * Назначение: обновляет баланс кошелька пользователя.
     *
     * Действие: принимает валидированную корректировку, передает ее в сервис и возвращает результат операции.
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
