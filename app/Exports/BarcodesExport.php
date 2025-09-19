<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BarcodesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::whereNotNull('parent_id') // children only
    ->where(function($q) {
        $q->whereNull('barcode')
          ->orWhere('barcode', '');
    })
    ->select('name', 'SKU', 'sizes', 'barcode')
    ->get();

    }

    public function headings(): array
    {
        return [
            'Name',
            'SKU',
            'Size',
            'Barcode',
        ];
    }
}
