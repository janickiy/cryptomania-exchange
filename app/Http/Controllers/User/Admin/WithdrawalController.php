<?php

namespace App\Http\Controllers\User\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;
use App\Services\User\Admin\ReportsService;
use App\Services\User\Admin\WithdrawalReviewService;

class WithdrawalController extends Controller
{
    private $withdrawalRepository;

    public function __construct(WithdrawalInterface $withdrawalRepository)
    {
        $this->withdrawalRepository = $withdrawalRepository;
    }

    public function index()
    {
        $data['list'] = app(ReportsService::class)->withdrawals(null, null, 'reviewing');
        $data['title'] = __('Withdrawals for Reviewing');

        return view('backend.review_withdrawals.withdrawal', $data);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
     */
    public function show($id)
    {
        $data['title'] = __('Review Withdrawal');
        $data['withdrawal'] = $this->withdrawalRepository->findOrfailById($id, ['stockItem', 'wallet', 'user', 'user.userinfo']);
        $data['user'] = $data['withdrawal']->user;

        return view('backend.review_withdrawals.show', $data);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve($id)
    {
        if (app(WithdrawalReviewService::class)->approve((int) $id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The withdrawal has been approved successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid Request.'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function decline($id)
    {
        if (app(WithdrawalReviewService::class)->decline((int) $id)) {
            return redirect()->back()->with(SERVICE_RESPONSE_SUCCESS, __('The withdrawal has been declined successfully.'));
        }

        return redirect()->back()->with(SERVICE_RESPONSE_ERROR, __('Invalid Request.'));
    }
}
