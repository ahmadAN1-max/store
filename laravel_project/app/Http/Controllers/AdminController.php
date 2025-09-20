<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Bill;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Setting;
use App\Models\BillItem;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class AdminController extends Controller
{
    public function index()
    {
        $orders = Order::orderBy('created_at', 'DESC')->take(10)->get();

        $dashboardDatas = DB::select("SELECT 
        SUM(total) AS TotalAmount,
        SUM(IF(status='ordered', total, 0)) AS TotalOrderedAmount,
        SUM(IF(status='delivered', total, 0)) AS TotalDeliveredAmount,
        SUM(IF(status='canceled', total, 0)) AS TotalCanceledAmount,
        COUNT(*) AS Total,
        SUM(IF(status='ordered', 1, 0)) AS TotalOrdered,
        SUM(IF(status='delivered', 1, 0)) AS TotalDelivered,
        SUM(IF(status='canceled', 1, 0)) AS TotalCanceled
        FROM orders
    ");

        $orderStats = Order::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw("COUNT(*) as total_orders")
        )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();


        $deliveryChargeSetting = DB::table('settings')->where('key', 'delivery_charge')->first();
        $deliveryCharge = $deliveryChargeSetting ? $deliveryChargeSetting->value : null;

        return view("admin.index", compact('orders', 'dashboardDatas', 'orderStats', 'deliveryCharge'));
    }

    public function updateDeliveryCharge(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'delivery_charge'],
            ['value' => $request->delivery_charge]
        );

        return redirect()->back()->with('success', 'Delivery charge updated successfully.');
    }
    // ======================= Brands =======================

    public function brands()
    {
        $brands = Brand::orderBy('id', 'DESC')->get();
        return view("admin.brands", compact('brands'));
    }

    public function add_brand()
    {
        return view("admin.brands.add");
    }

    public function add_brand_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = Str::slug($request->name);
        $brand->main = $request->has('main');
        $brand->store = 'SP';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumbailImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Record has been added successfully!');
    }

    public function GenerateBrandThumbailImage($image, $imageName)
    {
        $destinationPath = '/home/customer/www/adamn85.sg-host.com/public_html/uploads/brands/thumbnails';
        $img = Image::make($image->path());
        $img->fit(124, 124, function ($constraint) {
            $constraint->upsize();
        }, 'top')->save($destinationPath . '/' . $imageName);
    }

    public function edit_brand($id)
    {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit', compact('brand'));
    }

    public function update_brand(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg'
        ]);

        $brand = Brand::findOrFail($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        $brand->main = $request->has('main');
        $brand->store = 'SP';
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/brands/thumbnails/' . $brand->image))) {
                File::delete(public_path('uploads/brands/thumbnails/' . $brand->image));
            }
            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumbailImage($image, $file_name);
            $brand->image = $file_name;
        }

        $brand->save();

        return redirect()->route('admin.brands')->with('status', 'Record has been updated successfully!');
    }

    public function delete_brand($id)
    {
        $brand = Brand::findOrFail($id);
        if (File::exists(public_path('uploads/brands/thumbnails/' . $brand->image))) {
            File::delete(public_path('uploads/brands/thumbnails/' . $brand->image));
        }
        $brand->delete();

        return redirect()->route('admin.brands')->with('status', 'Record has been deleted successfully!');
    }

    // ======================= Categories =======================

    public function categories()
    {
        $categories = Category::orderBy('id', 'DESC')->get();
        return view("admin.categories", compact('categories'));
    }

    public function add_category()
    {
        return view("admin.category.add");
    }


    public function GenerateCategoryThumbailImage($image, $imageName)
    {
        $destinationPath = '/home/customer/www/adamn85.sg-host.com/public_html/uploads/categories/thumbnails';  
        $img = Image::make($image->path());
        $img->resize(124, 124, function ($constraint) {
        $constraint->aspectRatio(); // يحافظ على نسبة العرض/الارتفاع الأصلية
        $constraint->upsize();      // يمنع تكبير الصورة لو كانت أصغر
        })->save($destinationPath . '/' . $imageName);
    }


    public function add_category_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->main = $request->has('main');
        $category->free_delivery = $request->has('freeDelivery');
        $category->store = 'SP';
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbailImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Record has been added successfully!');
    }

    public function edit_category($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.category.edit', compact('category'));
    }

    public function update_category(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,' . $request->id,
            'image' => 'nullable|mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::findOrFail($request->id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        $category->main = $request->has('main');
        $category->free_delivery = $request->has('freeDelivery'); // true إذا محدد، false إذا مش محدد

        $category->store = 'SP';
        if ($request->hasFile('image')) {
            if (File::exists(public_path('uploads/categories/thumbnails/' . $category->image))) {
                File::delete(public_path('uploads/categories/thumbnails/' . $category->image));
            }
            $image = $request->file('image');
            $file_extention = $image->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumbailImage($image, $file_name);
            $category->image = $file_name;
        }

        $category->save();

        return redirect()->route('admin.categories')->with('status', 'Record has been updated successfully!');
    }

    public function delete_category($id)
    {
        $category = Category::findOrFail($id);
        if (File::exists(public_path('uploads/categories/thumbnails/' . $category->image))) {
            File::delete(public_path('uploads/categories/thumbnails/' . $category->image));
        }
        $category->delete();

        return redirect()->route('admin.categories')->with('status', 'Record has been deleted successfully!');
    }

    // ======================= Products =======================
