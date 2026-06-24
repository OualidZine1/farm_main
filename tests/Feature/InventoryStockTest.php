<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Field;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_adding_stock_updates_quantity_and_writes_audit_log(): void
    {
        $user = User::factory()->create(['role' => 'administrator']);
        $category = Category::create(['name' => 'Fertilizer']);
        $product = Product::create([
            'name' => 'NPK',
            'category_id' => $category->id,
            'current_quantity' => 0,
            'min_quantity' => 5,
            'price' => 0,
        ]);

        $this->actingAs($user)
            ->post(route('products.add-stock', $product), [
                'quantity' => 10,
                'price' => 25,
                'date' => now()->toDateString(),
                'notes' => 'Delivery',
            ])
            ->assertSessionHas('success');

        $this->assertSame(10, $product->fresh()->current_quantity);
        $this->assertDatabaseHas(InventoryTransaction::class, [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
        ]);
        $this->assertDatabaseHas(AuditLog::class, [
            'event' => 'created',
            'auditable_type' => InventoryTransaction::class,
        ]);
    }

    public function test_using_stock_cannot_exceed_current_quantity(): void
    {
        $user = User::factory()->create(['role' => 'administrator']);
        $worker = User::factory()->create(['role' => 'manager']);
        $category = Category::create(['name' => 'Seeds']);
        $field = Field::create(['bloc_number' => 'A1', 'crop_type' => 'Wheat', 'location' => 'North']);
        $product = Product::create([
            'name' => 'Wheat Seed',
            'category_id' => $category->id,
            'current_quantity' => 3,
            'min_quantity' => 5,
            'price' => 15,
        ]);
        InventoryTransaction::create([
            'product_id' => $product->id,
            'user_id' => $user->id,
            'type' => 'in',
            'quantity' => 3,
            'price' => 15,
            'date' => now()->toDateString(),
        ]);

        $this->actingAs($user)
            ->post(route('products.use-stock', $product), [
                'quantity' => 4,
                'field_id' => $field->id,
                'used_by_user_id' => $worker->id,
                'date' => now()->toDateString(),
            ])
            ->assertSessionHasErrors('quantity');

        $this->assertSame(3, $product->fresh()->current_quantity);
    }
}
