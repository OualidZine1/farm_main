<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\ProductUsageExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class InventoryTransactionController extends Controller
{
    /**
     * Display the specified inventory transaction.
     */
    public function show($id)
    {
        $transaction = InventoryTransaction::with(['product', 'field', 'enteredBy', 'usedBy'])->findOrFail($id);
        return view('transactions.show', compact('transaction'));
    }
    public function index(Request $request)
    {
        $transactions = InventoryTransaction::with([
            'product',
            'field',
            'enteredBy',
            'usedBy'
        ])
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            })
            ->when($request->type, function($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->product_id, function($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            })
            ->when($request->field_id, function($query) use ($request) {
                return $query->where('field_id', $request->field_id);
            })
            ->when($request->used_by_user_id, function($query) use ($request) {
                return $query->where('used_by_user_id', $request->used_by_user_id);
            })
            ->orderBy('date', 'desc')
            ->paginate(25);

        $summary = InventoryTransaction::select([
            DB::raw('SUM(CASE WHEN type = "in" THEN quantity ELSE 0 END) as total_in'),
            DB::raw('SUM(CASE WHEN type = "out" THEN quantity ELSE 0 END) as total_out')
        ])
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            })
            ->first();

        // Calculate total cost of products based on transaction prices
        $totalCost = 0;
        foreach ($transactions as $transaction) {
            $totalCost += $transaction->quantity * $transaction->price;
        }

        return view('transactions.index', [
            'transactions' => $transactions,
            'products' => Product::orderBy('name')->get(),
            'fields' => Field::orderBy('bloc_number')->get(),
            'users' => User::all(),
            'summary' => $summary,
            'totalCost' => $totalCost,
            'filters' => $request->all()
        ]);
    }

    public function getRecentTransactions(Request $request)
    {
        $transactions = InventoryTransaction::with(['product', 'user'])
            ->orderBy('date', 'desc')
            ->paginate($request->query('length', 10));

        return response()->json([
            'data' => $transactions->items(),
            'recordsTotal' => $transactions->total(),
            'recordsFiltered' => $transactions->total(),
            'draw' => $request->query('draw', 1)
        ]);
    }

    public function export(Request $request)
    {
        $transactions = InventoryTransaction::with([
            'product',
            'field',
            'enteredBy',
            'usedBy'
        ])
            ->when($request->start_date, function($query) use ($request) {
                return $query->where('date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                return $query->where('date', '<=', $request->end_date);
            })
            ->when($request->type, function($query) use ($request) {
                return $query->where('type', $request->type);
            })
            ->when($request->product_id, function($query) use ($request) {
                return $query->where('product_id', $request->product_id);
            })
            ->when($request->field_id, function($query) use ($request) {
                return $query->where('field_id', $request->field_id);
            })
            ->orderBy('date', 'desc')
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=transactions_export.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Date', 'Product', 'Type', 'Quantity', 'Price', 'Subtotal',
                'Field/Block', 'Crop Type', 'Notes',
                'Recorded By', 'Used By'
            ]);

            // Calculate total cost
            $totalCost = 0;
            foreach ($transactions as $t) {
                $subtotal = $t->quantity * $t->price;
                $totalCost += $subtotal;

                fputcsv($file, [
                    $t->date->format('Y-m-d'),
                    $t->product->name,
                    strtoupper($t->type),
                    $t->quantity,
                    number_format($t->price, 2),
                    number_format($subtotal, 2),
                    $t->field ? $t->field->bloc_number : 'N/A',
                    $t->field ? $t->field->crop_type : 'N/A',
                    $t->notes,
                    $t->enteredBy->name,
                    $t->usedBy ? $t->usedBy->name : 'N/A'
                ]);
            }

            // Add empty row as separator
            fputcsv($file, []);

            // Add total cost row
            fputcsv($file, [
                '', '', '', '', 'TOTAL COST:', number_format($totalCost, 2)
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function dashboard(Request $request)
    {
        // Determine period (months) - allow 6 (rolling) or 12 (calendar year)
        $months = in_array($request->get('months'), [12]) ? 12 : 6;

        if ($months === 12) {
            // Full calendar year starting January
            $year = (int)($request->get('year') ?? now()->year);
            if ($year < 2000 || $year > now()->year) {
                $year = now()->year;
            }

            $startDate = Carbon::createFromDate($year, 1, 1)->startOfDay();
            $endDate   = Carbon::createFromDate($year, 12, 31)->endOfDay();
            $currentMonth = Carbon::createFromDate($year, 12, 1);
        } else {
            // Rolling last 6 months from today
            $endDate   = now()->endOfDay();
            $startDate = $endDate->copy()->subMonths(5)->startOfMonth();
            $currentMonth = $endDate->copy()->startOfMonth();
            $year = null;
        }

        // Get transactions for selected period
        $transactions = InventoryTransaction::with(['product', 'field', 'user'])
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();

        // Debug: Log the raw transactions
        \Log::info('Raw transactions:', $transactions->toArray());

        // Group transactions by month
        $monthlyData = [];
        // $currentMonth = $endDate->copy()->startOfMonth();
        
        // Initialize data for the selected months
        for ($i = $months-1; $i >= 0; $i--) {
            $month = $currentMonth->copy()->subMonths($i);
            $monthKey = $month->format('Y-m');
            $monthlyData[$monthKey] = [
                'in' => 0,
                'out' => 0,
                'balance' => 0,
                'label' => $month->format('M Y')
            ];
        }
        
        // Debug: Log the initialized monthly data
        \Log::info('Initialized monthly data:', $monthlyData);

        // Calculate monthly in/out and running balance
        $runningBalance = 0;
        
        foreach ($transactions as $transaction) {
            $monthKey = $transaction->date->format('Y-m');
            
            if (isset($monthlyData[$monthKey])) {
                if ($transaction->type === 'in') {
                    $monthlyData[$monthKey]['in'] += $transaction->quantity;
                    $runningBalance += $transaction->quantity;
                } else {
                    $monthlyData[$monthKey]['out'] += $transaction->quantity;
                    $runningBalance -= $transaction->quantity;
                }
                $monthlyData[$monthKey]['balance'] = $runningBalance;
            }
        }

        // Prepare data for the chart
        $chartData = [
            'labels' => array_column($monthlyData, 'label'),
            'datasets' => [
                [
                    'label' => 'Stock In',
                    'data' => array_column($monthlyData, 'in'),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Stock Out',
                    'data' => array_column($monthlyData, 'out'),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1
                ],
                [
                    'label' => 'Running Balance',
                    'data' => array_column($monthlyData, 'balance'),
                    'type' => 'line',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                ]
            ]
        ];
        
        // Debug: Log the prepared chart data
        \Log::info('Prepared chart data:', $chartData);

        // Get recent transactions for the table
        $recentTransactions = InventoryTransaction::with(['product', 'field', 'user'])
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        // Calculate summary stats
        $totalIn = $transactions->where('type', 'in')->sum('quantity');
        $totalOut = $transactions->where('type', 'out')->sum('quantity');
        $currentBalance = $totalIn - $totalOut;

        return view('transactions.dashboard', [
            'chartData' => $chartData,
            'recentTransactions' => $recentTransactions,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut,
            'currentBalance' => $currentBalance,
            'selectedMonths' => $months,
            'selectedYear' => $year,
            'rangeType' => $months
        ]);
            
        return view('transactions.dashboard', [
            'testData' => $testData,
            'recentIns' => $recentIns,
            'recentOuts' => $recentOuts,
            'totalIn' => $totalIn,
            'totalOut' => $totalOut
        ]);

        $topProducts = InventoryTransaction::with('product')
            ->select([
                'product_id',
                DB::raw('SUM(quantity) as total_used')
            ])
            ->where('type', 'out')
            ->groupBy('product_id')
            ->orderBy('total_used', 'desc')
            ->limit(5)
            ->get();

        $recentIns = InventoryTransaction::with(['product', 'enteredBy'])
            ->where('type', 'in')
            ->latest()
            ->limit(5)
            ->get();

        $recentOuts = InventoryTransaction::with(['product', 'field', 'enteredBy', 'usedBy'])
            ->where('type', 'out')
            ->latest()
            ->limit(5)
            ->get();

        return view('transactions.dashboard', [
            'monthlySummary' => $monthlySummary,
            'topProducts' => $topProducts,
            'recentIns' => $recentIns,
            'recentOuts' => $recentOuts,
            'totalIn' => $monthlySummary->sum('total_in'),
            'totalOut' => $monthlySummary->sum('total_out'),
            'runningBalanceData' => $runningBalanceData
        ]);
    }

    /**
     * Generate a report of product usage by field and user
     */
    public function productUsageReport(Request $request)
    {
        $products = Product::orderBy('name')->get();
        $fields = Field::orderBy('bloc_number')->get();
        $users = User::orderBy('firstname')->get();

        $baseQuery = function() use ($request) {
            return InventoryTransaction::with(['product', 'field', 'usedBy'])
                ->where('type', 'out')
                ->when($request->product_id, function($query) use ($request) {
                    return $query->where('product_id', $request->product_id);
                })
                ->when($request->field_id, function($query) use ($request) {
                    return $query->where('field_id', $request->field_id);
                })
                ->when($request->used_by_user_id, function($query) use ($request) {
                    return $query->where('used_by_user_id', $request->used_by_user_id);
                })
                ->when($request->start_date, function($query) use ($request) {
                    return $query->where('date', '>=', $request->start_date);
                })
                ->when($request->end_date, function($query) use ($request) {
                    return $query->where('date', '<=', $request->end_date);
                });
        };

        // Get the base query
        $query = $baseQuery();

        // Group by product, field, and user with quantity calculations
        $usageByProductFieldUser = (clone $query)
            ->select([
                'product_id',
                'field_id',
                'used_by_user_id',
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('COUNT(*) as usage_count')
            ])
            ->groupBy('product_id', 'field_id', 'used_by_user_id')
            ->orderBy('product_id')
            ->orderBy('field_id')
            ->orderBy('used_by_user_id')
            ->get()
            ->load(['product', 'field', 'usedBy']);
            
        // Calculate grand totals
        $grandTotalQuantity = $usageByProductFieldUser->sum('total_quantity');

        // Get all transactions for detailed view
        $transactions = $query->orderBy('date', 'desc');

        // Check if export is requested
        if ($request->has('export')) {
            $allTransactions = $transactions->get();
            return Excel::download(
                new ProductUsageExport($allTransactions, $usageByProductFieldUser),
                'product-usage-report-' . now()->format('Y-m-d') . '.xlsx'
            );
        }

        // Paginate for the web view
        $transactions = $transactions->paginate(25);

        return view('transactions.product-usage-report', [
            'products' => $products,
            'fields' => $fields,
            'users' => $users,
            'usageByProductFieldUser' => $usageByProductFieldUser,
            'transactions' => $transactions,
            'filters' => $request->all(),
            'grandTotalQuantity' => $grandTotalQuantity
        ]);
    }
}
