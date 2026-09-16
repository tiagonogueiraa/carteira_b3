<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $stocks = $request->user()->stocks()->with('lots', 'market', 'dividends')->get();

        // dd($stocks);

        // Gera os 6 pontos do gráfico (5 meses atrás até o mês atual). Pra cada
        // um, soma quantidade × preço de todo lote comprado até o fim daquele
        // mês — dá o capital investido acumulado naquele momento, sem depender
        // de cotação de mercado (isso vem depois, com a integração da brapi.dev).
        $months = collect(range(5, 0))->map(function (int $monthsAgo) use ($stocks) {
            $referenceDate = now()->subMonths($monthsAgo)->endOfMonth();

            $invested = $stocks->flatMap->lots
                ->filter(fn ($lot) => $lot->purchased_at->lte($referenceDate))
                ->sum(fn ($lot) => $lot->quantity * $lot->price);
                
            return [
                'month' => $referenceDate->translatedFormat('M/y'),
                'invested' => round($invested, 2),
            ];
        });

        // Mesmo cálculo, mas quebrado por ação: uma série por ticker, cada
        // uma com o capital investido acumulado até o fim de cada mês.
        $stocksHistorySeries = $stocks->map(function ($stock) {
            $data = collect(range(5, 0))->map(function (int $monthsAgo) use ($stock) {
                $referenceDate = now()->subMonths($monthsAgo)->endOfMonth();

                return round(
                    $stock->lots
                        ->filter(fn ($lot) => $lot->purchased_at->lte($referenceDate))
                        ->sum(fn ($lot) => $lot->quantity * $lot->price),
                    2
                );
            });

            return [
                'name' => $stock->ticker,
                'data' => $data,
            ];
        })->values();

        return Inertia::render('Dashboard', [
            'stocks' => $stocks,
            'netWorthHistory' => $months,
            'stocksHistory' => [
                'categories' => $months->pluck('month'),
                'series' => $stocksHistorySeries,
            ],
        ]);
    }
}
