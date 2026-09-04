<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\B3Ticker;

class TickerSearchController extends Controller
{
    // para fazer a pesquisa de açõs no cadastro

    public function search(Request $request)
    {
      
        $term = $request->get('ticker', '');
        if(strlen($term) < 1) {
            return response()->json([]);
        }

        $results = B3Ticker::where('is_active', true)
            ->where( function($query) use ($term) {
                $query->where('symbol', 'like', '%' . $term . '%')
                    ->orWhere('name', 'like', '%' . $term . '%');
            })
            ->limit(10)
            ->get(['symbol', 'name', 'logo_url']);

            return response()->json($results);
        
    }
}
