<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $fillable = ['ticker', 'type'];

    protected $appends = ['quantity', 'average_price', 'logo_url', 'total_dividends'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lots()
    {
        return $this->hasMany(PurchaseLot::class);
    }

    public function getQuantityAttribute(): int
    {
        return $this->lots->sum('quantity');
    }

    public function getAveragePriceAttribute(): float
    {
        $quantity = $this->quantity;

        if ($quantity === 0) {
            return 0;
        }

        return $this->lots->sum(fn ($lot) => $lot->quantity * $lot->price) / $quantity;
    }

    // relacionamento pra sempre pegar a cotação mais recente

    public function market()
    {
        return $this->hasOne(StockMarket::class)->latestOfMany();
    }

    // relacionamento com o catálogo de tickers da B3, casando pelo símbolo
    // (não é FK de id — stocks.ticker bate com b3_tickers.symbol)
    public function b3Ticker()
    {
        return $this->belongsTo(B3Ticker::class, 'ticker', 'symbol');
    }
    // para pegar a logo e associar no ticker
    public function getLogoUrlAttribute(){
        return $this->b3Ticker?->logo_url; // ?-> interrogaão para evitar erro
    }

    public function dividends(){
        return $this->hasMany(StockDividend::class);
    }

    public function getTotalDividendsAttribute(): float
    {

        return $this->dividends->sum(function ($dividendo) {
            $quantidadeNaData = $this->lots
                ->filter(fn ($lot) => $lot->purchased_at->lte($dividendo->payment_date))
                ->sum('quantity');

            return $quantidadeNaData * $dividendo->amount;
        });
        // $totalDividendos = 0;

        // // 2. Passa por CADA dividendo pago dessa ação, um de cada vez
        // foreach ($this->dividends as $dividendo) {

        //     // 3. Pra esse dividendo específico, preciso saber quantas ações
        //     //    eu já tinha na data em que ele foi pago. Começa contando do zero.
        //     $quantidadeNaData = 0;

        //     // 4. Passa por TODOS os lotes de compra dessa ação
        //     foreach ($this->lots as $lote) {

        //         // 5. Só conta esse lote se ele foi comprado ANTES ou NA MESMA
        //         //    data do pagamento do dividendo que estou analisando agora
        //         if ($lote->purchased_at->lte($dividendo->payment_date)) {
        //             $quantidadeNaData += $lote->quantity;
        //         }
        //     }

        //     // 6. Já sei quantas ações eu tinha nessa data -> calculo quanto
        //     //    recebi NESSE dividendo específico
        //     $valorRecebidoNesseDividendo = $quantidadeNaData * $dividendo->amount;

        //     // 7. Soma esse valor no total geral (guardado fora do loop de lotes)
        //     $totalDividendos += $valorRecebidoNesseDividendo;
        // }

        // // 8. Depois de passar por todos os dividendos, devolve o total acumulado
        // return $totalDividendos;
    }

    public function marketHistory()
    {
        return $this->hasMany(StockMarket::class);
    }


}
