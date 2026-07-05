<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\User\Admin\ReportsService;
use App\Services\User\Admin\WithdrawalReviewService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class WithdrawalController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела заявок на вывод средств.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly WithdrawalInterface $withdrawalRepository,
        private readonly ReportsService $reportsService,
        private readonly WithdrawalReviewService $withdrawalReviewService,
    ) {
    }

    /**
     * Назначение: показывает основную страницу или список раздела заявок на вывод средств.
     *
     * Действие: запрашивает нужные данные через сервисы или репозитории, формирует данные для view и возвращает представление.
     */
    public function index(): View|Factory|Application
    {
        $data['list'] = $this->reportsService->withdrawals(null, null, 'reviewing');
        $data['title'] = __('Withdrawals for Reviewing');

        return view('backend.review_withdrawals.withdrawal', $data);
    }

    /**
     * Назначение: показывает детальную страницу записи в разделе заявок на вывод средств.
     *
     * Действие: находит запись по идентификатору, подготавливает связанные данные и возвращает представление просмотра.
     */
    public function show(int|string $id): View|Factory|Application
    {
        $data['title'] = __('Review Withdrawal');
        $data['withdrawal'] = $this->withdrawalRepository->findOrfailById($id, ['stockItem', 'wallet', 'user', 'user.userinfo']);
        $data['user'] = $data['withdrawal']->user;

        return view('backend.review_withdrawals.show', $data);
    }

    /**
     * Назначение: подтверждает запись в разделе заявок на вывод средств.
     *
     * Действие: передает идентификатор в сервис проверки, меняет статус записи и возвращает результат.
     */
    public function approve(int|string $id): RedirectResponse
    {
        if ($this->withdrawalReviewService->approve((int) $id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The withdrawal has been approved successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid Request.'));
    }

    /**
     * Назначение: отклоняет запись в разделе заявок на вывод средств.
     *
     * Действие: передает идентификатор в сервис проверки, меняет статус записи и возвращает результат.
     */
    public function decline(int|string $id): RedirectResponse
    {
        if ($this->withdrawalReviewService->decline((int) $id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The withdrawal has been declined successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid Request.'));
    }
}
