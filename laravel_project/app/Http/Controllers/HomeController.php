<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function index()
    {
        $categories = Category::where('main', true)
            ->whereNotNull('image')
            ->orderBy('id', 'DESC')
            ->get();

        $brands = Brand::where('main', true)
            ->whereNotNull('image')
            ->orderBy('id', 'DESC')
            ->get();

        $saleProducts = Product::whereHas('categories', function ($query) {
            $query->where('name', 'sale');
        })->get();

        $featuredProducts = Product::where('featured', '1')->get();

        return view('index', compact('categories', 'brands', 'saleProducts', 'featuredProducts'));
    }
}
