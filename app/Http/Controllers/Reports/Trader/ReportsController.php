<?php

namespace App\Http\Controllers\Reports\Trader;

use App\Http\Controllers\Controller;
use App\Services\User\Admin\ReportsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ReportsController extends Controller
{
    /**
     * Purpose: initializes the ReportsController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(
        private readonly ReportsService $reportsService,
    ) {
    }

    /**
     * Purpose: displays the authenticated trader's deposit report rows.
     *
     * Action: delegates authenticated user scoping and list preparation to the report service.
     */
    public function allDeposits(?string $paymentTransactionType = null): View
    {
        return view('frontend.reports.all_deposit', $this->reportsService->traderAllDepositsData($paymentTransactionType));
    }

    /**
     * Purpose: displays the authenticated trader's wallet deposit report rows.
     *
     * Action: delegates wallet ownership checks and list preparation to the report service.
     */
    public function deposits(int|string $id, ?string $paymentTransactionType = null): View
    {
        return view('frontend.reports.deposit', $this->reportsService->traderDepositsData($id, $paymentTransactionType));
    }

    /**
     * Purpose: displays the authenticated trader's withdrawal report rows.
     *
     * Action: delegates authenticated user scoping and list preparation to the report service.
     */
    public function allWithdrawals(?string $paymentTransactionType = null): View
    {
        return view('frontend.reports.all_withdrawal', $this->reportsService->traderAllWithdrawalsData($paymentTransactionType));
    }

    /**
     * Purpose: displays the authenticated trader's wallet withdrawal report rows.
     *
     * Action: delegates wallet ownership checks and list preparation to the report service.
     */
    public function withdrawals(int|string $id, ?string $paymentTransactionType = null): View
    {
        return view('frontend.reports.withdrawal', $this->reportsService->traderWithdrawalsData($id, $paymentTransactionType));
    }

    /**
     * Purpose: displays the authenticated trader's executed trade report rows.
     *
     * Action: delegates trade category filtering and authenticated user scoping to the service.
     */
    public function trades(?string $categoryType = null): View
    {
        return view('frontend.reports.trades', $this->reportsService->traderTradesData($categoryType));
    }

    /**
     * Purpose: displays the authenticated trader's referred users.
     *
     * Action: delegates referral list scoping and data preparation to the report service.
     */
    public function referralUsers(): View
    {
        return view('frontend.reports.referral_users', $this->reportsService->traderReferralUsersData());
    }

    /**
     * Purpose: displays referral earnings for a selected referred user.
     *
     * Action: resolves the encrypted referral id through the service and renders earning totals.
     */
    public function referralEarning(Request $request): View|RedirectResponse
    {
        $encryptedReferralId = $request->query('ref');
        $referralUserId = $this->reportsService->resolveReferralUserId(is_string($encryptedReferralId) ? $encryptedReferralId : null);

        if (is_null($referralUserId)) {
            return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Referral earning not found for this request.'));
        }

        return view('frontend.reports.referral_earning', $this->reportsService->traderReferralEarningData($referralUserId));
    }
}
