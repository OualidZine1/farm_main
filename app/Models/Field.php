<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    protected $fillable = ['bloc_number', 'crop_type', 'location'];

    public function transactions() {
        return $this->hasMany(InventoryTransaction::class);
    }
}
