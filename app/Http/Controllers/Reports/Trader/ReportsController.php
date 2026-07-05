<?php

namespace App\Http\Controllers\Reports\Trader;

use App\Http\Controllers\Controller;
use App\Repositories\User\Interfaces\UserInfoInterface;
use App\Repositories\User\Trader\Interfaces\WalletInterface;
use App\Services\User\Admin\ReportsService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReportsController extends Controller
{
    /**
     * Назначение: инициализирует контроллер раздела отчетов.
     *
     * Действие: получает зависимости из DI-контейнера Laravel и сохраняет их для обработки запросов.
     */
    public function __construct(
        private readonly ReportsService $reportsService,
        private readonly WalletInterface $wallets,
        private readonly UserInfoInterface $userInfo,
    ) {
    }

    /**
     * Назначение: показывает общий отчет по депозитам.
     *
     * Действие: получает список депозитов с учетом типа транзакции и возвращает отчетное представление.
     */
    public function allDeposits(?string $paymentTransactionType = null): View|Factory|Application
    {
        $data['list'] = $this->reportsService->deposits(Auth::id(), null, $paymentTransactionType);
        $data['title'] = __('Deposits');
        $data['status'] = $paymentTransactionType;

        return view('frontend.reports.all_deposit', $data);
    }

    /**
     * Назначение: показывает отчет по депозитам конкретного пользователя.
     *
     * Действие: фильтрует депозиты по пользователю и типу транзакции, затем возвращает отчет.
     */
    public function deposits(int|string $id, ?string $paymentTransactionType = null): View|Factory|Application
    {
        $data['wallet'] = $this->wallets->firstOrFail(['id' => $id, 'user_id' => Auth::id()], 'stockItem');
        $data['list'] = $this->reportsService->deposits(Auth::id(), $id, $paymentTransactionType);
        $data['title'] = __('Deposits');
        $data['status'] = $paymentTransactionType;

        return view('frontend.reports.deposit', $data);
    }

    /**
     * Назначение: показывает общий отчет по выводам средств.
     *
     * Действие: получает список выводов с учетом типа транзакции и возвращает отчетное представление.
     */
    public function allWithdrawals(?string $paymentTransactionType = null): View|Factory|Application
    {
        $data['list'] = $this->reportsService->withdrawals(Auth::id(), null, $paymentTransactionType);
        $data['title'] = __('Withdrawals');
        $data['status'] = $paymentTransactionType;

        return view('frontend.reports.all_withdrawal', $data);
    }

    /**
     * Назначение: показывает отчет по выводам конкретного пользователя.
     *
     * Действие: фильтрует выводы по пользователю и типу транзакции, затем возвращает отчет.
     */
    public function withdrawals(int|string $id, ?string $paymentTransactionType = null): View|Factory|Application
    {
        $data['wallet'] = $this->wallets->firstOrFail(['id' => $id, 'user_id' => Auth::id()], 'stockItem');
        $data['list'] = $this->reportsService->withdrawals(Auth::id(), $id, $paymentTransactionType);
        $data['title'] = __('Withdrawals');
        $data['status'] = $paymentTransactionType;

        return view('frontend.reports.withdrawal', $data);
    }

    /**
     * Назначение: показывает отчет по сделкам.
     *
     * Действие: фильтрует торговые операции по пользователю или категории и возвращает отчет.
     */
    public function trades(?string $categoryType = null): View|Factory|Application
    {
        $data['list'] = $this->reportsService->trades(Auth::id(), $categoryType);
        $data['title'] = __('Trades');
        $data['categoryType'] = $categoryType;

        return view('frontend.reports.trades', $data);
    }

    /**
     * Назначение: показывает отчет по реферальным пользователям.
     *
     * Действие: получает пользователей, приглашенных текущим аккаунтом, и возвращает отчетное представление.
     */
    public function referralUsers(): View|Factory|Application
    {
        $data['list'] = $this->reportsService->referralUsers(Auth::id());
        $data['title'] = __('Trades');

        return view('frontend.reports.referral_users', $data);
    }

    /**
     * Назначение: показывает отчет по реферальным начислениям.
     *
     * Действие: проверяет наличие реферального кода, получает начисления и возвращает отчет или перенаправление.
     */
    public function referralEarning(): View|Factory|Application|RedirectResponse
    {
        try {
            $userId = decrypt(request()->get('ref'));
        } catch (\Exception $exception) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Referral earning not found for this request.'));
        }

        $data['list'] = $this->reportsService->referralEarning(\auth()->id(), $userId);
        $data['referralUserInfo'] = $this->userInfo->findOrFailByConditions(['user_id' => $userId]);
        $data['title'] = __('Referral Earning');
//        dd($data);
        return view('frontend.reports.referral_earning', $data);
    }
}
