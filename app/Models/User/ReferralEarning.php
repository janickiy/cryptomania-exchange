<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralEarning extends Model
{
    protected $table = 'referral_earnings';

    protected $guarded = ['id'];

    /**
     * Purpose: defines the user who generated this referral earning.
     *
     * Action: lets Eloquent load the referrer through the referrer_user_id foreign key.
     *
     * @return BelongsTo
     */
    public function referrerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    /**
     * Purpose: defines a model relation or computed value through referral user.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return BelongsTo
     */
    public function referralUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referral_user_id');
    }

    /**
     * Purpose: defines a model relation or computed value through stock item.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return BelongsTo
     */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }


}
