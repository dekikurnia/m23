<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal', 'provider_id', 'item_id', 'kuantitas', 'harga', 'purchase_id'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class, 'item_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class, 'item_id');
    }
}
