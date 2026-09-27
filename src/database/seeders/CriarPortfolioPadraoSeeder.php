<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\PurchaseLot;
use App\Models\User;
use Illuminate\Database\Seeder;

class CriarPortfolioPadraoSeeder extends Seeder
{
    public function run(): void
    {
        User::whereHas('stocks.lots', function ($query) {
            $query->whereNull('portfolio_id');
        })->each(function (User $user) {
            $portfolio = Portfolio::create([
                'user_id' => $user->id,
                'name' => 'Minha carteira',
                'is_default' => true,
            ]);

            // pega os lots através das stocks do usuário
            $lotIds = PurchaseLot::whereIn('stock_id', $user->stocks->pluck('id'))
                ->whereNull('portfolio_id')
                ->pluck('id');

            PurchaseLot::whereIn('id', $lotIds)->update(['portfolio_id' => $portfolio->id]);

            $this->command->info("Portfolio padrão criado para {$user->email} ({$lotIds->count()} lotes vinculados)");
        });
    }
}