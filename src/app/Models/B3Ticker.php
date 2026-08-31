<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model:  B3Ticker
//    ↓ snake_case
//    b3_ticker
//    ↓ plural
//    b3_tickers   ← nome da tabela que o Laravel procura automaticamente
class B3Ticker extends Model
{
    //
    protected $fillable = [
        'symbol',
        'name',
        'long_name',
        'asset_type',
        'sub_type',
        'sector',
        'subsector',
        'is_active',
        'logo_url',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
