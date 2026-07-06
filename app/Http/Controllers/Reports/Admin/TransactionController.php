<?php

namespace App\Http\Controllers\Reports\Admin;

use App\Http\Controllers\Controller;
use App\Services\User\Admin\TransactionReportService;
use Illuminate\Contracts\View\View;

class TransactionController extends Controller
{
    /**
     * Purpose: initializes the TransactionController instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     */
    public function __construct(
        private readonly TransactionReportService $transactionReportService,
    ) {
    }

    /**
     * Purpose: displays transaction report rows for a specific user.
     *
     * Action: delegates filtering, summary calculation, and list preparation to the report service.
     */
    public function user(int|string $userId, ?string $journalType = null): View
    {
        return view('backend.transactions.all_users', $this->transactionReportService->userData($userId, $journalType));
    }

    /**
     * Purpose: displays transaction report rows across all users.
     *
     * Action: delegates filtering, summary calculation, and list preparation to the report service.
     */
    public function allUser(?string $journalType = null): View
    {
        return view('backend.transactions.all_users', $this->transactionReportService->allUsersData($journalType));
    }
}
