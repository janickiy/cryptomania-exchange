<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralEarning extends Model
{
    protected $table = 'referral_earnings';

    protected $guarded = ['id'];

    public function referrerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referralUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referral_user_id');
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }


}
