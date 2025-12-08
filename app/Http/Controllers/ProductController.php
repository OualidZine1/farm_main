<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Field;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

use App\Services\ProductService;
use App\Services\CacheService;
use App\Exceptions\AppException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    protected $productService;
    protected $cacheService;

    public function __construct(ProductService $productService, CacheService $cacheService)
    {
        $this->productService = $productService;
        $this->cacheService = $cacheService;
        $this->middleware('auth');
    }

    public function getLowStock(Request $request)
    {
        $products = Product::with('category')
            ->where('current_quantity', '<', 'min_quantity')
            ->orderBy('current_quantity', 'asc')
            ->paginate($request->query('length', 10));

        return response()->json([
            'data' => $products->items(),
            'recordsTotal' => $products->total(),
            'recordsFiltered' => $products->total(),
            'draw' => $request->query('draw', 1)
        ]);
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $products = $this->cacheService->cacheProductSearch($request->search);
        } else {
            $products = Product::with('category')
                ->when($request->category_id, function($query) use ($request) {
                    $query->where('category_id', $request->category_id);
                })
                ->orderBy('name')
                ->paginate(10);
        }

        $categories = $this->cacheService->cacheCategories();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = $this->cacheService->cacheCategories();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'current_quantity' => 'required|integer|min:0',
            'price' => 'required_if:current_quantity,>,0|numeric|min:0',
            'description' => 'nullable|string',
        ], [
            'price.required_if' => 'The price per unit is required when adding initial quantity.',
            'price.numeric' => 'The price must be a valid number.',
            'price.min' => 'The price cannot be negative.',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create($request->only(['name', 'category_id', 'current_quantity', 'description']));
            
            // If initial quantity is provided, create an initial stock transaction
            if ($product->current_quantity > 0) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'in',
                    'quantity' => $product->current_quantity,
                    'price' => $validated['price'],
                    'date' => now(),
                    'notes' => 'Initial stock'
                ]);
            }
            
            $this->cacheService->flush();
            DB::commit();
            
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new AppException('Failed to create product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
        ]);

        try {
            // Don't update current_quantity directly as it's managed by transactions
            $product->update($validated);
            $this->cacheService->flush();
            
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            throw new AppException('Failed to update product: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();
        try {
            // Delete all related inventory transactions first
            $product->transactions()->delete();
            $product->delete();
            $this->cacheService->flush(); // Clear cache after deleting product
            DB::commit();
            return redirect()->route('products.index')
                ->with('success', 'Product and its transactions deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new AppException('Failed to delete product: ' . $e->getMessage(), 500);
        }
    }



    /**
     * Deduct stock from a product and record the transaction using FIFO method.
     */
    public function useStock(Request $request, Product $product)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'quantity' => "required|integer|min:1|max:$product->current_quantity",
                'field_id' => 'required|exists:fields,id',
                'used_by_user_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'notes' => 'nullable|string',
            ]);

            // Get available stock (FIFO) - only 'in' transactions with remaining quantity
            $availableStock = $product->transactions()
                ->where('type', 'in')
                ->where('quantity', '>', 0)
                ->orderBy('date')
                ->orderBy('created_at')
                ->get();

            $remainingQuantity = $request->quantity;
            $totalCost = 0;
            $transactionsToCreate = [];

            // First pass: Calculate how much to take from each source transaction
            foreach ($availableStock as $stock) {
                if ($remainingQuantity <= 0) break;

                $quantityToUse = min($remainingQuantity, $stock->quantity);
                $totalCost += $quantityToUse * $stock->price;
                $remainingQuantity -= $quantityToUse;

                // Store the quantity to use from this source transaction
                $transactionsToCreate[] = [
                    'source_transaction_id' => $stock->id,
                    'quantity' => $quantityToUse,
                    'price' => $stock->price,
                ];
            }

            // If we don't have enough stock, throw an exception (should be caught by validation)
            if ($remainingQuantity > 0) {
                throw new \Exception('Not enough stock available');
            }

            // Reduce product quantity
            $product->decrement('current_quantity', $request->quantity);

            // Create the out transaction
            $outTransaction = InventoryTransaction::create([
                'product_id' => $product->id,
                'price' => $totalCost / $request->quantity, // Average price per unit for reference
                'user_id' => auth()->id() ?? 1,
                'used_by_user_id' => $request->used_by_user_id,
                'type' => 'out',
                'quantity' => $request->quantity,
                'field_id' => $request->field_id,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            // Create usage records for each source transaction
            foreach ($transactionsToCreate as $txn) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'price' => $txn['price'],
                    'user_id' => auth()->id() ?? 1,
                    'used_by_user_id' => $request->used_by_user_id,
                    'type' => 'out',
                    'quantity' => $txn['quantity'],
                    'field_id' => $request->field_id,
                    'date' => $request->date,
                    'notes' => $request->notes,
                    'source_transaction_id' => $txn['source_transaction_id'],
                ]);

                // Reduce the source transaction's quantity
                InventoryTransaction::where('id', $txn['source_transaction_id'])
                    ->decrement('quantity', $txn['quantity']);
            }

            $this->cacheService->flush();
            DB::commit();
            return back()->with('success', 'Stock used!');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new AppException('Failed to use stock: ' . $e->getMessage(), 500);
        }
    }
    public function showAddStockForm(Product $product)
    {
        return view('products.add-stock', compact('product'));
    }

    /**
     * Add stock to a product and record the transaction (type: in).
     */
    public function addStock(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $product->current_quantity += $request->quantity;
            $product->save();

            InventoryTransaction::create([
                'product_id' => $product->id,
                'price' => $request->price,
                'user_id' => auth()->check() ? auth()->id() : 1,
                'type' => 'in',
                'quantity' => $request->quantity,
                'date' => $request->date,
                'notes' => $request->notes,
            ]);

            $this->cacheService->flush();
            DB::commit();
            return back()->with('success', 'Stock added!');
        } catch (\Exception $e) {
            DB::rollBack();
            throw new AppException('Failed to add stock: ' . $e->getMessage(), 500);
        }
    }

    public function showUseStockForm(Product $product)
    {
        $fields = Field::all();
        $users = User::all();
        return view('products.use-stock', compact('product', 'fields', 'users'));
    }

    /**
     * Show the transaction history for a product (add/out stock).
     */
    public function transactionHistory(Product $product)
    {
        try {
            $transactions = $product->transactions()
                ->with(['field', 'usedBy', 'enteredBy'])
                ->orderByDesc('date')
                ->paginate(15);

            return view('products.transaction-history', compact('product', 'transactions'));
        } catch (\Throwable $e) {
            \Log::error('Transaction history error: ' . $e->getMessage(), ['exception' => $e]);
            abort(500, 'Transaction history error: ' . $e->getMessage());
        }
    }
}
