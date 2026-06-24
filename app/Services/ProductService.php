<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Add stock to a product and create an inventory transaction
     */
    public function addStock(Product $product, array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($product, $data) {
            $this->validateStockOperation($data);

            $product->increment('current_quantity', $data['quantity']);

            // Use normalized type values ('in' / 'out') to match other code
            return InventoryTransaction::create([
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'type' => 'in',
                'notes' => $data['notes'] ?? null,
                'date' => $data['date'] ?? now(),
                'user_id' => Auth::id(),
                'price' => $data['price'] ?? null,
            ]);
        });
    }

    /**
     * Use stock from a product and create an inventory transaction
     */
    public function useStock(Product $product, array $data): InventoryTransaction
    {
        return DB::transaction(function () use ($product, $data) {
            $this->validateStockOperation($data);

            if ($product->current_quantity < $data['quantity']) {
                throw new \Exception('Insufficient stock');
            }

            $product->decrement('current_quantity', $data['quantity']);

            // Include used_by_user_id when provided in data
            $payload = [
                'product_id' => $product->id,
                'quantity' => $data['quantity'],
                'type' => 'out',
                'notes' => $data['notes'] ?? null,
                'date' => $data['date'] ?? now(),
                'user_id' => Auth::id(),
            ];

            if (isset($data['used_by_user_id'])) {
                $payload['used_by_user_id'] = $data['used_by_user_id'];
            }

            if (isset($data['field_id'])) {
                $payload['field_id'] = $data['field_id'];
            }

            return InventoryTransaction::create($payload);
        });
    }

    /**
     * Validate stock operation data
     */
    private function validateStockOperation(array $data): void
    {
        if (! isset($data['quantity']) || $data['quantity'] <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        if (isset($data['date']) && ! strtotime($data['date'])) {
            throw new \InvalidArgumentException('Invalid date format');
        }
    }
}
