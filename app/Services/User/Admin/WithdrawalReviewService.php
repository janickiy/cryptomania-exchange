<?php

namespace App\Services\User\Admin;

use App\Jobs\ReversWithdrawal;
use App\Jobs\StockItemWithdrawal;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;

class WithdrawalReviewService
{
    /**
     * Purpose: initializes the WithdrawalReviewService instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly WithdrawalInterface $withdrawalRepository)
    {
    }

    /**
     * Purpose: executes the approve service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function approve(int $id): bool
    {
        if (!$this->withdrawalRepository->updateByConditions(['status' => PAYMENT_PENDING], ['id' => $id, 'status' => PAYMENT_REVIEWING])) {
            return false;
        }

        dispatch(new StockItemWithdrawal($id));

        return true;
    }

    /**
     * Purpose: executes the decline service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function decline(int $id): bool
    {
        if (!$this->withdrawalRepository->updateByConditions(['status' => PAYMENT_DECLINED], ['id' => $id, 'status' => PAYMENT_REVIEWING])) {
            return false;
        }

        dispatch(new ReversWithdrawal($id));

        return true;
    }
}
