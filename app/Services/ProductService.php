<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ProductService
{
    public function addStock(Product $product, array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($product, $data) {
            $this->validateStockOperation($data);
            
            $product->increment('current_quantity', $data['quantity']);
            
            return InventoryTransaction::create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'type' => 'stock_in',
                'notes' => $data['notes'] ?? null,
                'date' => $data['date'] ?? now(),
                'user_id' => Auth::id(),
            ]);
        });
    }

    public function useStock(Product $product, array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($product, $data) {
            $this->validateStockOperation($data);
            
            if ($product->current_quantity < $data['quantity']) {
                throw new \Exception('Insufficient stock');
            }
            
            $product->decrement('current_quantity', $data['quantity']);
            
            return InventoryTransaction::create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'type' => 'stock_out',
                'notes' => $data['notes'] ?? null,
                'date' => $data['date'] ?? now(),
                'user_id' => Auth::id(),
            ]);
        });
    }

    private function validateStockOperation(array $data): void
    {
        if (!isset($data['quantity']) || $data['quantity'] <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        if (isset($data['date']) && !strtotime($data['date'])) {
            throw new \InvalidArgumentException('Invalid date format');
        }
    }
}
