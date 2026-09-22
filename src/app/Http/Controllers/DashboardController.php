<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        // Carrega a ação junto com: lotes de compra, cotação mais recente,
        // TODO o histórico de cotações (marketHistory) e os dividendos pagos.
        $stocks = $request->user()->stocks()->with('lots', 'market', 'marketHistory', 'dividends')->get();

        // Gera os 6 pontos do gráfico (5 meses atrás até o mês atual). Pra cada
        // um, soma quantidade × preço de todo lote comprado até o fim daquele
        // mês — dá o capital investido acumulado naquele momento, sem depender
        // de cotação de mercado.
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

        $days = collect(range(29, 0))->map(function (int $daysAgo) use ($stocks) {
            $referenceDate = now()->subDays($daysAgo)->endOfDay();

            $invested = $stocks->flatMap->lots
                ->filter(fn ($lot) => $lot->purchased_at->lte($referenceDate))
                ->sum(fn ($lot) => $lot->quantity * $lot->price);

            return [
                'day' => $referenceDate->translatedFormat('d/M'),
                'invested' => round($invested, 2),
            ];
        });
        
        $stocksHistorySeries = $stocks->map(function ($stock) {
            $data = collect(range(29, 0))->map(function (int $daysAgo) use ($stock) {
                $referenceDate = now()->subDays($daysAgo)->endOfDay();

                $quantidade = $stock->lots
                    ->filter(fn ($lot) => $lot->purchased_at->lte($referenceDate))
                    ->sum('quantity');

                $precoNaData = $stock->marketHistory
                    ->filter(fn ($m) => $m->created_at->lte($referenceDate))
                    ->sortByDesc('created_at')
                    ->first()
                    ?->regular_market_price ?? $stock->average_price;

                return round($quantidade * $precoNaData, 2);
            });

            return [
                'name' => $stock->ticker,
                'data' => $data,
            ];
        })->values();

        // Histórico DIÁRIO dos últimos 30 dias: valor de mercado da carteira
        // inteira (soma de todas as ações) e valor de mercado + dividendos
        // recebidos até aquele dia.
        $dailyHistory = collect(range(29, 0))->map(function (int $daysAgo) use ($stocks) {
            $referenceDate = now()->subDays($daysAgo)->endOfDay();

            // Soma, pra todas as ações, quantidade possuída até essa data ×
            // preço que a ação tinha nessa data (ou o preço médio, se ainda
            // não houver cotação registrada até então).
            $mercado = $stocks->sum(function ($stock) use ($referenceDate) {
                $quantidade = $stock->lots
                    ->filter(fn ($lot) => $lot->purchased_at->lte($referenceDate))
                    ->sum('quantity');

                $precoNaData = $stock->marketHistory
                    ->filter(fn ($m) => $m->created_at->lte($referenceDate))
                    ->sortByDesc('created_at')
                    ->first()
                    ?->regular_market_price ?? $stock->average_price;

                return $quantidade * $precoNaData;
            });

            // Soma bruta de dividendos pagos até essa data (aproximado —
            // pra ficar exato como o accessor total_dividends, precisaria
            // multiplicar pela quantidade possuída na data de cada pagamento).
            // $dividendosAteData = $stocks->flatMap->dividends
            //     ->filter(fn ($div) => $div->payment_date->lte($referenceDate))
            //     ->sum('amount');
            $dividendosAteData = $stocks->sum(function ($stock) use ($referenceDate) {
            return $stock->dividends
                ->filter(fn ($div) => $div->payment_date->lte($referenceDate))
                ->sum(function ($div) use ($stock) {
                    $quantidadeNaData = $stock->lots
                        ->filter(fn ($lot) => $lot->purchased_at->lte($div->payment_date))
                        ->sum('quantity');

                    return $quantidadeNaData * $div->amount;
                });

});


            return [
                'date' => $referenceDate->format('d/m'),
                'mercado' => round($mercado, 2),
                // 'comDividendos' => round($mercado + $dividendosAteData, 2),
                'comDividendos' => round($mercado + $dividendosAteData, 2),
            ];
        });

        return Inertia::render('Dashboard', [
            'stocks' => $stocks,
            'netWorthHistory' => $dailyHistory,
            'stocksHistory' => [
                'categories' => $dailyHistory->pluck('date'),
                'series' => $stocksHistorySeries,
            ],
            'dailyHistory' => $dailyHistory,
        ]);
    }
}
