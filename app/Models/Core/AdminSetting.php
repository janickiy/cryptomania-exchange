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

    public function getRouteGroupAttribute(mixed $value): mixed
    {
        return json_decode($value,true);
    }
}
