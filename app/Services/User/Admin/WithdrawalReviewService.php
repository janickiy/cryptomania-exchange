<?php

namespace App\Services\User\Admin;

use App\Jobs\ReversWithdrawal;
use App\Jobs\StockItemWithdrawal;
use App\Repositories\User\Trader\Interfaces\WithdrawalInterface;

class WithdrawalReviewService
{
    public function __construct(private readonly WithdrawalInterface $withdrawalRepository)
    {
    }

    public function approve(int $id): bool
    {
        if (!$this->withdrawalRepository->updateByConditions(['status' => PAYMENT_PENDING], ['id' => $id, 'status' => PAYMENT_REVIEWING])) {
            return false;
        }

        dispatch(new StockItemWithdrawal($id));

        return true;
    }

    public function decline(int $id): bool
    {
        if (!$this->withdrawalRepository->updateByConditions(['status' => PAYMENT_DECLINED], ['id' => $id, 'status' => PAYMENT_REVIEWING])) {
            return false;
        }

        dispatch(new ReversWithdrawal($id));

        return true;
    }
}
