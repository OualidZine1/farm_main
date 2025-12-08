<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'category_id', 'current_quantity', 'description'];
    
    protected $appends = ['average_price'];
    
    public function getAveragePriceAttribute()
    {
        $inTransactions = $this->transactions()
            ->where('type', 'in')
            ->where('quantity', '>', 0)
            ->get();
            
        if ($inTransactions->isEmpty()) {
            return 0;
        }
        
        $totalCost = $inTransactions->sum(function($transaction) {
            return $transaction->price * $transaction->quantity;
        });
        
        $totalQuantity = $inTransactions->sum('quantity');
        
        return $totalQuantity > 0 ? $totalCost / $totalQuantity : 0;
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function transactions() {
        return $this->hasMany(InventoryTransaction::class);
    }
}
