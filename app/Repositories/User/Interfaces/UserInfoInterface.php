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
     * Purpose: describes the user profile creation contract.
     *
     * Action: persists prepared profile attributes and returns the created model or false.
     */
    public function create(array $attributes): Model|false;

    /**
     * Purpose: describes the user profile update contract.
     *
     * Action: updates a profile row by identifier or custom attribute and returns the updated model or false.
     */
    public function update(array $attributes, int $id, string $attribute = 'id'): Model|bool;

    /**
     * Purpose: describes the user profile lookup contract.
     *
     * Action: returns the first matching profile or aborts when it cannot be found.
     */
    public function findOrFailByConditions(array $conditions, string|array|null $relations = null): Model;
}
