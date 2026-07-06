<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 9/30/18
 * Time: 1:21 PM
 */

namespace App\Repositories\Core\Interfaces;


interface SystemNoticeInterface
{
    /**
     * Purpose: describes the todays notifications contract for SystemNoticeInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function todaysNotifications();
}