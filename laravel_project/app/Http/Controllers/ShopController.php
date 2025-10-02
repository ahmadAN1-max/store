<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $productsQuery = Product::where('parent', true)
    ->whereNotNull('image')       // الصورة موجودة
    ->where('image', '!=', '')
    ->where('featured', 1);   // الصورة مش فاضية


        if ($request->filled('category')) {
            $categoryParam = $request->category;

            $productsQuery->whereHas('categories', function ($q) use ($categoryParam) {
                if (is_numeric($categoryParam)) {
                    $q->where('id', $categoryParam);
                } else {
                    $q->where('name', $categoryParam);
                }
            });
        }
        if ($request->filled('brand')) {
            $brandParam = $request->brand;

            $productsQuery->whereHas('brands', function ($q) use ($brandParam) {
                if (is_numeric($brandParam)) {
                    $q->where('id', $brandParam);
                } else {
                    $q->where('name', $brandParam);
                }
            });
        }

        // إذا كان فيه طلب exclusive → نفس الشي فلترة كاتيغوري
        if ($request->boolean('exclusive')) {
            $productsQuery->whereHas('categories', function ($q) {
                $q->where('name', 'Exclusive Website')
                    ->orWhere('name', 'like', 'Exclusive %');
                // هيك بيجيب كل الـ sub categories تحت الـ Exclusive
            });
        }

        $products = $productsQuery
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('shop', compact('products'));
    }





    public function product_details($product_slug)
    {
        $product = Product::with('categories')->where("slug", $product_slug)->first();

        if (!$product) {
            abort(404, 'product not available');
        }

        $sizes = $product->children()->orderBy('sizes')->get();

        $rproducts = Product::where("slug", "<>", $product_slug)->take(8)->get();

        return view('details', compact("product", "rproducts", "sizes"));
    }

    public function categoryProducts($slug, Request $request)
{
    $productsQuery = Product::where('parent', true)
        ->whereNotNull('image')
        ->where('image', '!=', '')
        ->where('featured', 1)
        ->whereHas('categories', function ($q) use ($slug) {
            $q->where('slug', $slug);
        });

    if ($request->boolean('exclusive')) {
        $productsQuery->whereHas('categories', function ($q) {
            $q->where('name', 'Exclusive Website')
              ->orWhere('name', 'like', 'Exclusive %');
        });
    }

    // لو حابب تعرض اسم الكاتيجوري في الصفحة
    $category = \App\Models\Category::where('slug', $slug)->firstOrFail();

    $products = $productsQuery->paginate(12);

    return view('shop', compact('products', 'category'));
}

    public function search(Request $request)
    {
    $keyword = $request->input('search-keyword');

    $products = Product::where('parent', 1)
    ->where('featured', 1)
    ->whereNotNull('image')        // الصورة موجودة
    ->where('image', '!=', '')     // الصورة مش فاضية
    ->where(function($query) use ($keyword) {
    $query->where('name', 'LIKE', "%{$keyword}%")
        ->orWhere('short_description', 'LIKE', "%{$keyword}%")
        ->orWhere('description', 'LIKE', "%{$keyword}%");
    })
    ->take(10)
    ->get();
    // إذا الصفحة عادية
    return view('shop', compact('products'));
}

}
