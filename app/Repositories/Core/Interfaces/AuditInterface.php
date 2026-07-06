<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 10/2/18
 * Time: 4:56 PM
 */

namespace App\Repositories\Core\Interfaces;


interface AuditInterface
{
    /**
     * Purpose: describes the paginate with user filters contract for AuditInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function paginateWithUserFilters(array $searchFields, array $orderFields);
}
