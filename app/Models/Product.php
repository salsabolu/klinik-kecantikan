<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'harga',
        'stok',
    ];

    // Relasi
    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function stockHistories()
    {
        return $this->hasMany(ProductStockHistory::class)->latest();
    }
}
