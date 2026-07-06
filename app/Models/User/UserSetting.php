<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class UserSetting extends Model implements AuditableInterface
{
    use Auditable;

    protected $table = 'user_settings';

    protected $fillable = ['user_id', 'language', 'timezone'];

    protected $fakeFields = ['language', 'timezone', 'is_otp_allowed'];

    /**
     * Purpose: defines a model relation or computed value through user.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
