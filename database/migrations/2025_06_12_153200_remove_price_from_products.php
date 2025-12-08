<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, ensure all in transactions have prices
        \DB::table('inventory_transactions')
            ->where('inventory_transactions.type', 'in')
            ->whereNull('inventory_transactions.price')
            ->join('products', 'inventory_transactions.product_id', '=', 'products.id')
            ->update([
                'inventory_transactions.price' => \DB::raw('products.price')
            ]);

        // Now remove the price column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->after('current_quantity');
        });
    }
};
