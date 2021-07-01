<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $casts = [
        'is_lunas' => 'boolean',
    ];

    const STATUS_COLOR = [
        0  => '#ffc107',
        1   => '#5cb85c',
    ];

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
