<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/1/18
 * Time: 11:19 AM
 */

namespace App\Repositories\User\Interfaces;


interface NotificationInterface
{
    /**
     * Purpose: describes the read contract for NotificationInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function read(int|string $id);

    /**
     * Purpose: describes the unread contract for NotificationInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function unread(int|string $id);

    /**
     * Purpose: describes the count unread contract for NotificationInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function countUnread(int|string $userId);

    /**
     * Purpose: describes the get last five contract for NotificationInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getLastFive(int|string $userId);
}
