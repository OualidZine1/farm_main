<?php

namespace Tests\Feature;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_factory_works(): void
    {
        $product = Product::factory()->create();
        $this->assertDatabaseHas('products', ['id' => $product->id]);
        $this->assertNotNull($product->category_id);
    }

    public function test_inventory_transaction_factory_is_diverse_in_dates(): void
    {
        // Ensure a user exists
        \App\Models\User::factory()->create();

        $transactions = InventoryTransaction::factory()->count(20)->create();

        $years = $transactions->pluck('date')->map(function ($date) {
            return $date->format('Y');
        })->unique();

        // Check if we have at least 2 distinct years, which suggests diversity
        $this->assertGreaterThanOrEqual(2, $years->count());
    }
}
