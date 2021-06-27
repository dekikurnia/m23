<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoveItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor',
        'tanggal'
    ];

    public function moveItemDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'move_item_id');
    }
}
