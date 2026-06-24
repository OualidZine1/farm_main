<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::query();
        $totalProducts = (clone $products)->count();
        $lowStockCount = Product::lowStock()->count();
        $inventoryValue = Product::all()->sum(function (Product $product) {
            return $product->current_quantity * $product->average_price;
        });

        // Top 5 most used products (by quantity 'out')
        $topProducts = InventoryTransaction::with('product')
            ->selectRaw('product_id, SUM(quantity) as total_used')
            ->where('type', 'out')
            ->groupBy('product_id')
            ->orderByDesc('total_used')
            ->take(5)
            ->get();

        // Last 10 transactions
        $recentTransactions = InventoryTransaction::with(['product', 'user'])
            ->orderByDesc('date')
            ->take(10)
            ->get();

        // Mini version of the stock in/out chart (last 6 months)
        $months = 6;
        $labels = [];
        $inData = [];
        $outData = [];
        $now = Carbon::now();
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i)->format('Y-m');
            $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $inData[] = InventoryTransaction::where('type', 'in')
                ->whereYear('date', Carbon::createFromFormat('Y-m', $month)->year)
                ->whereMonth('date', Carbon::createFromFormat('Y-m', $month)->month)
                ->sum('quantity');
            $outData[] = InventoryTransaction::where('type', 'out')
                ->whereYear('date', Carbon::createFromFormat('Y-m', $month)->year)
                ->whereMonth('date', Carbon::createFromFormat('Y-m', $month)->month)
                ->sum('quantity');
        }
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Stock In',
                    'backgroundColor' => 'rgba(25,135,84,0.7)',
                    'data' => $inData,
                ],
                [
                    'label' => 'Stock Out',
                    'backgroundColor' => 'rgba(220,53,69,0.7)',
                    'data' => $outData,
                ],
            ],
        ];

        return view('home', [
            'topProducts' => $topProducts,
            'recentTransactions' => $recentTransactions,
            'chartData' => $chartData,
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
            'inventoryValue' => $inventoryValue,
        ]);
    }
}
