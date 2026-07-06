<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $table = 'wallets';

    protected $fillable = ['id', 'user_id', 'stock_item_id', 'primary_balance', 'on_order_balance', 'address', 'is_active'];

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
