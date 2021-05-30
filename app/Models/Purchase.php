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
        'pajak',
        'jatuh_tempo',
        'is_lunas',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'is_lunas' => 'boolean',
    ];

    const STATUS_COLOR = [
        0  => '#ffc107',
        1   => '#5cb85c',
    ];

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
