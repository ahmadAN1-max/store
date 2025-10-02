<?php

namespace App\Http\Controllers;



use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Setting;
use App\Models\BillItem;
use App\Models\Category;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\BarcodesExport;
use App\Imports\BarcodesImport;
use App\Imports\ProductsImport;
use Illuminate\Support\FacadesDB;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\alert;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Support\Facades\Validator;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $admin = $user->utype === 'POSADM';

        $billItems = [];
        $bill = null; // <-- مهم

        if ($request->has('bill_id')) {
            $billId = $request->bill_id;
            $bill = Bill::with('billItems.product')->find($billId);
            if ($bill) {
                $billItems = $bill->billItems;
            }
        }

        $items = session()->get('pos_items', []);
        $discount = session()->get('pos_discount', 0);

        $maxDiscountSetting = DB::table('settings')->where('key', 'maxDiscount')->first();
        $maxDiscount = $maxDiscountSetting ? $maxDiscountSetting->value : 0;

        // جلب العملاء
        $customers = DB::table('customers')->get();
        // أو إذا عندك Model:
        // $customers = Customer::all();

        return view("pos.index", [
            'admin' => $admin,
            'billItems' => $billItems,
            'bill' => $bill,
            'items' => $items,
            'discount' => $discount,
            'maxDiscount' => $maxDiscount,
            'customers' => $customers,
        ]);
    }

    public function customers()
    {
        $customers = Customer::orderBy('id', 'DESC')->get();
        return view("pos.customers", compact("customers"));
    }
    public function add_customer()
    {
        return view("pos.customer-add");
    }

    public function add_customer_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->city = $request->city;
        $customer->address = $request->address;
        $customer->save();

        return redirect()->route('pos.customers')->with('status', 'Record has been added successfully!');
    }

    public function edit_customer($id)
    {
        $customer = Customer::findOrFail($id);
        return view('pos.customer-edit', compact('customer'));
    }

    public function update_customer(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:customers,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'city' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);


        $customer = Customer::findOrFail($request->id);
        $customer->name = $request->name;
        $customer->phone = $request->phone;
        $customer->city = $request->city;
        $customer->address = $request->address;
        $customer->save();

        return redirect()->route('pos.customers')->with('status', 'Record has been updated successfully!');
    }

    public function delete_customer($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('pos.customers')->with('status', 'Record has been deleted successfully!');
    }

    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->get();
        return view("pos.brands", compact('brands'));
    }

    public function add_brand()
    {
        return view("pos.brand-add");
    }

    public function add_brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $brand->main = $request->main;
        $brand->save();

        return redirect()->route('pos.brands')->with('status', 'Record has been added successfully!');
    }

    public function edit_brand($id)
    {
        $brand = Brand::findOrFail($id);
        return view('pos.brand-edit', compact('brand'));
    }

    public function update_brand(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
        ]);

        $brand = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->main = $request->main;



        $brand->save();

        return redirect()->route('pos.brands')->with('status', 'Record has been updated successfully!');
    }

    public function delete_brand($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->delete();

        return redirect()->route('pos.brands')->with('status', 'Record has been deleted successfully!');
    }

    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->get();
        return view("pos.categories", compact('categories'));
    }

    public function add_category()
    {
        return view("pos.category-add");
    }

    public function add_category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->main = $request->main ?? 0;

        $category->save();

        return redirect()->route('pos.categories')->with('status', 'Record has been added successfully!');
    }

    public function edit_category($id)
    {
        $category = Category::findOrFail($id);
        return view('pos.category-edit', compact('category'));
    }

    public function update_category(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
        ]);

        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->main = $request->main;

        $category->save();

        return redirect()->route('pos.categories')->with('status', 'Record has been updated successfully!');
    }

    public function delete_category($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('pos.categories')->with('status', 'Record has been deleted successfully!');
    }

    public function products()
    {
        $products = Product::where('parent', true)
            ->orderBy('created_at', 'DESC')
            ->get();

        $lastId = Product::max('id');


        return view("pos.products", compact('products', 'lastId'));
    }


    public function add_product()
    {
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        return view("pos.product-add", compact('categories', 'brands'));
    }

    public function product_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'unit_cost' => 'nullable',
            'category_id' => 'required|array',
            'category_id.*' => 'exists:categories,id',
            'brand_id' => 'required',
            'regular_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'quantity' => 'required',
            'size_barcodes' => 'nullable|string',

        ]);


        $current_timestamp = Carbon::now()->timestamp;
        $imageName = null;
        $gallery_arr = [];
        $gallery_images = "";


        $parentProduct = new Product();
        $parentProduct->name = $request->name;
        $parentProduct->slug = Str::slug($request->name);
        $parentProduct->unit_cost = $request->unit_cost;
        $parentProduct->short_description = $request->short_description ?? '';
        $parentProduct->description = $request->description ?? '';
        $parentProduct->regular_price = $request->regular_price;
        $parentProduct->sale_price = $request->sale_price;
        $parentProduct->SKU = $request->SKU;
        $parentProduct->stock_status = $request->stock_status;
        $parentProduct->featured = $request->featured;
        $parentProduct->quantity = $request->quantity;
        $parentProduct->sizes = '';
        $parentProduct->store = 'SP';
        $parentProduct->parent = true;
        $parentProduct->parent_id = null;


        $parentProduct->brand_id = $request->brand_id;
        $parentProduct->save();
        $parentProduct->categories()->sync($request->category_id);
        $sizeBarcodes = [];
        if ($request->size_barcodes) {
            $lines = explode("\n", $request->size_barcodes);
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    [$size, $barcode] = array_map('trim', explode(':', $line, 2));
                    $sizeBarcodes[$size] = $barcode;
                }
            }
        }

        if ($request->sizes) {
            $sizes = explode(',', $request->sizes);
            foreach ($sizes as $size) {
                $size = trim($size);
                if (!empty($size)) {
                    $childProduct = new Product();
                    $childProduct->name = $request->name;
                    $childProduct->slug = Str::slug($request->name) . '-' . strtolower($size);
                    $childProduct->short_description = $request->short_description ?? '';
                    $childProduct->description = $request->description ?? '';
                    $childProduct->regular_price = $request->regular_price;
                    $childProduct->sale_price = $request->sale_price;
                    $childProduct->SKU = $request->SKU . '-' . strtoupper($size);
                    $childProduct->unit_cost = $request->unit_cost;
                    $childProduct->stock_status = $request->stock_status;
                    $childProduct->featured = $request->featured;
                    $childProduct->quantity = $request->quantity;
                    $childProduct->sizes = $size;
                    $childProduct->parent = false;
                    $childProduct->store = 'SP';
                    $childProduct->parent_id = $parentProduct->id;
                    $childProduct->brand_id = $request->brand_id;
                    $childProduct->image = $parentProduct->image;
                    $childProduct->images = $parentProduct->images;

                    $childProduct->barcode = $sizeBarcodes[$size] ?? null;

                    $childProduct->save();
                    $childProduct->categories()->sync($request->category_id);
                }
            }
        }

        return redirect()->route('pos.products')->with('status', 'Product and variants added successfully!');
    }

    public function edit_product($id)
    {
        $product = Product::findOrFail($id);
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        $brands = Brand::select('id', 'name')->orderBy('name')->get();

        // تأكد من وجود علاقة children في موديل Product
        $parentProduct = Product::with('children')->where('id', $id)->where('parent', true)->firstOrFail();

        $items = $parentProduct->children;

        return view('pos.product-edit', compact('product', 'categories', 'brands', 'items'));
    }

  public function update_product(Request $request)
{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:products,slug,' . $request->id,
        'category_id' => 'required|array',
        'category_id.*' => 'exists:categories,id',
        'unit_cost' => 'nullable',
        'brand_id' => 'required',
        'regular_price' => 'required',
        'SKU' => 'required',
        'stock_status' => 'required',
        'quantity' => 'required',
    ]);

    $product = Product::findOrFail($request->id);

    if (!$product->parent) {
        abort(404, 'Parent product not found');
    }

    // معالجة باركود الأحجام
    $sizeBarcodes = [];
    if ($request->size_barcodes) {
        $lines = explode("\n", $request->size_barcodes);
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                [$size, $barcode] = array_map('trim', explode(':', $line, 2));
                $sizeBarcodes[$size] = $barcode;
            }
        }
    }

    if ($request->sizes) {
        $sizes = array_map('trim', explode(',', $request->sizes));
        $existingChildren = $product->children()->get()->keyBy('sizes');

        foreach ($sizes as $size) {
            if (isset($existingChildren[$size])) {
                // تحديث بيانات الطفل الموجود
                $child = $existingChildren[$size];
                $child->regular_price = $request->regular_price;
                $child->sale_price = $request->sale_price;

                if (isset($sizeBarcodes[$size])) {
                    $barcodeExists = Product::where('barcode', $sizeBarcodes[$size])->where('id', '!=', $child->id)->first();
                    $child->barcode = $barcodeExists ? $child->id : $sizeBarcodes[$size];
                }

                $child->save();
            } else {
                // إنشاء طفل جديد
                $childProduct = new Product();
                $childProduct->name = $request->name;
                $childProduct->slug = Str::slug($request->name) . '-' . strtolower($size);
                $childProduct->short_description = $request->short_description ?? '';
                $childProduct->description = $request->description ?? '';
                $childProduct->regular_price = $request->regular_price;
                $childProduct->sale_price = $request->sale_price;
                $childProduct->SKU = $request->SKU . '-' . strtoupper($size);
                $childProduct->unit_cost = $request->unit_cost;
                $childProduct->stock_status = $request->stock_status;
                $childProduct->featured = $request->featured;
                $childProduct->quantity = 0;
                $childProduct->sizes = $size;
                $childProduct->parent = false;
                $childProduct->store = 'SP';
                $childProduct->parent_id = $product->id;
                $childProduct->brand_id = $request->brand_id;
                $childProduct->image = $product->image;
                $childProduct->images = $product->images;

                if (isset($sizeBarcodes[$size])) {
                    $barcodeExists = Product::where('barcode', $sizeBarcodes[$size])->first();
                    $childProduct->barcode = $barcodeExists ? $childProduct->id : $sizeBarcodes[$size];
                }

                $childProduct->save();
                $childProduct->categories()->sync($request->category_id);
            }
        }

        // حذف الأطفال الذين لم يعودوا موجودين بشرط أن تكون الكمية صفر
        foreach ($existingChildren as $size => $child) {
            if (!in_array($size, $sizes) && $child->quantity === 0) {
                $child->categories()->detach();
                $child->delete();
            }
        }
    }

    if ($request->has('quantities')) {
        $totalQuantity = 0;

        foreach ($request->quantities as $childId => $quantity) {
            $child = Product::find($childId);
            if ($child && $child->parent_id == $product->id) {
                $child->quantity = $quantity;

                if ($request->has('barcodes') && isset($request->barcodes[$childId])) {
                    $barcodeExists = Product::where('barcode', $request->barcodes[$childId])->where('id', '!=', $child->id)->first();
                    $child->barcode = $barcodeExists ? $child->id : $request->barcodes[$childId];
                }

                $child->save();
                $totalQuantity += $quantity;
            }
        }

        // تحديث سعر البيع لجميع الأطفال
        if ($request->has('sale_price')) {
            foreach ($product->children as $child) {
                $child->sale_price = $request->sale_price;
                $child->save();
            }
        }

        $product->quantity = $totalQuantity;
    }

    // تحديث بيانات الأب
    $product->name = $request->name;
    $product->slug = Str::slug($request->name);
    $product->unit_cost = $request->unit_cost;
    $product->short_description = $request->short_description ?? '';
    $product->description = $request->description ?? '';
    $product->regular_price = $request->regular_price;
    $product->sale_price = $request->sale_price;
    $product->SKU = $request->SKU;
    $product->stock_status = $request->stock_status;
    $product->featured = 0;
    $product->categories()->sync($request->category_id);
    $product->brand_id = $request->brand_id;
    $product->save();

    return redirect()->route('pos.products')->with('status', 'Product and quantities updated successfully!');
}


    public function delete_product($id)
    {
        $product = Product::findOrFail($id);
        Product::where('parent_id', $product->id)->delete();
        $product->delete();

        return redirect()->route('pos.products')->with('status', 'Record has been deleted successfully!');
    }

    public function scan($barcode)
    {
        $parent = Product::whereHas('children', function ($query) use ($barcode) {
            $query->where('barcode', $barcode);
        })->with(['children' => function ($query) use ($barcode) {
            $query->where('barcode', $barcode);
        }])->first();

        if (!$parent || $parent->children->isEmpty()) {
            return response()->json(null, 404);
        }

        $child = $parent->children->first();

        $quantity = $child->quantity;
        if ($quantity > 0) {
            return response()->json([
                'product_id' => $parent->id,
                'name' => $parent->name,
                'child_id' => $child->id,
                'barcode' => $child->barcode,
                'size' => $child->sizes,
                'price' => $child->regular_price,
                'salePrice' => $child->sale_price ?? 0,
            ]);
        } else if ($quantity == 0) {
            return response()->json([
                'product_id' => $parent->id,
                'name' => $parent->name,
                'child_id' => $child->id,
                'barcode' => $child->barcode,
                'size' => $child->sizes,
                'price' => $child->regular_price * -1,
            ]);
        } {
            return response()->json([
                'message' => 'Out of stock'
            ], 404);;
        }
    }
    public function printBill($id)
    {
        $bill = Bill::with(['billItems.product', 'billItems.child'])->findOrFail($id);

        return view('pos.print', compact('bill'));
    }

    public function holdBill(Request $request)
    {
        DB::beginTransaction();
        try {
            // استخدام bill_number المرسل أو إنشاء جديد
            $billNumber = $request->input('bill_number') ?? 'BILL-' . time() . '-' . rand(1000, 9999);

            // جلب الفاتورة إذا موجودة
            $bill = Bill::where('bill_number', $billNumber)->first();

            if ($bill) {
                // تحديث بيانات الفاتورة الأساسية
                $bill->update([
                    'total_price' => $request->total_price,
                    'total_items' => collect($request->products)->sum(fn($p) => $p['quantity'] ?? 1),
                    'name' => $request->customerName,
                    'phone_number' => $request->phoneNumber,
                    'reference' => $request->reference,
                    'status' => $request->status ?? $bill->status,
                    'employee_name' => $request->employee,
                ]);

                // جلب العناصر الحالية بالفاتورة
                $existingItems = $bill->billItems()->get()->keyBy(fn($item) => $item->product_id . '-' . $item->child_id);
                $newProducts = collect($request->products)->keyBy(fn($p) => $p['product_id'] . '-' . $p['child_id']);

                // حذف المنتجات المحذوفة
                $removedKeys = $existingItems->keys()->diff($newProducts->keys());

                // جمع IDs للـ child و parent من العناصر الحالية بالفاتورة
                $childIdsInBill = $existingItems->pluck('child_id')->filter()->unique();
                $productIdsInBill = $existingItems->pluck('product_id')->filter()->unique();

                // جلب كل الـ child و parent products من قاعدة البيانات
                $childProducts = Product::whereIn('id', $childIdsInBill)->get()->keyBy('id');
                $parentProducts = Product::whereIn('id', $productIdsInBill)->get()->keyBy('id');

                // معالجة العناصر المحذوفة
                foreach ($removedKeys as $key) {
                    $item = $existingItems->get($key);
                    if ($item) {
                        $qty = $item->quantity ?? 1;
                        $child = $childProducts->get($item->child_id);
                        $parent = $parentProducts->get($item->product_id);

                        // زيادة كمية الـ child و parent عند حذف العنصر
                        if ($child) {
                            $child->quantity += $qty;
                            $child->save();
                        }
                        if ($parent) {
                            $parent->quantity += $qty;
                            $parent->save();
                        }

                        $item->delete();
                    }
                }

                //            foreach ($newProducts as $key => $prod) {
                //     $existingItem = $existingItems->get($key);
                //     $qty = $prod['quantity'] ?? 1;

                // $child = $childProducts->get($prod['child_id']);
                // $parent = $parentProducts->get($prod['product_id']);


                //     if ($existingItem) {
                //         $oldQty = $existingItem->quantity ?? 1;
                //         $diff = $qty - $oldQty;

                //         // تحديث الفاتورة
                //         $existingItem->update([
                //             'price' => $prod['price'],
                //             'quantity' => $qty,
                //         ]);

                //         // تعديل كمية الـ child حسب الفرق
                //         if ($child && $diff != 0) {
                //             $child->quantity = max(0, $child->quantity - $diff);
                //             $child->save();
                //         }

                //         // تعديل كمية الـ parent حسب الفرق
                //         if ($parent && $diff != 0) {
                //             $parent->quantity = max(0, $parent->quantity - $diff);
                //             $parent->save();
                //         }
                //     } else {
                //         // عنصر جديد بالفاتورة
                //         $bill->billItems()->create([
                //             'product_id' => $prod['product_id'],
                //             'child_id' => $prod['child_id'] ?? null,
                //             'quantity' => $qty,
                //             'price' => $prod['price'],
                //         ]);

                //         // خصم الكمية من الـ child و parent
                //         if ($child) {
                //             $child->quantity = max(0, $child->quantity - $qty);
                //             $child->save();
                //         }
                //         if ($parent) {
                //             $parent->quantity = max(0, $parent->quantity - $qty);
                //             $parent->save();
                //         }
                //     }
                // }

            } else {
                // إنشاء فاتورة جديدة
                $bill = Bill::create([
                    'bill_number' => $billNumber,
                    'name' => $request->name,
                    'phone_number' => $request->phone_number,
                    'reference' => $request->reference,
                    'status' => $request->status ?? 'unpaid',
                    'total_price' => $request->total_price,
                    'payment_method' => 'cash',
                    'employee_name' => $request->employee,
                    'total_items' => collect($request->products)->sum(fn($p) => $p['quantity'] ?? 1),
                    'user_id' => Auth::id() ?? 5,
                ]);

                $productIds = collect($request->products)->pluck('product_id');
                $childIds = collect($request->products)->pluck('child_id');
                $childProducts = Product::whereIn('id', $childIds)->get()->keyBy('id');
                $parentProducts = Product::whereIn('id', $productIds)->get()->keyBy('id');

                foreach ($request->products as $prod) {
                    $qty = $prod['quantity'] ?? 1;

                    $bill->billItems()->create([
                        'product_id' => $prod['product_id'],
                        'child_id' => $prod['child_id'] ?? null,
                        'price' => $prod['price'],
                        'quantity' => $qty,
                    ]);

                    $child = $childProducts[$prod['child_id']] ?? null;
                    $parent = $parentProducts[$prod['product_id']] ?? null;

                    if ($child) {
                        $child->quantity = max(0, $child->quantity - $qty);
                        $child->save();
                    }
                    if ($parent) {
                        $parent->quantity = max(0, $parent->quantity - $qty);
                        $parent->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'bill' => $bill->load('billItems'),
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function checkout(Request $request)
    {
        DB::beginTransaction();
        try {

            // إنشاء الفاتورة
            $bill = Bill::create([
                'bill_number' => 'BILL-' . time() . '-' . rand(1000, 9999),
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'reference' => 'CASH',
                'status' => $request->status,
                'employee_name' => $request->employee,
                'total_price' => $request->total_price,
                'payment_method' => 'cash',
                'total_items' => collect($request->products)->sum(function ($prod) {
                    return $prod['quantity'] ?? 1;
                }),
                'user_id' => Auth::id() ?? 5,
            ]);

            foreach ($request->products as $prod) {

                // تحديد السعر النهائي: salePrice إذا موجود > 0، وإلا regular_price
                $finalPrice =  $prod['price'];

                // حفظ عناصر الفاتورة
                BillItem::create([
                    'bill_id'    => $bill->id,
                    'product_id' => $prod['product_id'],
                    'child_id'   => $prod['child_id'],
                    'price'      => $finalPrice,
                    'quantity'   => $prod['quantity'] ?? 1,
                ]);

                $quantityToDeduct = $prod['quantity'] ?? 1;

                // تحديث كمية الطفل
                $child = Product::find($prod['child_id']);
                if ($child) {
                    $child->quantity = max(0, $child->quantity - $quantityToDeduct);
                    $child->save();
                }

                // تحديث كمية المنتج الأب
                $parent = Product::find($prod['product_id']);
                if ($parent) {
                    $parent->quantity = max(0, $parent->quantity - $quantityToDeduct);
                    $parent->save();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'bill' => $bill,
                'bill_id' => $bill->id,
                'products' => $request->products,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function saveBill(Request $request)
    {
        DB::beginTransaction();
        try {
            // نحاول العثور على الفاتورة بحسب bill_number المرسل
            $billNumber = $request->input('bill_number') ?? $request->input('products.0.bill_number');
            $bill = Bill::where('bill_number', $billNumber)->first();
            // إذا لم توجد الفاتورة، نرجع خطأ مع rollback
            if (!$bill) {
                DB::rollBack();
                return response()->json(['error' => 'Bill not found.'], 404);
            }

            // تحديث بيانات الفاتورة الأساسية
            $bill->update([
                'total_price' => $request->total_price,
                'total_items' => collect($request->products)->sum(fn($prod) => $prod['quantity'] ?? 1),
            ]);

            // المنتجات الحالية في الفاتورة
            $existingItems = $bill->billItems()->get()->keyBy(fn($item) => $item->product_id . '-' . $item->child_id);

            // المنتجات الجديدة من الريكوست
            $newProducts = collect($request->products)->keyBy(fn($prod) => $prod['product_id'] . '-' . $prod['child_id']);

            // المنتجات المحذوفة
            $removedKeys = $existingItems->keys()->diff($newProducts->keys());

            foreach ($removedKeys as $key) {
                $item = $existingItems->get($key);
                if ($item) {
                    $quantityToRestore = $item->quantity ?? 1;

                    // استرجاع الكمية عند حذف أو تقليل المنتج
                    $parent = Product::find($item->product_id);

                    if ($parent) {
                        // استرجاع للطفل إذا موجود
                        if ($item->child_id) {
                            $child = $parent->children()->find($item->child_id);
                            if ($child) {
                                $child->quantity += $quantityToRestore; // زيادة كمية الطفل
                                $child->save();
                            }
                        }

                        // استرجاع للبارنت
                        $parent->quantity += $quantityToRestore;
                        $parent->save();
                    }

                    // حذف العنصر
                    $item->delete();
                }
            }

            // تحديث أو إنشاء المنتجات الجديدة/المعدلة
            foreach ($newProducts as $key => $prod) {
                $existingItem = $existingItems->get($key);
                $newQuantity = $prod['quantity'] ?? 1;

                if ($existingItem) {
                    $oldQuantity = $existingItem->quantity ?? 1;
                    $quantityDiff = $newQuantity - $oldQuantity;

                    $existingItem->update([
                        'price' => $prod['price'],
                        'quantity' => $newQuantity,
                    ]);

                    if ($quantityDiff != 0) {
                        if (!empty($prod['child_id'])) {
                            $child = Product::find($prod['child_id']);
                            if ($child) {
                                $child->quantity = max(0, $child->quantity - $quantityDiff);
                                $child->save();
                            }
                        }

                        $parent = Product::find($prod['product_id']);
                        if ($parent) {
                            $parent->quantity = max(0, $parent->quantity - $quantityDiff);
                            $parent->save();
                        }
                    }
                } else {
                    BillItem::create([
                        'bill_id' => $bill->id,
                        'product_id' => $prod['product_id'],
                        'child_id' => $prod['child_id'],
                        'price' => $prod['price'],
                        'quantity' => $newQuantity,
                    ]);

                    if (!empty($prod['child_id'])) {
                        $child = Product::find($prod['child_id']);
                        if ($child) {
                            $child->quantity = max(0, $child->quantity - $newQuantity);
                            $child->save();
                        }
                    }

                    $parent = Product::find($prod['product_id']);
                    if ($parent) {
                        $parent->quantity = max(0, $parent->quantity - $newQuantity);
                        $parent->save();
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'bill' => $bill,
                'products' => $request->products,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function checkoutSaved(Request $request)
    {
        DB::beginTransaction();
        try {
            $billNumber = $request->input('bill_number') ?? $request->input('products.0.bill_number');

            $bill = Bill::where('bill_number', $billNumber)->first();

            if (!$bill) {
                return response()->json(['error' => 'Bill not found'], 404);
            }
            if ($bill->status == 'unpaid')
                // تحديث حالة الفاتورة إلى paid
                $bill->update([
                    'status' => 'paid',

                    // يمكنك تحديث بيانات أخرى إذا لزم الأمر
                ]);
            if ($bill->status == 'unpaidWebsite')
                // تحديث حالة الفاتورة إلى paid
                $bill->update([
                    'status' => 'paidWebsite',

                    // يمكنك تحديث بيانات أخرى إذا لزم الأمر
                ]);
            // هنا يمكنك تحديث أو معالجة العناصر إذا لزم الأمر

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bill checked out successfully',
                'bill' => $bill
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function bills()
    {
        $bills = Bill::whereIn('status', ['unpaid', 'unpaidWebsite'])->get();


        return view("pos.bills", compact("bills"));
    }

    public function paidBills()
    {
        $bills = Bill::whereIn('status', ['paid', 'paidWebsite'])->get();


        return view("pos.paidBills", compact("bills"));
    }

    public function delete_bill($id)
    {
        $bill = Bill::find($id);
        $bill->delete();
        return redirect()->route('pos.bills')->with('status', 'Record has been deleted successfully !');
    }

    public function reports()
    {
        $bills = null;
        $categories = Category::all();
        return view("pos.reports", compact('bills', 'categories'));
    }

   public function generateReport(Request $request)
{
    $request->validate([
        'date_from' => 'required|date',
        'date_to' => 'required|date|after_or_equal:date_from',
        'report_type' => 'required|in:pos,web,both',
        'store' => 'required|string',
        'category_id' => 'nullable|exists:categories,id',
    ]);

    $from = $request->date_from;
    $to = $request->date_to;
    $type = $request->report_type;
    $store = $request->store;
    $categoryId = $request->category_id;

    $query = Bill::with(['billItems.product.categories'])
        ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);

    // فلتر حسب نوع التقرير
    if ($type === 'pos') {
        $query->whereIn('status', ['paid']);
    } elseif ($type === 'web') {
        $query->whereIn('status', ['paidWebsite']);
    } else { // both
        $query->whereIn('status', ['paid', 'paidWebsite']);
    }

    // فلتر حسب المتجر
    if ($store !== 'both') {
        $query->whereHas('billItems.product', function ($q) use ($store) {
            $q->where('store', $store);
        });
    }

    // فلتر حسب الكاتيجوري
    if ($categoryId) {
        $query->whereHas('billItems.product.categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    $bills = $query->get();
    $categories = Category::all();

    return view('pos.reports', compact('bills', 'from', 'to', 'type', 'store', 'categories', 'categoryId'));
}


    public function return()
    {
        return view("pos.return");
    }

    public function returnBill(Request $request)
    {
        DB::beginTransaction();
        try {
            // أولًا ننشئ الـ bill بدون total_price نهائي
            $bill = Bill::create([
                'bill_number' => 'BILL-' . time() . '-' . rand(1000, 9999),
                'name' => $request->name ?? 'customer',
                'phone_number' => $request->phone_number ?? 'No Phone number',
                'reference' => $request->reference ?? 'ref #',
                'status' => $request->status, // خلي status كما هو
                'total_price' => 0, // رح نحسبه بعدين
                'payment_method' => 'cash',
                'employee_name' => $request->employee,
                'total_items' => collect($request->products)->sum(fn($prod) => $prod['quantity'] ?? 1),
                'user_id' => Auth::id() ?? 5,
            ]);

            $billTotal = 0; // لتجميع السعر الكلي بعد الخصم
            foreach ($request->products as $prod) {
                $quantityToAdd = $prod['quantity'] ?? 1;
                $price = $prod['price'] ?? 0;

                // نجعل السعر بالسالب للمرتجع
                $itemPrice = -1 * $price;

                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_id' => $prod['product_id'],
                    'child_id' => $prod['child_id'],
                    'price' => $itemPrice,
                    'quantity' => $quantityToAdd,
                    'is_return' => 1,
                ]);

                // تحديث المخزون
                $child = Product::find($prod['child_id']);
                $parent = Product::find($prod['product_id']);

                if ($child) {
                    $child->quantity += $quantityToAdd;
                    $child->save();
                }

                if ($parent) {
                    $parent->quantity += $quantityToAdd;
                    $parent->save();
                }

                $billTotal += $itemPrice * $quantityToAdd;
            }

            // نحدث total_price بعد حساب كل المنتجات
            $bill->total_price = $billTotal;
            $bill->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'bill' => $bill,
                'bill_id' => $bill->id,
                'products' => $request->products,
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function settings()
    {
        $maxDiscountSetting = DB::table('settings')->where('key', 'maxDiscount')->first();
        $maxDiscount = $maxDiscountSetting ? $maxDiscountSetting->value : null;
        return view("pos.settings", compact("maxDiscount"));
    }



    public function setMaxDiscount(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'maxDiscount'],
            ['value' => $request->maxDiscount]
        );

        return redirect()->back()->with('success', 'Max Discount updated successfully.');
    }


    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        Excel::import(new ProductsImport, $request->file('file'));

        return redirect()->back()->with('success', 'تم استيراد المنتجات بنجاح!');
    }

    public function exportBarcodes()
    {
        return Excel::download(new BarcodesExport, 'barcodes.xlsx');
    }

    public function importBarcodes(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls'
        ]);

        Excel::import(new BarcodesImport, $request->file('file'));

        return back()->with('success', 'Barcodes imported successfully!');
    }
}
