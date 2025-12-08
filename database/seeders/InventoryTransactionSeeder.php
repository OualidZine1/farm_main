<?php

namespace Database\Seeders;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\Field;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InventoryTransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create necessary data
        $admin = User::where('email', 'oualid.zine@uit.ac.ma')->first();
        $user = User::where('email', 'user@example.com')->first();
        
        // Get or create a category
        $category = \App\Models\Category::firstOrCreate(
            ['name' => 'Agricultural Inputs']
        );

        // Get or create some products
        $products = [
            Product::firstOrCreate(['name' => 'Fertilizer'], [
                'category_id' => $category->id,
                'current_quantity' => 0,
            ]),
            Product::firstOrCreate(['name' => 'Pesticide'], [
                'category_id' => $category->id,
                'current_quantity' => 0,
            ]),
            Product::firstOrCreate(['name' => 'Wheat Seeds'], [
                'category_id' => $category->id,
                'current_quantity' => 0,
            ]),
        ];

        // Get or create some fields
        $fields = [
            Field::firstOrCreate(['bloc_number' => 'A1'], [
                'crop_type' => 'Wheat',
                'location' => 'North field',
            ]),
            Field::firstOrCreate(['bloc_number' => 'B2'], [
                'crop_type' => 'Corn',
                'location' => 'South field',
            ]),
        ];

        // Clear existing transactions
        InventoryTransaction::truncate();

        // Generate transactions for the past 6 months
        $now = now();
        
        foreach (range(1, 6) as $monthOffset) {
            $date = $now->copy()->subMonths(6 - $monthOffset);
            
            // Add stock at the beginning of each month
            foreach ($products as $product) {
                $quantity = rand(50, 200);
                
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'user_id' => $admin->id,
                    'type' => 'in',
                    'quantity' => $quantity,
                    'date' => $date->copy()->startOfMonth(),
                    'notes' => 'Monthly restock',
                ]);
                
                // Update product stock
                $product->increment('current_quantity', $quantity);
                
                // Generate usage transactions throughout the month
                $usageCount = rand(3, 8);
                $remainingQuantity = $quantity;
                
                for ($i = 1; $i <= $usageCount; $i++) {
                    if ($remainingQuantity <= 0) break;
                    
                    $usageQty = min(rand(5, $quantity / 3), $remainingQuantity);
                    $remainingQuantity -= $usageQty;
                    
                    InventoryTransaction::create([
                        'product_id' => $product->id,
                        'user_id' => $user->id,
                        'type' => 'out',
                        'quantity' => $usageQty,
                        'field_id' => $fields[array_rand($fields)]->id,
                        'date' => $date->copy()->startOfMonth()->addDays(rand(1, 25)),
                        'notes' => 'Field application',
                    ]);
                    
                    // Update product stock
                    $product->decrement('current_quantity', $usageQty);
                }
            }
        }
        
        $this->command->info('Generated 6 months of sample inventory transactions');
    }
}
