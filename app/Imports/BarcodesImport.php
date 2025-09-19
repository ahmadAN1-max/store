<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarcodesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $sku     = $row['sku']   ?? null;
            $size    = $row['size']  ?? null;
            $barcode = $row['barcode'] ?? null;

            if (!$sku) {
                continue; // إذا ما في SKU → نتجاهل
            }

            // جِب child product فقط
            $query = Product::where('SKU', trim($sku))
                ->whereNotNull('parent_id');

            if ($size) {
                $query->where('sizes', trim($size));
            }

            $product = $query->first();

            if (!$product) {
                continue; // إذا ما لقى → تجاهل (ما نعمل insert أبداً)
            }

            // إذا الباركود موجود → عدل
            if ($barcode !== null && $barcode !== '') {
                $barcode = trim((string)$barcode);

                // تصحيح الباركود لو إكسل عاملو رقم (مثال: 12345.0)
                if (preg_match('/^\d+(\.0+)?$/', $barcode)) {
                    $barcode = preg_replace('/\.0+$/', '', $barcode);
                }

                $product->barcode = $barcode;
                $product->save();
            }

            // إذا فاضي → منتركه مثل ما هو
        }
    }
}
