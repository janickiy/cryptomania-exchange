<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class StockPair extends Model
{

    protected $table = 'stock_pairs';

    protected $fillable = ['stock_item_id', 'base_item_id', 'is_active', 'is_default', 'base_item_buy_order_volume', 'stock_item_buy_order_volume', 'base_item_sale_order_volume', 'stock_item_sale_order_volume', 'exchanged_buy_total', 'exchanged_sale_total', 'exchanged_maker_total', 'exchanged_amount', 'exchanged_buy_fee', 'exchanged_sale_fee', 'last_price', 'exchange_24', 'ico_total_earned', 'ico_fee_earned', 'ico_total_sold'];

    protected $fakeFields = ['stock_item_id', 'base_item_id', 'is_active', 'is_default', 'base_item_buy_order_volume', 'stock_item_buy_order_volume', 'base_item_sale_order_volume', 'stock_item_sale_order_volume', 'exchanged_buy_total', 'exchanged_sale_total', 'exchanged_maker_total', 'exchanged_amount', 'exchanged_buy_fee', 'exchanged_sale_fee', 'last_price', 'exchange_24', 'ico_total_earned', 'ico_fee_earned', 'ico_total_sold'];

    /**
     * Purpose: defines a model relation or computed value through stock item.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     */
    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'stock_item_id', 'id');
    }

    /**
     * Purpose: defines a model relation or computed value through base item.
     *
     * Action: lets Eloquent load related data while keeping model rules close to the model.
     *
     */
    public function baseItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class, 'base_item_id', 'id');
    }

    /**
     * Purpose: returns a computed model attribute through the get stock pair attribute accessor.
     *
     * Action: is used by Eloquent when reading the property so the app receives a prepared value.
     *
     */
    public function getStockPairAttribute(): string
    {
        return $this->stockItem->item . '/' . $this->baseItem->item;
    }
}
