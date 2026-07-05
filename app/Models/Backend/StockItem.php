<?php

namespace App\Models\Backend;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class StockItem extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'stock_items';

    protected $fillable = ['item', 'item_name', 'item_type', 'item_emoji', 'is_active', 'exchange_status', 'is_ico', 'deposit_status', 'deposit_fee', 'withdrawal_status', 'withdrawal_fee', 'minimum_withdrawal_amount', 'daily_withdrawal_limit', 'api_service', 'total_deposit', 'total_pending_deposit', 'total_deposit_fee', 'total_withdrawal', 'total_pending_withdrawal', 'total_withdrawal_fee'];

    protected $fakeFields = ['item', 'item_name', 'item_type', 'item_emoji', 'is_active', 'exchange_status', 'is_ico', 'deposit_status', 'deposit_fee', 'withdrawal_status', 'withdrawal_fee', 'minimum_withdrawal_amount', 'daily_withdrawal_limit', 'api_service', 'total_deposit', 'total_pending_deposit', 'total_deposit_fee', 'total_withdrawal', 'total_pending_withdrawal', 'total_withdrawal_fee'];

    /**
     * @return string
     */
    public function getStockItemNameAttribute(): string
    {
        return $this->item . ' (' . $this->item_name . ')';
    }

    public function baseStockPairs(): HasMany
    {
        return $this->hasMany(StockPair::class, 'base_item_id');
    }

    public function stockPairs(): HasMany
    {
        return $this->hasMany(StockPair::class, 'stock_item_id');
    }
}
