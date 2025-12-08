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
        'source_transaction_id' // For tracking which 'in' transaction this 'out' is using
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['effective_price', 'total_cost'];

    protected $casts = [
        'date' => 'date',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }
    
    /**
     * Get the user who created this transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the source transaction that this transaction is using stock from (for 'out' transactions)
     */
    public function sourceTransaction()
    {
        return $this->belongsTo(InventoryTransaction::class, 'source_transaction_id');
    }

    /**
     * Get all transactions that are using stock from this transaction (for 'in' transactions)
     */
    public function usageTransactions()
    {
        return $this->hasMany(InventoryTransaction::class, 'source_transaction_id');
    }

    /**
     * Get the effective price for this transaction
     * For 'in' transactions, returns the actual price
     * For 'out' transactions, returns the price from the source transaction
     *
     * @return float
     */
    public function getEffectivePriceAttribute()
    {
        if ($this->type === 'in') {
            return $this->price;
        }
        
        return $this->sourceTransaction ? $this->sourceTransaction->price : 0;
    }
    
    /**
     * Get the total cost for this transaction
     *
     * @return float
     */
    public function getTotalCostAttribute()
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
    
    // Duplicate methods removed - these are already defined earlier in the file

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function getTypeNameAttribute()
    {
        return $this->type === 'in' ? 'Stock In' : 'Stock Out';
    }
}
