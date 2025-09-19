<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Address;


class UserController extends Controller
{
    public function index()
    {
        return view("user.index");
    }

    public function account_orders()
{
$orders = Order::where('user_id',Auth::user()->id)->orderBy('created_at','DESC')->paginate(10);
return view('user.orders',compact('orders'));
}

public function account_order_details($order_id)
{
        $order = Order::where('user_id',Auth::user()->id)->find($order_id);        
        $orderItems = OrderItem::where('order_id',$order_id)->orderBy('id')->paginate(12);
        $transaction = Transaction::where('order_id',$order_id)->first();
        return view('user.order-details',compact('order','orderItems','transaction'));
}

public function account_cancel_order(Request $request)
{
    $order = Order::find($request->order_id);
    $order->status = "canceled";
    $order->canceled_date = Carbon::now();
    $order->save();
    return back()->with("status", "Order has been cancelled successfully!");
}

 public function updateAddress(Request $request)
    {
        $request->validate([
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            // أضف حقول أخرى إذا تحتاج
        ]);

        $user = Auth::user();

        $address = Address::where('user_id', $user->id)->where('isdefault', true)->first();

        if (!$address) {
            // إذا ما في عنوان، أنشئ جديد
            $address = new Address();
            $address->user_id = $user->id;
            $address->isdefault = true;
        }

        $address->city = $request->city;
        $address->address = $request->address;
        $address->phone = $request->phone;
        // إذا في حقول أخرى مثل state, zip, locality, landmark أضفها هنا

        $address->save();

        return redirect()->back()->with('status', 'تم تحديث العنوان بنجاح!');
    }
}


