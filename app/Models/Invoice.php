<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'invoice_date',
        'subtotal',
        'tax',
        'discount',
        'grand_total',
        'status',
    ];

    /**
     * One invoice has many items
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
