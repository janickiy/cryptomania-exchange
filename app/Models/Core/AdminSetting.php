<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class AdminSetting extends Model implements AuditableInterface
{
    use Auditable;

    protected $table = 'admin_settings';

    protected $fillable = [
        'slug',
        'value',
    ];

    /**
     * Purpose: returns a computed model attribute through the get route group attribute accessor.
     *
     * Action: is used by Eloquent when reading the property so the app receives a prepared value.
     *
     */
    public function getRouteGroupAttribute(?string $value): ?array
    {
        return json_decode($value,true);
    }
}
