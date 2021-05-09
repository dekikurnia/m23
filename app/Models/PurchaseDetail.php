<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'tanggal', 'provider_id', 'item_id', 'kuantitas', 'harga', 'sub_total', 'purchase_id'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