public function GenerateThumbnailImage($imageFile, $imageName)
{
    $destinationPath = '/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/thumbnails';

    $img = Image::make($imageFile);
    $img->fit(124, 124, function ($constraint) {
        $constraint->upsize();
    }, 'top')->save($destinationPath . '/' . $imageName);
}

public function SaveOriginalGalleryImage($image, $imageName)
{
    $destinationPath = '/home/customer/www/adamn85.sg-host.com/public_html/uploads/products';
    $img = Image::make($image->path());
    $img->save($destinationPath . '/' . $imageName);
}

public function SaveOriginalProductImage($imageFile, $imageName)
{
    $destinationPath = '/home/customer/www/adamn85.sg-host.com/public_html/uploads/products';
    $imageFile->move($destinationPath, $imageName);
}

public function GenerateThumbnailImageFromPath($imagePath, $imageName)
{
    $destinationPath = '/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/thumbnails';
    $img = Image::make($imagePath);
    $img->fit(124, 124, function ($constraint) {
        $constraint->upsize();
    }, 'top')->save($destinationPath . '/' . $imageName);
}

// =================== LIST PRODUCTS ===================
public function products()
{
    $products = Product::where('parent', true)
        ->orderBy('created_at', 'DESC')
        ->get();

    return view("admin.products", compact('products'));
}

// =================== ADD PRODUCT ===================
public function add_product()
{
    $categories = Category::select('id', 'name')->orderBy('name')->get();
    $brands = Brand::select('id', 'name')->orderBy('name')->get();

    return view("admin.product.add", compact('categories', 'brands'));
}

// =================== STORE PRODUCT ===================
public function product_store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:products,slug',
        'category_id' => 'required|array',
        'category_id.*' => 'exists:categories,id',
        'brand_id' => 'required',
        'regular_price' => 'required',
        'SKU' => 'required',
        'stock_status' => 'required',
        'featured' => 'required',
        'quantity' => 'required',
        'size_barcodes' => 'nullable|string',
        'image' => 'nullable|mimes:png,jpg,jpeg'
    ]);

    $current_timestamp = Carbon::now()->timestamp;
    $imageName = null;
    $gallery_arr = [];
    $gallery_images = "";

    if ($request->hasFile('images')) {
        $allowedfileExtension = ['jpg', 'png', 'jpeg'];
        $files = $request->file('images');
        $gallery_arr = [];
        $counter = 1;

        foreach ($files as $file) {
            $gextension = $file->getClientOriginalExtension();
            if (in_array($gextension, $allowedfileExtension)) {
                $gfilename = $current_timestamp . "-" . $counter . "." . $gextension;
                $this->SaveOriginalProductImage($file, $gfilename);
                $fullImagePath = '/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/' . $gfilename;
                $this->GenerateThumbnailImageFromPath($fullImagePath, $gfilename);
                $gallery_arr[] = $gfilename;
                $counter++;
            }
        }

        $gallery_images = implode(',', $gallery_arr);
    }

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = $current_timestamp . '.' . $image->extension();
        $this->SaveOriginalProductImage($image, $imageName);
        $fullImagePath = '/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/' . $imageName;
        $this->GenerateThumbnailImageFromPath($fullImagePath, $imageName);
    }

    $parentProduct = new Product();
    $parentProduct->name = $request->name;
    $parentProduct->slug = Str::slug($request->name);
    $parentProduct->short_description = $request->short_description ?? ' ';
    $parentProduct->description = $request->description ?? ' ';
    $parentProduct->regular_price = $request->regular_price;
    $parentProduct->sale_price = $request->sale_price;
    $parentProduct->SKU = $request->SKU;
    $parentProduct->stock_status = $request->stock_status;
    $parentProduct->featured = $request->featured;
    $parentProduct->quantity = $request->quantity;
    $parentProduct->sizes = '';
    $parentProduct->parent = true;
    $parentProduct->parent_id = null;
    $parentProduct->brand_id = $request->brand_id;
    $parentProduct->image = $imageName;
    $parentProduct->images = $gallery_images;
    $parentProduct->store = 'SP';
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
                $childProduct->short_description = $request->short_description ?? ' ';
                $childProduct->description = $request->description ?? ' ';
                $childProduct->regular_price = $request->regular_price;
                $childProduct->sale_price = $request->sale_price;
                $childProduct->SKU = $request->SKU . '-' . strtoupper($size);
                $childProduct->stock_status = $request->stock_status;
                $childProduct->featured = $request->featured;
                $childProduct->quantity = $request->quantity;
                $childProduct->sizes = $size;
                $childProduct->parent = false;
                $childProduct->parent_id = $parentProduct->id;
                $childProduct->brand_id = $request->brand_id;
                $childProduct->image = $parentProduct->image;
                $childProduct->images = $parentProduct->images;
                $childProduct->store = 'SP';
                $childProduct->barcode = $sizeBarcodes[$size] ?? null;

                $childProduct->save();
                $childProduct->categories()->sync($request->category_id);
            }
        }
    }

    return redirect()->route('admin.products')->with('status', 'Product and variants added successfully!');
}

