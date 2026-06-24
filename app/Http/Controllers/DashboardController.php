<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        $stats = [
            'totalProducts' => Product::count(),
            'totalStockValue' => Product::sum('current_quantity'),
            'recentTransactions' => InventoryTransaction::count(),
            'lowStockItems' => Product::where('current_quantity', '<', 'min_quantity')->count(),
        ];

        return response()->json($stats);
    }

    public function getStockHistory(Request $request)
    {
        $period = $request->query('period', 30);
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subDays($period);

        $transactions = InventoryTransaction::whereBetween('date', [$startDate, $endDate])
            ->with(['product'])
            ->get();

        $dates = [];
        $values = [];

        $currentDate = $startDate;
        while ($currentDate->lte($endDate)) {
            $dates[] = $currentDate->format('Y-m-d');
            $values[] = $this->calculateStockValue($currentDate);
            $currentDate->addDay();
        }

        return response()->json([
            'dates' => $dates,
            'values' => $values,
        ]);
    }

    public function getCategoryDistribution()
    {
        $categories = Category::withCount('products')->get();

        $labels = $categories->pluck('name')->toArray();
        $values = $categories->pluck('products_count')->toArray();

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }

    private function calculateStockValue($date)
    {
        $transactions = InventoryTransaction::where('date', '<=', $date)
            ->with(['product'])
            ->get();

        $stockValue = 0;
        $productStocks = collect();

        foreach ($transactions as $transaction) {
            $product = $transaction->product;
            if ($transaction->type === 'stock_in') {
                $productStocks[$product->id] = ($productStocks[$product->id] ?? 0) + $transaction->quantity;
            } else {
                $productStocks[$product->id] = max(0, ($productStocks[$product->id] ?? 0) - $transaction->quantity);
            }
        }

        foreach ($productStocks as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $stockValue += $quantity * $product->price;
            }
        }

        return $stockValue;
    }
}
