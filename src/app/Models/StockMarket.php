<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMarket extends Model
{
    protected $fillable = [
        'stock_id', 'short_name', 'long_name', 'currency',
        'regular_market_price', 'regular_market_day_high', 'regular_market_day_low',
        'regular_market_day_range', 'regular_market_change', 'regular_market_change_percent',
        'regular_market_time', 'market_cap', 'regular_market_volume',
        'regular_market_previous_close', 'regular_market_open',
        'fifty_two_week_range', 'fifty_two_week_low', 'fifty_two_week_high', 'logo_url',
    ];

    protected $casts = [
        'regular_market_time' => 'datetime',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}