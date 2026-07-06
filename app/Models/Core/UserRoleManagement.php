<?php

namespace App\Models\Core;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class UserRoleManagement extends Model implements AuditableInterface
{
    use Auditable;

    protected $table = 'user_role_managements';

    protected $fillable = ['role_name', 'route_group'];

    protected $fakeFields = ['role_name', 'route_group'];

    /**
     * Purpose: returns a computed model attribute through the get route group attribute accessor.
     *
     * Action: is used by Eloquent when reading the property so the app receives a prepared value.
     *
     */
    public function getRouteGroupAttribute(?string $value): ?array
    {
        return json_decode($value, true);
    }

    /**
     * Purpose: normalizes a model attribute through the set route group attribute mutator.
     *
     * Action: is used by Eloquent when assigning the property so the stored value has the expected format.
     *
     * @param $value
     */
    public function setRouteGroupAttribute(array|string|null $value): void
    {
        $this->attributes['route_group'] = json_encode($value);
    }

    /**
     * Purpose: defines a model relation or computed value through users.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
