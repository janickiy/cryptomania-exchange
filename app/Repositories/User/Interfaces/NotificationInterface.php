<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/1/18
 * Time: 11:19 AM
 */

namespace App\Repositories\User\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface NotificationInterface
{
    /**
     * Purpose: describes the notification creation contract.
     *
     * Action: persists one notification row and returns the created model or false.
     */
    public function create(array $attributes): Model|false;

    /**
     * Purpose: describes the notification bulk insert contract.
     *
     * Action: persists multiple prepared notification rows in one database operation.
     */
    public function insert(array $attributes): bool;

    /**
     * Purpose: describes the read contract for NotificationInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function read(int|string $id): bool;

    /**
     * Purpose: describes the unread contract for NotificationInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function unread(int|string $id): bool;

    /**
     * Purpose: describes the count unread contract for NotificationInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function countUnread(int|string $userId): int;

    /**
     * Purpose: describes the get last five contract for NotificationInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getLastFive(int|string $userId): Collection;
}
