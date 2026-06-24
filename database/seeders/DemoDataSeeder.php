<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed categories
        $categories = [
            ['name' => 'Seeds'],
            ['name' => 'Fertilizers'],
            ['name' => 'Tools'],
            ['name' => 'Pesticides'],
            ['name' => 'Harvest'],
        ];
        $categoryIds = [];
        foreach ($categories as $cat) {
            $category = Category::create($cat);
            $categoryIds[$category->name] = $category->id;
        }

        // Get admin user for transactions
        $admin = \App\Models\User::where('email', 'oualid.zine@uit.ac.ma')->first();

        // Seed products
        $products = [
            [
                'name' => 'Corn Seed',
                'description' => 'Hybrid corn seed for planting',
                'price' => 10.50,
                'quantity' => 100,
                'category_id' => $categoryIds['Seeds'],
            ],
            [
                'name' => 'Wheat Seed',
                'description' => 'Premium wheat seed',
                'price' => 8.75,
                'quantity' => 150,
                'category_id' => $categoryIds['Seeds'],
            ],
            [
                'name' => 'Urea Fertilizer',
                'description' => 'High-nitrogen fertilizer',
                'price' => 25.00,
                'quantity' => 50,
                'category_id' => $categoryIds['Fertilizers'],
            ],
            [
                'name' => 'NPK Fertilizer',
                'description' => 'Balanced NPK fertilizer',
                'price' => 30.00,
                'quantity' => 60,
                'category_id' => $categoryIds['Fertilizers'],
            ],
            [
                'name' => 'Hoe',
                'description' => 'Steel garden hoe',
                'price' => 12.00,
                'quantity' => 20,
                'category_id' => $categoryIds['Tools'],
            ],
            [
                'name' => 'Shovel',
                'description' => 'Heavy-duty shovel',
                'price' => 15.00,
                'quantity' => 15,
                'category_id' => $categoryIds['Tools'],
            ],
            [
                'name' => 'Insecticide',
                'description' => 'General purpose insecticide',
                'price' => 18.00,
                'quantity' => 40,
                'category_id' => $categoryIds['Pesticides'],
            ],
            [
                'name' => 'Herbicide',
                'description' => 'Weed control herbicide',
                'price' => 20.00,
                'quantity' => 35,
                'category_id' => $categoryIds['Pesticides'],
            ],
            [
                'name' => 'Potato',
                'description' => 'Freshly harvested potatoes',
                'price' => 2.50,
                'quantity' => 200,
                'category_id' => $categoryIds['Harvest'],
            ],
            [
                'name' => 'Tomato',
                'description' => 'Ripe red tomatoes',
                'price' => 3.00,
                'quantity' => 180,
                'category_id' => $categoryIds['Harvest'],
            ],
        ];

        foreach ($products as $productData) {
            // Extract the price and quantity as they're not part of the product anymore
            $price = $productData['price'];
            $quantity = $productData['quantity'];

            // Remove price and quantity from product data
            unset($productData['price'], $productData['quantity']);

            // Create the product
            $product = Product::create($productData);

            // Create initial inventory transaction
            \App\Models\InventoryTransaction::create([
                'product_id' => $product->id,
                'user_id' => $admin->id,
                'type' => 'in',
                'quantity' => $quantity,
                'price' => $price,
                'date' => now(),
                'notes' => 'Initial stock',
            ]);

            // Update product's current_quantity
            $product->current_quantity = $quantity;
            $product->save();
        }

        // Seed demo stock out transactions for dashboard chart
        $allProducts = \App\Models\Product::all();
        $firstField = \App\Models\Field::first();
        $firstUser = \App\Models\User::first();
        if ($firstField && $firstUser) {
            foreach ($allProducts as $product) {
                \App\Models\InventoryTransaction::create([
                    'product_id' => $product->id,
                    'type' => 'out',
                    'quantity' => rand(5, 20),
                    'date' => now()->subDays(rand(1, 30)),
                    'field_id' => $firstField->id,
                    'user_id' => $firstUser->id,
                    'used_by_user_id' => $firstUser->id,
                    'notes' => 'Demo stock out',
                ]);
            }
        }
    }
}
