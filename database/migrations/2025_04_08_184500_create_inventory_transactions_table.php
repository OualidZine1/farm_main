<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('used_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['in', 'out']);
            $table->integer('quantity');
            $table->decimal('price', 10, 2)->nullable();
            $table->unsignedBigInteger('source_transaction_id')->nullable();
            $table->foreignId('field_id')->nullable()->constrained()->nullOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('source_transaction_id')
                ->references('id')
                ->on('inventory_transactions')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};
