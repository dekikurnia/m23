<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoveItemDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'kuantitas', 'move_item_id'
    ];

    public function moveItem()
    {
        return $this->belongsTo(MoveItem::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
