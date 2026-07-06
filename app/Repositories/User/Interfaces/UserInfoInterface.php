<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/1/18
 * Time: 12:11 PM
 */

namespace App\Repositories\User\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface UserInfoInterface
{
    /**
     * Purpose: describes the user profile lookup contract.
     *
     * Action: returns the first matching profile or aborts when it cannot be found.
     */
    public function findOrFailByConditions(array $conditions, string|array|null $relations = null): Model;
}
