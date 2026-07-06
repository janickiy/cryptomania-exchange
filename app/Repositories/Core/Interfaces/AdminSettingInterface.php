<?php
/**
 * Created by PhpStorm.
 * User: rana
 * Date: 9/30/18
 * Time: 11:31 AM
 */

namespace App\Repositories\Core\Interfaces;


interface AdminSettingInterface
{
    /**
     * Purpose: describes the get by slug contract for AdminSettingInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getBySlug(string $slug);

    /**
     * Purpose: describes the get by slugs contract for AdminSettingInterface.
     *
     * Action: defines the expected signature so implementations use one consistent behavior for this scenario.
     *
     */
    public function getBySlugs(array $slugs);
}
