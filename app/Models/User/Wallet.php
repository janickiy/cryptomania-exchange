<?php

namespace App\Models\User;

use App\Models\Backend\StockItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $table = 'wallets';

    protected $fillable = ['id', 'user_id', 'stock_item_id', 'primary_balance', 'on_order_balance', 'address', 'is_active'];

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
