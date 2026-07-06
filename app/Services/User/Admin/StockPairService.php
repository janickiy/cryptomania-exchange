<?php

namespace App\Services\User\Admin;

use App\DTO\Admin\StockPairData;
use App\Exceptions\JobException;
use App\Repositories\User\Admin\Interfaces\StockPairInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockPairService
{
    /**
     * Purpose: initializes the StockPairService instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly StockPairInterface $stockPair)
    {
    }

    /**
     * Purpose: executes the create service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function create(StockPairData $data): Model|false
    {
        return DB::transaction(function () use ($data) {
            if ($data->isDefault === ACTIVE_STATUS_ACTIVE) {
                $removed = $this->stockPair->updateRows(['is_default' => ACTIVE_STATUS_INACTIVE], ['is_default' => ACTIVE_STATUS_ACTIVE]);

                if (!$removed) {
                    throw new JobException(__('Failed to create stock pair.'));
                }
            }

            return $this->stockPair->createFromDto($data);
        });
    }

    /**
     * Purpose: executes the update service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function update(int $id, StockPairData $data): bool
    {
        return $this->stockPair->updateFromDto($id, $data);
    }

    /**
     * Purpose: executes the delete service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function delete(int $id): bool
    {
        return $this->stockPair->deleteByConditions(['id' => $id, 'is_default' => ACTIVE_STATUS_INACTIVE]);
    }

    /**
     * Purpose: executes the toggle active status service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function toggleActiveStatus(int $id): array
    {
        if ($updated = $this->stockPair->toggleStatusByConditions(['id' => $id, 'is_default' => ACTIVE_STATUS_INACTIVE])) {
            $message = $updated->is_active == ACTIVE_STATUS_ACTIVE
                ? __('The stock pair has been activated successfully.')
                : __('The stock pair has been deactivated successfully.');

            return [SERVICE_RESPONSE_SUCCESS => $message];
        }

        return [SERVICE_RESPONSE_ERROR => __('Failed to change status.')];
    }

    /**
     * Purpose: executes the make default service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function makeDefault(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $this->stockPair->updateRows(['is_default' => ACTIVE_STATUS_INACTIVE], ['is_default' => ACTIVE_STATUS_ACTIVE]);

            $updated = $this->stockPair->updateByConditions(
                ['is_default' => ACTIVE_STATUS_ACTIVE],
                ['is_active' => ACTIVE_STATUS_ACTIVE, 'is_default' => ACTIVE_STATUS_INACTIVE, 'id' => $id]
            );

            if (!$updated) {
                throw new JobException(__('Failed to make default.'));
            }

            return true;
        });
    }
}
