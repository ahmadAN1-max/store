<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return DB::transaction(function () use ($row) {

            // 1) البراند (إن وجد)
            $brand = null;
            if (!empty($row['brand'])) {
                $brandName = trim($row['brand']);
                $brand = Brand::where('name', $brandName)->first();
            }

            // 2) التصنيفات (إن وجدت)
            $categoryIds = [];
            if (!empty($row['categories'])) {
                $categoryNames = array_map('trim', explode(',', $row['categories']));
                foreach ($categoryNames as $catName) {
                    if ($catName === '') continue;
                    $category = Category::where('name', $catName)->first();
                    if ($category) {
                        $categoryIds[] = $category->id;
                    }
                }
            }

            // 3) تجهيز slug و SKU فريدين للبارنت
            $baseParentSlug = trim((string)($row['slug'] ?? ''));
            if ($baseParentSlug === '') {
                $namePart = $row['name'] ?? 'product';
                $skuPart  = $row['sku'] ?? Str::random(6);
                $baseParentSlug = Str::slug($namePart . '-' . $skuPart);
            }
            $parentSlug = $this->makeUniqueSlug($baseParentSlug);

            $baseParentSku = trim((string)($row['sku'] ?? ''));
            if ($baseParentSku === '') {
                // لو الـ SKU فاضي منولده من الـ slug
                $baseParentSku = strtoupper(str_replace('-', '', $parentSlug));
            }
            $parentSku = $this->makeUniqueSku($baseParentSku);

            // 4) إنشاء البارنت دائمًا ببيانات نظيفة (بدون Update لمنتجات قديمة)
            $parentProduct = Product::create([
                'slug'              => $parentSlug,
                'name'              => $row['name'] ?? 'No Name',
                'unit_cost'         => $row['unit_cost'] ?? 0,
                'short_description' => $row['short_description'] ?? '',
                'description'       => $row['description'] ?? '',
                'regular_price'     => $row['regular_price'] ?? 0,
                'sale_price'        => $row['sale_price'] ?? null,
                'SKU'               => $parentSku,
                'stock_status'      => $row['stock_status'] ?? 'instock',
                'featured'          => $row['featured'] ?? 0,
                'quantity'          => 0, // يُحدّث بعد إنشاء الأطفال
                'sizes'             => '',
                'store'             => $row['store'] ?? '',
                'parent'            => 1,
                'parent_id'         => null,
                'brand_id'          => $brand ? $brand->id : null,
            ]);

            // ربط التصنيفات للبارنت إن وجدت
            if (!empty($categoryIds)) {
                $parentProduct->categories()->sync($categoryIds);
            }

            $totalQuantity = 0;

            // 5) إنشاء الأولاد (Children) مع Slug/SKU فريدين
            if (!empty($row['sizes'])) {
                $sizes = array_map('trim', explode(',', $row['sizes']));

                $quantitiesRaw = $row['size_quantity'] ?? '';
                $quantities = is_array($quantitiesRaw)
                    ? $quantitiesRaw
                    : preg_split('/[,;|]+/', (string)$quantitiesRaw, -1, PREG_SPLIT_NO_EMPTY);


                $barcodesRaw = $row['size_barcodes'] ?? '';
                $barcodes = preg_split('/[,;|]+/', (string)$barcodesRaw, -1, PREG_SPLIT_NO_EMPTY);

                // اجلب أرقام الأولاد الموجودة مسبقًا لتكملة الترقيم بدون تصادم
                $usedNumbers = $this->fetchUsedChildNumbers($parentSlug);

                foreach ($sizes as $index => $size) {
                    if ($size === '') continue;

                    // اختر أول رقم متاح للأولاد (1,2,3,...) بدون تصادم
                    $n = $this->nextFreeNumber($usedNumbers);
                    $usedNumbers[$n] = true;

                    $childBaseSlug = $parentSlug . '-' . $n;
                    $childSlug = $this->makeUniqueSlug($childBaseSlug);

                    $childBaseSku = (($parentSku ?: 'SKU') . '-' . $n);
                    $childSku = $this->makeUniqueSku($childBaseSku);

                    $childQuantity = (int) ($quantities[$index] ?? ($row['quantity'] ?? 0));
                    $childBarcode  = $barcodes[$index] ?? null;

                    $childProduct = Product::create([
                        'slug'              => $childSlug,
                        'name'              => $row['name'] ?? 'No Name',
                        'short_description' => $row['short_description'] ?? '',
                        'description'       => $row['description'] ?? '',
                        'regular_price'     => $row['regular_price'] ?? 0,
                        'sale_price'        => $row['sale_price'] ?? null,
                        'SKU'               => $childSku,
                        'unit_cost'         => $row['unit_cost'] ?? 0,
                        'stock_status'      => $row['stock_status'] ?? 'instock',
                        'featured'          => $row['featured'] ?? 0,
                        'quantity'          => $childQuantity,
                        'sizes'             => $size,
                        'parent'            => 0,
                        'parent_id'         => $parentProduct->id,
                        'store'             => $row['store'] ?? '',
                        'brand_id'          => $brand ? $brand->id : null,
                        'barcode'           => $childBarcode ?? '',
                    ]);

                    $totalQuantity += $childQuantity;

                    // ربط التصنيفات للـ Child إن وجدت
                    if (!empty($categoryIds)) {
                        $childProduct->categories()->sync($categoryIds);
                    }
                }
            }

            // 6) تحديث كمية البارنت بعد جمع الأولاد
            $parentProduct->quantity = $totalQuantity;
            $parentProduct->save();

            return $parentProduct;
        });
    }

    /**
     * توليد Slug فريد (يضيف -1, -2, .. إذا لزم)
     */
    protected function makeUniqueSlug(string $base, ?int $ignoreId = null): string
    {
        $slug = Str::slug($base);
        if ($slug === '') {
            $slug = 'product';
        }
        $original = $slug;
        $i = 1;

        while (
            Product::where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $slug = $original . '-' . $i;
            $i++;
            if ($i > 1000) {
                throw new \RuntimeException('Slug generation exceeded 1000 attempts.');
            }
        }
        return $slug;
    }

    /**
     * توليد SKU فريد (لو فاضي يرجّع null؛ ولو موجود ومكرر يضيف -1, -2, ..)
     */
    protected function makeUniqueSku(?string $base, ?int $ignoreId = null): ?string
    {
        $sku = trim((string)$base);
        if ($sku === '') {
            return null; // ما نولّد SKU غصبًا إذا مش مطلوب
        }

        $original = $sku;
        $i = 1;

        while (
            Product::where('SKU', $sku)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()
        ) {
            $sku = $original . '-' . $i;
            $i++;
            if ($i > 1000) {
                throw new \RuntimeException('SKU generation exceeded 1000 attempts.');
            }
        }
        return $sku;
    }

    /**
     * رجّع أرقام الأولاد المستعملة مسبقًا بشكل parent-slug-{n}
     * مثال: لو في slugs: 4060-1, 4060-3 → رح يرجع [1 => true, 3 => true]
     */
    protected function fetchUsedChildNumbers(string $parentSlug): array
    {
        $existing = Product::where('slug', 'LIKE', $parentSlug . '-%')
            ->pluck('slug')
            ->all();

        $used = [];
        $pattern = '/^' . preg_quote($parentSlug, '/') . '\-(\d+)$/';

        foreach ($existing as $slug) {
            if (preg_match($pattern, $slug, $m)) {
                $num = (int) $m[1];
                if ($num > 0) {
                    $used[$num] = true;
                }
            }
        }
        return $used;
    }

    /**
     * يعطيك أصغر رقم متاح غير مستخدم للأولاد (1 ثم 2 ثم 3 ...)
     */
    protected function nextFreeNumber(array $used): int
    {
        $n = 1;
        while (isset($used[$n])) {
            $n++;
        }
        return $n;
    }
}
