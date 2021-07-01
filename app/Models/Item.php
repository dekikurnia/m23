<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['provider_id', 'nama', 'category_id'];

    public function stock() {
        return $this->hasOne(Stock::class,'item_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'item_id');
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'item_id');
    }

    public function moveItemDetails()
    {
        return $this->hasMany(MoveItemDetail::class, 'item_id');
    }
}
