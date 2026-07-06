<?php

namespace App\Models\Backend;

use App\Models\User\StockOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockExchange extends Model
{
    protected $table = 'stock_exchanges';

    protected $fillable = [
        'user_id',
        'stock_exchange_group_id',
        'stock_order_id',
        'stock_pair_id',
        'amount',
        'price',
        'total',
        'fee',
        'referral_earning',
        'exchange_type',
        'related_order_id',
        'base_order',
        'is_maker',
    ];

    /**
     * Purpose: defines a model relation or computed value through stock order.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     */
    public function stockOrder(): BelongsTo
    {
        return $this->belongsTo(StockOrder::class);
    }
}
