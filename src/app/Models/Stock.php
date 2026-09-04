<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['ticker', 'type'];

    protected $appends = ['quantity', 'average_price', 'logo_url'];

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

    // relacionamento com o catálogo de tickers da B3, casando pelo símbolo
    // (não é FK de id — stocks.ticker bate com b3_tickers.symbol)
    public function b3Ticker()
    {
        return $this->belongsTo(B3Ticker::class, 'ticker', 'symbol');
    }
    // para pegar a logo e associar no ticker
    public function getLogoUrlAttribute(){
        return $this->b3Ticker?->logo_url; // ?-> interrogaão para evitar erro
    }
}
