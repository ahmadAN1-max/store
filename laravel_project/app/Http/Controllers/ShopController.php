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
            ->where('featured', 1);       // الصورة مش فاضية

        // فلترة حسب الكاتيجوري
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

     // فلترة حسب البراند
    if ($request->filled('brand')) {
        $brandParam = $request->brand;

        $productsQuery->whereHas('brand', function ($q) use ($brandParam) {
            if (is_numeric($brandParam)) {
                $q->where('id', $brandParam);
            } else {
                $q->where('name', 'like', "%{$brandParam}%");
            }
        });
    }

        // فلترة حسب الـ Exclusive
        if ($request->boolean('exclusive')) {
            $productsQuery->whereHas('categories', function ($q) {
                $q->where('name', 'Exclusive Website')
                  ->orWhere('name', 'like', 'Exclusive %');
            });
        }

        // Pagination مع الاحتفاظ بكل الـ request parameters
        $products = $productsQuery
            ->orderBy('created_at', 'desc')
            ->get();

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

        // فلترة Exclusive
        if ($request->boolean('exclusive')) {
            $productsQuery->whereHas('categories', function ($q) {
                $q->where('name', 'Exclusive Website')
                  ->orWhere('name', 'like', 'Exclusive %');
            });
        }

        $category = Category::where('slug', $slug)->firstOrFail();

        $products = $productsQuery
            ->get();

        return view('shop', compact('products', 'category'));
    }

    public function search(Request $request)
    {
        $keyword = $request->input('search-keyword');

        $products = Product::where('parent', 1)
            ->where('featured', 1)
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->where(function($query) use ($keyword) {
                $query->where('name', 'LIKE', "%{$keyword}%")
                      ->orWhere('short_description', 'LIKE', "%{$keyword}%")
                      ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->get();

        return view('shop', compact('products'));
    }
}
