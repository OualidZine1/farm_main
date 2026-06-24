<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category_id', 'current_quantity', 'min_quantity', 'price', 'description'];

    protected $appends = ['average_price'];

    protected $casts = [
        'current_quantity' => 'integer',
        'min_quantity' => 'integer',
        'category_id' => 'integer',
        'price' => 'decimal:2',
        'average_price' => 'float',
    ];
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saving(function ($product) {
            // Ensure current_quantity is never negative
            $product->current_quantity = max(0, $product->current_quantity);
        });
    }

    public function getAveragePriceAttribute(): float
    {
        $inTransactions = $this->transactions()
            ->where('type', 'in')
            ->where('quantity', '>', 0)
            ->get();

        if ($inTransactions->isEmpty()) {
            return 0;
        }

        $totalCost = $inTransactions->sum(function ($transaction) {
            return $transaction->price * $transaction->quantity;
        });

        $totalQuantity = $inTransactions->sum('quantity');

        return $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_quantity <= 0) {
            return 'empty';
        }

        if ($this->current_quantity <= $this->min_quantity) {
            return 'low';
        }

        if ($this->current_quantity <= ($this->min_quantity * 2)) {
            return 'watch';
        }

        return 'ok';
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_quantity', '<=', 'min_quantity');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}
