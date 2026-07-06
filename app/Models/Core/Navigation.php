<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class Navigation extends Model implements AuditableInterface
{
    use Auditable;

    protected $table = 'navigations';

    protected $fillable = ['slug', 'navigation_items'];

    protected $fakeFields = ['slug', 'navigation_items'];

    /**
     * Purpose: returns a computed model attribute through the get navigation items attribute accessor.
     *
     * Action: is used by Eloquent when reading the property so the app receives a prepared value.
     *
     * @param $value
     * @return mixed
     */
    public function getNavigationItemsAttribute(?string $value): ?array
    {
        return json_decode($value, true);
    }

    /**
     * Purpose: normalizes a model attribute through the set navigation items attribute mutator.
     *
     * Action: is used by Eloquent when assigning the property so the stored value has the expected format.
     *
     * @param $value
     * @return false|string
     */
    public function setNavigationItemsAttribute(array|string|null $value): string|false
    {
        return $this->attributes['navigation_items'] =json_encode($value);
    }
}
