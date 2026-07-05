<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;

class ReferralEarning extends Model
{
    protected $table = 'referral_earnings';

    protected $guarded = ['id'];

    public function referrerUser(): mixed
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    public function referralUser(): mixed
    {
        return $this->belongsTo(User::class, 'referral_user_id');
    }

    public function stockItem(): mixed
    {
        return $this->belongsTo(StockItem::class);
    }


}
