<?php

namespace App\Models\User;

use App\Models\Backend\StockPair;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOrder extends Model
{
    protected $table = 'stock_orders';

    protected $fillable = [
        'user_id',
        'stock_pair_id',
        'category',
        'exchange_type',
        'status',
        'price',
        'amount',
        'exchanged',
        'canceled',
        'stop_limit',
        'maker_fee',
        'taker_fee',
    ];

    protected $fakeFields = [
        'user_id',
        'stock_pair_id',
        'category',
        'exchange_type',
        'status',
        'price',
        'amount',
        'exchanged',
        'canceled',
        'stop_limit',
        'maker_fee',
        'taker_fee',
    ];

    /**
     * Purpose: defines a model relation or computed value through stock pair.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     * @return BelongsTo
     */
    public function stockPair(): BelongsTo
    {
        return $this->belongsTo(StockPair::class);
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
