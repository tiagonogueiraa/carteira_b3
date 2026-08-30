<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Stocks/Index', [
            'stocks' => $request->user()->stocks()->with('lots')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Stocks/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ticker' => 'required|string|max:10',
            'type' => 'required|in:acao,fii',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0.01',
            'purchased_at' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $stock = $request->user()->stocks()->firstOrCreate(
                ['ticker' => strtoupper($validated['ticker'])],
                ['type' => $validated['type']],
            );

            $stock->lots()->create([
                'quantity' => $validated['quantity'],
                'price' => $validated['price'],
                'purchased_at' => $validated['purchased_at'],
            ]);
        });

        return redirect()->route('stocks.index');
    }

    public function show(Stock $stock)
    {
        Gate::authorize('view', $stock);

        return Inertia::render('Stocks/Show', [
            'stock' => $stock->load('lots'),
        ]);
    }

    public function edit(Stock $stock)
    {
        Gate::authorize('update', $stock);

        return Inertia::render('Stocks/Edit', [
            'stock' => $stock->load('lots'),
        ]);
    }

    public function update(Request $request, Stock $stock)
    {
        Gate::authorize('update', $stock);

        $validated = $request->validate([
            'ticker' => 'required|string|max:10',
            'type' => 'required|in:acao,fii',
        ]);

        $stock->update([
            'ticker' => strtoupper($validated['ticker']),
            'type' => $validated['type'],
        ]);

        return redirect()->route('stocks.index');
    }

    public function destroy(Stock $stock)
    {
        Gate::authorize('delete', $stock);

        $stock->delete();

        return redirect()->route('stocks.index');
    }
}