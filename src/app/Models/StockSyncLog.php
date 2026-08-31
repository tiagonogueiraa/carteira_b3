<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockSyncLog extends Model
{
    protected $fillable = ['ticker', 'status', 'response', 'error_message', 'status_code'];

    protected $casts = [
        'response' => 'array', // salva/lê como JSON automaticamente
    ];
}