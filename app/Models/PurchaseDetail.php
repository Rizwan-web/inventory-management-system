<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_detail';
    protected $primaryKey = 'id_purchase_detail';
    protected $guarded = [];

    public function Product()
    {
        return $this->hasOne(Product::class, 'id_product', 'id_product');
    }
}
