<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockDividend extends Model
{
    protected $fillable = ['stock_id', 'payment_date', 'amount', 'source'];

    protected $casts = ['payment_date' => 'date'];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