// =================== EDIT PRODUCT ===================
public function edit_product($id)
{
    $product = Product::findOrFail($id);
    $categories = Category::select('id', 'name')->orderBy('name')->get();
    $brands = Brand::select('id', 'name')->orderBy('name')->get();
    $parentProduct = Product::with('children')->where('id', $id)->where('parent', true)->firstOrFail();
    $items = $parentProduct->children;

    return view('admin.product.edit', compact('product', 'categories', 'brands', 'items'));
}

// =================== UPDATE PRODUCT ===================
public function update_product(Request $request)
{
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:products,slug,' . $request->id,
        'category_id' => 'required|array',
        'category_id.*' => 'exists:categories,id',
        'brand_id' => 'required',
        'regular_price' => 'required',
        'SKU' => 'required',
        'stock_status' => 'required',
        'featured' => 'required',
        'image' => 'nullable|mimes:png,jpg,jpeg',
        'images.*' => 'nullable|mimes:png,jpg,jpeg'
    ]);

    $product = Product::findOrFail($request->id);

    if (!$product->parent) {
        abort(404, 'Parent product not found');
    }

    // تحديث البيانات الأساسية + children كما عندك
    // === تعديل المسارات للصور ===
    if ($request->hasFile('image')) {
        if ($product->image) {
            if (File::exists('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/thumbnails/' . $product->image)) {
                File::delete('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/thumbnails/' . $product->image);
            }
            if (File::exists('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/' . $product->image)) {
                File::delete('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/' . $product->image);
            }
        }

        $image = $request->file('image');
        $imageName = Carbon::now()->timestamp . '.' . $image->extension();
        $image->move('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/', $imageName);
        $this->GenerateThumbnailImageFromPath('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/' . $imageName, $imageName);

        $product->image = $imageName;
    }

    if ($request->hasFile('images')) {
        $newImages = [];
        foreach ($request->file('images') as $file) {
            $imageName = Carbon::now()->timestamp . rand(1000, 9999) . '.' . $file->extension();
            $file->move('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/', $imageName);
            $this->GenerateThumbnailImageFromPath('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/' . $imageName, $imageName);
            $newImages[] = $imageName;
        }
        $allImages = $product->images ? explode(',', $product->images) : [];
        $product->images = implode(',', array_merge($allImages, $newImages));
    }

    $product->save();
    return redirect()->route('admin.products')->with('status', 'Product and quantities updated successfully!');
}

