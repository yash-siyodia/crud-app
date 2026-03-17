<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'product_name',
        'description',
        'quantity',
        'price',
        'total',
    ];

    /**
     * Each item belongs to an invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
