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
     * @param $value
     * @return mixed
     */
    public function getNavigationItemsAttribute(mixed $value): ?array
    {
        return json_decode($value, true);
    }

    /**
     * @param $value
     * @return false|string
     */
    public function setNavigationItemsAttribute(mixed $value): string|false
    {
        return $this->attributes['navigation_items'] =json_encode($value);
    }
}
