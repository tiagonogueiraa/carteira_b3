<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockDividend extends Model
{
    protected $fillable = ['ticker', 'payment_date', 'amount', 'source'];

    protected $casts = ['payment_date' => 'date'];
}
