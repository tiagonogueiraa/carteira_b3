<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseLot extends Model
{
    protected $fillable = ['quantity', 'price', 'purchased_at'];

    protected $casts = ['purchased_at' => 'date'];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}
