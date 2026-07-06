<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $table = 'withdrawals';

    protected $fillable = ['user_id', 'ref_id', 'wallet_id', 'stock_item_id', 'amount', 'network_fee', 'system_fee', 'address', 'txn_id', 'payment_method', 'status'];

    /**
     * Purpose: defines the currency or token requested for withdrawal.
     *
     * Action: lets Eloquent load the related stock item for withdrawal displays and processing.
     *
     * @return BelongsTo
     */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    /**
     * Purpose: defines a model relation or computed value through wallet.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return BelongsTo
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

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
