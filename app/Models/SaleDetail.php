<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    /*
    public function stock()
    {
        return $this->hasOne(Stock::class, 'item_id');
    }
    */

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
