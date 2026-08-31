<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['ticker', 'type'];

    protected $appends = ['quantity', 'average_price'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lots()
    {
        return $this->hasMany(PurchaseLot::class);
    }

    public function getQuantityAttribute(): int
    {
        return $this->lots->sum('quantity');
    }

    public function getAveragePriceAttribute(): float
    {
        $quantity = $this->quantity;

        if ($quantity === 0) {
            return 0;
        }

        return $this->lots->sum(fn ($lot) => $lot->quantity * $lot->price) / $quantity;
    }

    // relacionamento pra sempre pegar a cotação mais recente

    public function market()
    {
        return $this->hasOne(StockMarket::class)->latestOfMany();
    }
}
