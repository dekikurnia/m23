<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice',
        'tanggal',
        'supplier_id',
        'cara_bayar',
        'jatuh_tempo',
        'keterangan',
        'user_id'
    ];

    public function purchaseDetail()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }
}
