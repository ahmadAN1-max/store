<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Product;
use App\Models\Setting;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\DB; 

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::instance('cart')->content();

        foreach ($cartItems as $key => $item) {
            if (!$item->model) {
                Cart::instance('cart')->remove($key);
            }
        }

        $subtotal = Cart::instance('cart')->subtotal();
        $total = $subtotal;
        $discount = 0;

        $couponSession = session()->get('coupon');

        // تحقق من وجود الكوبون وأنه array فيه مفتاح id
        if (is_array($couponSession) && isset($couponSession['id'])) {
            $coupon = \App\Models\Coupon::find($couponSession['id']);

            if ($coupon) {
                $couponCategoryIds = $coupon->categories->pluck('id')->toArray();

                foreach ($cartItems as $item) {
                    $product = \App\Models\Product::find($item->id);

                    if ($product) {
                        $productCategoryIds = $product->categories->pluck('id')->toArray();

                        if (count(array_intersect($couponCategoryIds, $productCategoryIds)) > 0) {
                            if ($coupon->type === 'fixed') {
                                $discount += $coupon->value;
                            } elseif ($coupon->type === 'percent') {
                                $discount += ($item->price * $coupon->value / 100);
                            }
                        }
                    }
                }

                $total = $subtotal - $discount;
            }
        }

        return response()->view('cart', compact('cartItems', 'subtotal', 'discount', 'total'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->id);

        Cart::instance('cart')->add(
            $product->id,
            $product->name,
            $request->quantity,
            $product->sale_price != '' ? $product->sale_price : $product->regular_price,
            [
                'size' => $request->size
            ]
        )->associate('App\Models\Product');

        session()->flash('success', 'Product is Added to Cart Successfully!');
        return redirect()->back();
    }

    public function increase_item_quantity($rowId)
    {
        $cartItem = Cart::instance('cart')->get($rowId);
        $productModel = Product::where('parent_id', $cartItem->id)
            ->where('sizes', $cartItem->options->size)
            ->first();

        if (!$productModel) {
            return redirect()->back()->with('error', 'Product size not found!');
        }

        $qty = $cartItem->qty + 1;

        if ($qty > $productModel->quantity) {
            return redirect()->back()->with('error', 'Available stock for this size is only ' . $productModel->quantity);
        }

        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }

    public function reduce_item_quantity($rowId)
    {
        $cartItem = Cart::instance('cart')->get($rowId);
        $qty = $cartItem->qty - 1;
        if ($qty < 1) {
            return redirect()->back()->with('error', 'Minimum quantity is 1');
        }
        Cart::instance('cart')->update($rowId, $qty);
        $this->remove_coupon_code();
        return redirect()->back();
    }

    public function remove_item_from_cart($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function checkout()
    {
        if (Cart::instance('cart')->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'no items found');
        }
        return view('cart.checkout');
    }

   public function place_order(Request $request)
{
    $deliveryCharge = Setting::where('key', 'delivery_charge')->value('value') ?? 0;

    if (!Auth::check()) {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'city' => 'required|string',
            'address' => 'required|string',
        ]);
    }

    $user_id = Auth::check() ? Auth::id() : null;
    $user_name = $request->name;
    $phone = $request->phone;
    $city = $request->city;
    $address_txt = $request->address;

    // تأكد أن السلة ليست فارغة
    if (Cart::instance('cart')->count() == 0) {
        return redirect()->route('cart.index')->with('error', 'No items found in cart');
    }

    // تحضير المبالغ (يمكن استخدام هذا لتأكيد المجموع في الواجهة)
    $checkoutAmounts = $this->setAmountForCheckout();

    // استخدم معاملة لعمل القفل والتحقق والخصم بشكل ذري
    try {
        DB::transaction(function () use ($request, $user_id, $user_name, $phone, $city, $address_txt, $deliveryCharge, $checkoutAmounts) {

            // قبل إنشاء الطلب، تأكد من توفر الكميات لكل عنصر
            foreach (Cart::instance('cart')->content() as $item) {
                $childProduct = Product::where('parent_id', $item->id)
                    ->where('sizes', $item->options->size)
                    ->lockForUpdate() // قفل الصف لمنع سباق التحديث
                    ->first();

                if (!$childProduct) {
                    throw new \Exception("Selected size not available for product: {$item->name}");
                }

                if ($childProduct->quantity < $item->qty) {
                    throw new \Exception("Only {$childProduct->quantity} item(s) available for {$item->name} (size: {$item->options->size})");
                }
            }

            // كل شيء جاهز — أنشئ الـ Order
            $order = new Order();
            $order->user_id = $user_id;
            $order->name = $user_name;
            $order->subtotal = $request->subtotal ?? $checkoutAmounts['total'] ?? 0;
            $order->discount = $request->discount ?? $checkoutAmounts['discount'] ?? 0;
            $order->tax = 0;
            $order->total = ($request->subtotal ?? $checkoutAmounts['grand_total'] ?? 0) + $deliveryCharge;
            $order->phone = $phone ?? '';
            $order->city = $city ?? '';
            $order->address = $address_txt ?? '';
            $order->locality = '';
            $order->state = '';
            $order->country = '';
            $order->landmark = '';
            $order->zip = '';
            $order->save();

            // أنشئ عناصر الطلب وخصم الكمية (ضمن نفس المعاملة وبنفس القفل)
            foreach (Cart::instance('cart')->content() as $item) {
                $orderItem = new OrderItem();
                $orderItem->product_id = $item->id;
                $orderItem->order_id = $order->id;
                $orderItem->price = $item->price;
                $orderItem->quantity = $item->qty;
                $orderItem->size = $item->options->size ?? null;
                $orderItem->save();

                // جلب الـ child مجدداً بواسطة قفل (نفس القفل لأننا داخل الـ transaction)
                $childProduct = Product::where('parent_id', $item->id)
                    ->where('sizes', $item->options->size)
                    ->lockForUpdate()
                    ->first();

                // هنا من المفروض أن يكون التأكد تم سابقاً، لكن نضمنه مرة ثانية
                if ($childProduct && $childProduct->quantity >= $item->qty) {
                    // خصم الكمية بطريقة ذرية
                    $childProduct->quantity -= $item->qty;
                    $childProduct->quantity = max($childProduct->quantity, 0);
                    $childProduct->save();
                } else {
                    throw new \Exception("Insufficient stock for product: {$item->name} (size: {$item->options->size})");
                }

                // تحديث كمية المنتج الأب بإجمالي كميات الأطفال
                $parentProduct = Product::find($item->id);
                if ($parentProduct) {
                    $parentProduct->quantity = Product::where('parent_id', $parentProduct->id)->sum('quantity');
                    $parentProduct->save();
                }
            }

            // (اختياري) تسجيل معاملة أو غير ذلك هنا
        }, 5); // المحاولة حتى 5 مرات في حالة حدوث قفل متزامن
    } catch (\Exception $e) {
        // في حال أي خطأ داخل المعاملة سيتم rollback تلقائياً — نرجع رسالة للمستخدم
        return redirect()->route('cart.index')->with('error', $e->getMessage());
    }

    // نجاح — نظف السلة والجلسات
    Cart::instance('cart')->destroy();
    session()->forget('checkout');
    session()->forget('coupon');
    session()->forget('discounts');

    return redirect()->route('cart.index')->with('order_success', 'Your order is on its way!');
}

    public function setAmountForCheckout($coupon = null)
    {
        $cartItems = Cart::instance('cart')->content();
        $total = 0;
        $discount = 0;

        foreach ($cartItems as $item) {
            $total += $item->price * $item->qty;

            if ($coupon) {
                if ($this->applyCouponToCartItem($item, $coupon)) {
                    if ($coupon->type === 'fixed') {
                        $discount = $coupon->value; // بدل +=

                    } elseif ($coupon->type === 'percent') {
                        $discount += ($item->price * $item->qty * $coupon->value / 100);
                    }
                }
            }
        }

        $deliveryCharge = 0; // حسب حاجتك
        $grandTotal = $total - $discount + $deliveryCharge;

        return [
            'total' => $total,
            'discount' => $discount,
            'delivery_charge' => $deliveryCharge,
            'grand_total' => $grandTotal,
        ];
    }




    public function confirmation()
    {

        if (Cart::instance('cart')->count() > 0) {
            return redirect()->route('cart.index')->with('error', 'Cart is not empty yet!');
        }
        return view('order.confirmation');
    }


    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;

        if (!$coupon_code) {
            return back()->with('error', 'Invalid coupon code!');
        }

        $coupon = Coupon::where('code', $coupon_code)
            ->where('expiry_date', '>=', Carbon::today())
            ->where('cart_value', '<=', Cart::instance('cart')->subtotal())
            ->first();

        if (!$coupon) {
            return back()->with('error', 'Invalid coupon code!');
        }

        // تحقق من الكاتيجوري
        $cartItems = Cart::instance('cart')->content();
        $applies = false;

        foreach ($cartItems as $item) {
            if ($this->applyCouponToCartItem($item, $coupon)) {
                $applies = true;
                break;
            }
        }

        if (!$applies) {
            return back()->with('error', 'Coupon does not apply to any product in your cart!');
        }

        // نحفظ الكوبون بالسيشن
        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'cart_value' => $coupon->cart_value,
        ]);

        $this->calculateDiscounts();

        return back()->with('status', 'Coupon code has been applied!');
    }


    public function calculateDiscounts()
    {
        $discount = 0;
        if (session()->has('coupon')) {
            if (session()->get('coupon')['type'] == 'fixed') {
                $discount = session()->get('coupon')['value'];
            } else {
                $discount = (Cart::instance('cart')->subtotal() * session()->get('coupon')['value']) / 100;
            }
            $subtotalAfterDiscount = Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
            session()->put('discounts', [
                'discount' => number_format(floatval($discount), 2, '.', ''),
                'subtotal' => number_format(floatval(Cart::instance('cart')->subtotal() - $discount), 2, '.', ''),
                'tax' => number_format(floatval((($subtotalAfterDiscount * config('cart.tax')) / 100)), 2, '.', ''),
                'total' => number_format(floatval($subtotalAfterDiscount + $taxAfterDiscount), 2, '.', '')
            ]);
        }
    }
    protected function applyCouponToCartItem($item, $coupon)
    {
        // إذا الكوبون عام (ما عندو كاتيجوريز)
        if ($coupon->categories->isEmpty()) {
            return true;
        }

        // جلب جميع كاتيجوريات المنتج
        $productCategoryIds = $item->model->categories->pluck('id')->toArray();

        // جلب جميع كاتيجوريات الكوبون
        $couponCategoryIds = $coupon->categories->pluck('id')->toArray();

        // التحقق من وجود أي تطابق
        return count(array_intersect($productCategoryIds, $couponCategoryIds)) > 0;
    }




    public function remove_coupon_code()
    {
        session()->forget('coupon');
        session()->forget('discounts');
        return back()->with('status', 'Coupon has been removed!');
    }
}
