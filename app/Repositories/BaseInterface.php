<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 9/30/18
 * Time: 12:39 PM
 */

namespace App\Repositories;


interface BaseInterface
{
    /**
     * Purpose: describes the model contract for BaseInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function model();

    /**
     * Purpose: describes the bulk update contract for BaseInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function bulkUpdate($values);

    /**
     * Purpose: describes the search contract for BaseInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function search($searchFields, $orderFields = null, $whereArray = null, $selectData = null, $joinArray = null, $paginationKey = 'p', $dateField = 'created_at');
}