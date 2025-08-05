<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranModel extends Model
{
    use HasFactory;

    protected $table = 'tbl_pembayaran';

protected $fillable = [
    'order_id', 'user_id', 'total', 'paid_at', 'metode','produk_id'
];

public function beli_produk()
{
    return $this->belongsTo(BeliProdukModel::class, 'order_id', 'order_id');
}

}
