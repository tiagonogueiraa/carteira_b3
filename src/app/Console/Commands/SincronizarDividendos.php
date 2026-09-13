<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockDividend;
use App\Services\YahooFinanceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:sincronizar-dividendos')]
#[Description('Command description')]
class SincronizarDividendos extends Command
{
    protected $signature = 'app:sincronizar-dividendos';

    public function handle(YahooFinanceService $yahoo)
    {
        $tickers = Stock::distinct()->pluck('ticker');

        foreach ($tickers as $ticker) {
            $stock = Stock::where('ticker', $ticker)->first();
            $dividendos = $yahoo->buscarDividendos($ticker, anos: 1);

            foreach ($dividendos as $div) {
                StockDividend::updateOrCreate(
                    [
                        'stock_id' => $stock->id,
                        'payment_date' => $div->getDate()->format('Y-m-d'),
                    ],
                    [
                        'amount' => $div->getDividends(),
                        'source' => 'yahoo',
                    ]
                );
            }

            $this->info("{$ticker}: " . count($dividendos) . " dividendos sincronizados");

            sleep(rand(2, 5));
        }
    }
}
