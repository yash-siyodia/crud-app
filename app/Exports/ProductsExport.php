<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    protected $ids;

    public function __construct($ids = null)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        if ($this->ids) {
            return Product::whereIn('id', $this->ids)->get(['name', 'quantity', 'price']);
        }

        return Product::all(['name', 'quantity', 'price']);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Quantity',
            'Price',
        ];
    }
}
