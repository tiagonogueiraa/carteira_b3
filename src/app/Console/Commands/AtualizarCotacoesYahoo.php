<?php

namespace App\Console\Commands;

use App\Models\Stock;
use App\Models\StockSyncLog;
use App\Models\StockMarket;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Services\YahooFinanceService;

#[Signature('app:atualizar-cotacoes-yahoo')]
#[Description('Command description')]
class AtualizarCotacoesYahoo extends Command
{
   protected $signature = 'app:atualizar-cotacoes-yahoo';

    public function handle(YahooFinanceService $yahoo)
    {
        $tickers = Stock::distinct()->pluck('ticker');

        foreach ($tickers as $ticker) {
            $dados = $yahoo->buscarCotacao($ticker);
            
            StockSyncLog::create([
                'ticker' => $ticker,
                'status' => $dados ? 'success' : 'error',
                'source' => 'yahoo',
                'response' => $dados,
            ]);

            if ($dados) {
                $stock = Stock::where('ticker', $ticker)->first();

                StockMarket::create([
                    'stock_id' => $stock->id,
                    'short_name' => $dados['shortName'] ?? null,
                    'long_name' => $dados['longName'] ?? null,
                    'currency' => $dados['currency'] ?? null,
                    'regular_market_price' => $dados['regularMarketPrice'] ?? null,
                    'regular_market_change' => $dados['regularMarketChange'] ?? null,
                    'regular_market_change_percent' => $dados['regularMarketChangePercent'] ?? null,
                    'regular_market_day_high' => $dados['regularMarketDayHigh'] ?? null,
                    'regular_market_day_low' => $dados['regularMarketDayLow'] ?? null,
                    'regular_market_volume' => $dados['regularMarketVolume'] ?? null,
                    'regular_market_time' => $dados['regularMarketTime'] ?? null,
                    'market_cap' => $dados['marketCap'] ?? null,
                ]);
            }
            sleep(rand(2, 5)); // espaçamento entre requisições

            $this->info("{$ticker}: " . ($dados ? 'atualizado' : 'falhou'));
        }
    }
}