// =================== DELETE PRODUCT ===================
public function delete_product($id)
{
    $product = Product::findOrFail($id);
    Product::where('parent_id', $product->id)->delete();

    if (File::exists('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/thumbnails/' . $product->image)) {
        File::delete('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/thumbnails/' . $product->image);
    }
    if (File::exists('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/' . $product->image)) {
        File::delete('/home/customer/www/adamn85.sg-host.com/public_html/uploads/products/' . $product->image);
    }

    $product->delete();
    return redirect()->route('admin.products')->with('status', 'Record has been deleted successfully!');
}


    // ======================= Orders =======================

    public function orders()
    {
        $orders = Order::orderBy('created_at', 'DESC')->get();
        return view("admin.orders", compact('orders'));
    }

    public function order_items($order_id)
    {
        $order = Order::findOrFail($order_id);
        $orderitems = OrderItem::where('order_id', $order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id', $order_id)->first();

        if (!$transaction) {
            $transaction = (object) ['order' => $order, 'mode' => '-', 'status' => '-'];
        }

        return view("admin.order-details", compact('order', 'orderitems', 'transaction'));
    }

    public function update_order_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->status = $request->order_status;
        if ($order->status == 'delivered') {
            $order->delivered_date = Carbon::now();
        }

        $order->save();

        return redirect()->back()->with('status', 'Order status updated successfully!');
    }

    // ======================= Search by Barcode =======================

    public function searchByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();
        if ($product) {
            return $product;
        }
        return redirect()->route('admin.products')->with('status', 'Product not found for this barcode!');
    }

    public function coupons()
    {
        $coupons = Coupon::orderBy("expiry_date", "DESC")->paginate(12);
        return view("admin.coupons", compact("coupons"));
    }

    public function add_coupon()
    {
         $categories = Category::all();
        return view("admin.coupon.add",compact('categories'));
    }

    public function add_coupon_store(Request $request)
{
    $request->validate([
        'code' => 'required',
        'type' => 'required',
        'value' => 'required|numeric',
        'cart_value' => 'required|numeric',
        'expiry_date' => 'required|date',
        'category_id' => 'required|array' // نتحقق انه اختار كاتيغوري
    ]);

    $coupon = new Coupon();
    $coupon->code = $request->code;
    $coupon->type = $request->type;
    $coupon->value = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date = $request->expiry_date;
    $coupon->save();

    // ربط الكاتيغوريات
    $coupon->categories()->sync($request->category_id);

    return redirect()->route("admin.coupons")->with('status', 'Record has been added successfully!');
}


 public function edit_coupon($id)
{
    $coupon = Coupon::findOrFail($id); // الأفضل findOrFail للتأكد من وجوده
    $categories = Category::all(); // كل الكاتيغوريات

    // نحصل على IDs الكاتيغوريات المرتبطة بالكوبون
    $couponCategoryIds = $coupon->categories->pluck('id')->toArray();

    return view('admin.coupon.edit', compact('coupon', 'categories', 'couponCategoryIds'));
}


 public function update_coupon(Request $request)
{
    $request->validate([
        'id' => 'required|exists:coupons,id',
        'code' => 'required',
        'type' => 'required',
        'value' => 'required|numeric',
        'cart_value' => 'required|numeric',
        'expiry_date' => 'required|date',
        'category_id' => 'required|array', // للتأكد انه اختار شيء
    ]);

    // جلب الكوبون
    $coupon = Coupon::findOrFail($request->id);
    $coupon->code = $request->code;
    $coupon->type = $request->type;
    $coupon->value = $request->value;
    $coupon->cart_value = $request->cart_value;
    $coupon->expiry_date = $request->expiry_date;

    // حفظ بيانات الكوبون الأساسية
    $coupon->save();

    // ربط الكاتيجوريات الجديدة في pivot table
    $coupon->categories()->sync($request->category_id);

    return redirect()->route('admin.coupons')->with('status', 'Record has been updated successfully!');
}

    public function delete_coupon($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status', 'Record has been deleted successfully !');
    }

    public function delete_order($id)
    {
        $order = Order::find($id);
        $order->delete();
        return redirect()->route('admin.orders')->with('status', 'Record has been deleted successfully !');
    }

    public function holdBill(Request $request)
    {
        DB::beginTransaction();

        try {
            $bill = Bill::create([
                'bill_number' => 'BILL-' . time() . '-' . rand(1000, 9999),
                'name' => $request->name . ' - website',
                'phone_number' => $request->phone_number,
                'reference' => $request->reference,
                'status' => 'unpaidWebsite',
                'total_price' => $request->total,
                'payment_method' => 'cash',
                'total_items' => collect($request->products)->sum(fn($prod) => $prod['quantity'] ?? 1),
                'user_id' => Auth::id() ?? 5,
            ]);

            foreach ($request->products as $prod) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'product_id' => $prod['product_id'],
                    'child_id' => $prod['child_id'],
                    'price' => $prod['price'],
                    'quantity' => $prod['quantity'] ?? 1,
                ]);
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

    public function reports()
    {
        $bills = null;
        $categories = Category::all();
        return view("admin.reports", compact('bills', 'categories'));
    }

    public function generateReport(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $from = $request->date_from;
        $to = $request->date_to;
        $type = 'web';
        $categoryId = $request->category_id;

        $query = Bill::with(['billItems.product.categories']) // علاقة الكاتيغوري
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        if ($type === 'pos') {
            $query->whereIn('status', ['paid']); // أمثلة لحالة POS فقط
        } elseif ($type === 'web') {
            $query->whereIn('status', ['paidWebsite']); // حالة ويب فقط
        } else {
            $query->whereIn('status', ['paid', 'paidWebsite']); // حالة ويب فقط
        } // else 'both' لا نفلتر على الحالة

        if ($categoryId) {
            $query->whereHas('billItems.product.categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }


        $bills = $query->get();
        $categories = Category::all();

        return view('admin.reports', compact('bills', 'from', 'to', 'type', 'categories', 'categoryId'));
    }
  
}
