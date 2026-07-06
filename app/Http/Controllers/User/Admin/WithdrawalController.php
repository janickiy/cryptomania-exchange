<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\User\Admin\ReportsService;
use App\Services\User\Admin\WithdrawalReviewService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class WithdrawalController extends Controller
{
    /**
     * Purpose: initializes the WithdrawalController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(
        private readonly WithdrawalInterface $withdrawalRepository,
        private readonly ReportsService $reportsService,
        private readonly WithdrawalReviewService $withdrawalReviewService,
    ) {
    }

    /**
     * Purpose: shows the main page or record list for the section.
     *
     * Action: collects data through services or repositories and returns the view.
     *
     */
    public function index(): View
    {
        $data['list'] = $this->reportsService->withdrawals(null, null, 'reviewing');
        $data['title'] = __('Withdrawals for Reviewing');

        return view('backend.review_withdrawals.withdrawal', $data);
    }

    /**
     * Purpose: shows the detail page for the selected record.
     *
     * Action: loads the record by identifier and passes it to the view.
     *
     */
    public function show(int|string $id): View
    {
        $data['title'] = __('Review Withdrawal');
        $data['withdrawal'] = $this->withdrawalRepository->findOrfailById($id, ['stockItem', 'wallet', 'user', 'user.userinfo']);
        $data['user'] = $data['withdrawal']->user;

        return view('backend.review_withdrawals.show', $data);
    }

    /**
     * Purpose: approves the selected request or operation.
     *
     * Action: changes status through the service layer and redirects with the result.
     *
     */
    public function approve(int|string $id): RedirectResponse
    {
        if ($this->withdrawalReviewService->approve((int) $id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The withdrawal has been approved successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid Request.'));
    }

    /**
     * Purpose: declines the selected request or operation.
     *
     * Action: changes status through the service layer and redirects with the result.
     *
     */
    public function decline(int|string $id): RedirectResponse
    {
        if ($this->withdrawalReviewService->decline((int) $id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The withdrawal has been declined successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid Request.'));
    }
}
