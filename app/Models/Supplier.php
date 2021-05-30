<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public function purchase() {
        return $this->hasOne(Purchase::class, 'supplier_id');
    }
}
