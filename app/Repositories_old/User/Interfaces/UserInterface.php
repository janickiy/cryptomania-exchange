<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/1/18
 * Time: 12:11 PM
 */

namespace App\Repositories\User\Interfaces;


interface UserInterface
{
    /**
     * Purpose: describes the get count by conditions contract for UserInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getCountByConditions(array $conditions);

    /**
     * Purpose: describes the get by user ids contract for UserInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getByUserIds(array $ids, array $conditions = []);
}