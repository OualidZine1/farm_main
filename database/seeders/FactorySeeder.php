<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Field;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Database\Seeder;

class FactorySeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = Category::factory()->count(3)->create();

        // Create products
        $products = Product::factory()->count(10)->create([
            'category_id' => $categories->random()->id,
        ]);

        // Create fields
        $fields = Field::factory()->count(5)->create();

        // Create incoming transactions
        InventoryTransaction::factory()->count(20)->incoming()->create([
            'product_id' => $products->random()->id,
        ]);

        // Create outgoing transactions
        InventoryTransaction::factory()->count(30)->outgoing()->create([
            'product_id' => $products->random()->id,
            'field_id' => $fields->random()->id,
        ]);
    }
}
