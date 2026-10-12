<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $periodo = $request->get('periodo', '30d');

        $stocks = $request->user()->stocks()->with('lots', 'market', 'marketHistory', 'dividends')->get();

        // Lista de "momentos" de referência — cada um vira um ponto no gráfico.
        // "1d" usa os horários reais já salvos pelo cron (de 30 em 30 min).
        // Os outros períodos geram um ponto por dia.
        if ($periodo === '1d') {
            $momentos = $stocks->flatMap->marketHistory
                ->filter(fn ($m) => $m->created_at->isToday())
                ->pluck('created_at')
                ->unique(fn ($dt) => $dt->format('H:i'))
                ->sort()
                ->values()
                ->map(fn ($momento) => ['label' => $momento->format('H:i'), 'data' => $momento]);
        } else {
            $dias = match ($periodo) {
                '7d' => 7,
                '1y' => 365,
                default => 30,
            };

            $momentos = collect(range($dias - 1, 0))->map(function (int $diasAtras) {
                $data = now()->subDays($diasAtras)->endOfDay();
                return ['label' => $data->format('d/m'), 'data' => $data];
            });
        }

        // Quantidade de uma ação até um momento específico.
        $quantidadeAte = fn ($stock, $momento) => $stock->lots
            ->filter(fn ($lot) => $lot->purchased_at->lte($momento))
            ->sum('quantity');

        // Preço de mercado conhecido até um momento (fallback: preço médio de compra).
        $precoAte = fn ($stock, $momento) => $stock->marketHistory
            ->filter(fn ($m) => $m->created_at->lte($momento))
            ->sortByDesc('created_at')
            ->first()
            ?->regular_market_price ?? $stock->average_price;

        // Dividendos recebidos até um momento, respeitando a quantidade na data de cada pagamento.
        $dividendosAte = fn ($stock, $momento) => $stock->dividends
            ->filter(fn ($div) => $div->payment_date->lte($momento))
            ->sum(function ($div) use ($stock) {
                $qtd = $stock->lots->filter(fn ($l) => $l->purchased_at->lte($div->payment_date))->sum('quantity');
                return $qtd * $div->amount;
            });

        $dailyHistory = $momentos->map(function ($ponto) use ($stocks, $quantidadeAte, $precoAte, $dividendosAte) {
            $mercado = $stocks->sum(fn ($stock) => $quantidadeAte($stock, $ponto['data']) * $precoAte($stock, $ponto['data']));
            $dividendos = $stocks->sum(fn ($stock) => $dividendosAte($stock, $ponto['data']));

            return [
                'date' => $ponto['label'],
                'mercado' => round($mercado, 2),
                'comDividendos' => round($mercado + $dividendos, 2),
            ];
        });

        $stocksHistorySeries = $stocks->map(function ($stock) use ($momentos, $quantidadeAte, $precoAte) {
            return [
                'name' => $stock->ticker,
                'data' => $momentos->map(
                    fn ($ponto) => round($quantidadeAte($stock, $ponto['data']) * $precoAte($stock, $ponto['data']), 2)
                ),
            ];
        })->values();

        return Inertia::render('Dashboard', [
            'stocks' => $stocks,
            'netWorthHistory' => $dailyHistory,
            'stocksHistory' => [
                'categories' => $momentos->pluck('label'),
                'series' => $stocksHistorySeries,
            ],
            'dailyHistory' => $dailyHistory,
            'periodoSelecionado' => $periodo,
        ]);
    }

}
