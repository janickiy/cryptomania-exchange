<?php

namespace App\Services\User\Admin;

use App\DTO\Admin\SystemNoticeData;
use App\Repositories\Core\Interfaces\SystemNoticeInterface;

class SystemNoticeAdminService
{
    public function __construct(private readonly SystemNoticeInterface $systemNotice)
    {
    }

    public function create(SystemNoticeData $data): mixed
    {
        return $this->systemNotice->createFromDto($data);
    }

    public function update(int $id, SystemNoticeData $data): bool
    {
        return $this->systemNotice->updateFromDto($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->systemNotice->deleteById($id);
    }
}
