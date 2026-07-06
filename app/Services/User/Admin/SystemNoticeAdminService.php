<?php

namespace App\Services\User\Admin;

use App\DTO\Admin\SystemNoticeData;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;
use Illuminate\Database\Eloquent\Model;

class SystemNoticeAdminService
{
    /**
     * Purpose: initializes the SystemNoticeAdminService instance.
     *
     * Action: receives dependencies and initial data so the remaining methods can work with prepared state.
     *
     */
    public function __construct(private readonly SystemNoticeInterface $systemNotice)
    {
    }

    /**
     * Purpose: executes the create service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function create(SystemNoticeData $data): Model|false
    {
        return $this->systemNotice->createFromDto($data);
    }

    /**
     * Purpose: executes the update service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function update(int $id, SystemNoticeData $data): bool
    {
        return $this->systemNotice->updateFromDto($id, $data);
    }

    /**
     * Purpose: executes the delete service operation.
     *
     * Action: contains scenario business logic and keeps controllers free from processing details.
     *
     */
    public function delete(int $id): bool
    {
        return $this->systemNotice->deleteById($id);
    }
}
