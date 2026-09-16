<?php

namespace App\Console\Commands;

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

            sleep(rand(2, 5)); // espaçamento entre requisições
        }
    }
}
