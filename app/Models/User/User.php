<?php

namespace App\Models\User;

use App\Models\Core\UserRoleManagement;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class User extends Authenticatable implements AuditableInterface
{
    use Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'password', 'email', 'user_role_management_id', 'remember_me', 'avatar', 'is_email_verified', 'is_financial_active', 'is_accessible_under_maintenance', 'google2fa_secret', 'is_active', 'created_by_admin','referral_code','referrer_id'];

    protected $fakeFields = ['username', 'password', 'email', 'user_role_management_id', 'remember_me', 'avatar', 'is_email_verified', 'is_financial_active', 'is_accessible_under_maintenance', 'google2fa_secret', 'is_active', 'created_by_admin'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Purpose: defines a model relation or computed value through user role management.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return BelongsTo
     */
    public function userRoleManagement(): BelongsTo
    {
        return $this->belongsTo(UserRoleManagement::class);
    }

    /**
     * Purpose: defines a model relation or computed value through user info.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return HasOne
     */
    public function userInfo(): HasOne
    {
        return $this->hasOne(UserInfo::class);
    }

    /**
     * Purpose: defines a model relation or computed value through user setting.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return HasOne
     */
    public function userSetting(): HasOne
    {
        return $this->hasOne(UserSetting::class);
    }

    /**
     * Purpose: defines a model relation or computed value through wallets.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return HasMany
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(Wallet::class);
    }
}
