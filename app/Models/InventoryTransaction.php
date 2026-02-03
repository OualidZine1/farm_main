<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',          // Who recorded the transaction
        'used_by_user_id',  // Who physically used the product
        'type',             // 'in' or 'out'
        'quantity',
        'field_id',         // Only for 'out' transactions
        'date',
        'notes',
        'price',
        'source_transaction_id', // For tracking which 'in' transaction this 'out' is using
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['effective_price', 'total_cost'];

    protected $casts = [
        'date' => 'date',
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'product_id' => 'integer',
        'user_id' => 'integer',
        'used_by_user_id' => 'integer',
        'field_id' => 'integer',
        'source_transaction_id' => 'integer',
    ];

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function enteredBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    /**
     * Get the user who created this transaction.
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function field(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the source transaction that this transaction is using stock from (for 'out' transactions)
     */
    public function sourceTransaction(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InventoryTransaction::class, 'source_transaction_id');
    }

    /**
     * Get all transactions that are using stock from this transaction (for 'in' transactions)
     */
    public function usageTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'source_transaction_id');
    }

    /**
     * Get the effective price for this transaction
     * For 'in' transactions, returns the actual price
     * For 'out' transactions, returns the price from the source transaction
     */
    public function getEffectivePriceAttribute(): float
    {
        if ($this->type === 'in') {
            return (float) $this->price;
        }

        return $this->sourceTransaction ? (float) $this->sourceTransaction->price : 0;
    }

    /**
     * Get the total cost for this transaction
     */
    public function getTotalCostAttribute(): float
    {
        return $this->quantity * $this->effective_price;
    }

    public function scopeIncoming($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOutgoing($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function getTypeNameAttribute(): string
    {
        return $this->type === 'in' ? 'Stock In' : 'Stock Out';
    }
}
