<?php

namespace App\Models\User;

use App\Models\Core\UserRoleManagement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class UserInfo extends Model implements AuditableInterface
{
    use Auditable;

    protected $table = 'user_infos';

    protected $fillable = ['user_id', 'first_name', 'last_name', 'address', 'phone', 'is_id_verified', 'id_type', 'id_card_front', 'id_card_back'];

    protected $fakeFields = ['user_id','first_name','last_name', 'country_id', 'address', 'phone', 'is_id_verified', 'id_type', 'id_card_front', 'id_card_back'];

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
