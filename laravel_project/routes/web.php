<?php

use App\Models\Product;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ProductController;

Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::middleware('auth')->group(function () {
  Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
  Route::get('/account-orders', [UserController::class, 'account_orders'])->name('user.account.orders');
  Route::get('/account-order-detials/{order_id}', [UserController::class, 'account_order_details'])->name('user.account.order.details');
  Route::put('/account-order/cancel-order', [UserController::class, 'account_cancel_order'])->name('user.account_cancel_order');
  Route::post('/user/address/update', [UserController::class, 'updateAddress'])->name('user.update.address');
});


Route::middleware(['auth.role:ADM'])->group(function () {
  Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
  Route::get('/admin/brands', [AdminController::class, 'brands'])->name('admin.brands');
  Route::get('/admin/brand/add', [AdminController::class, 'add_brand'])->name('admin.brands.add');
  Route::post('/admin/brand/store', [AdminController::class, 'add_brand_store'])->name('admin.brand.store');
  Route::get('/admin/brand/edit/{id}', [AdminController::class, 'edit_brand'])->name('admin.brands.edit');
  Route::put('/admin/brand/update', [AdminController::class, 'update_brand'])->name('admin.brand.update');
  Route::delete('/admin/brand/{id}/delete', [AdminController::class, 'delete_brand'])->name('admin.brand.delete');
  Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
  Route::get('/admin/category/add', [AdminController::class, 'add_category'])->name('admin.category.add');
  Route::post('/admin/category/store', [AdminController::class, 'add_category_store'])->name('admin.category.store');
  Route::get('/admin/category/{id}/edit', [AdminController::class, 'edit_category'])->name('admin.category.edit');
  Route::put('/admin/category/update', [AdminController::class, 'update_category'])->name('admin.category.update');
  Route::delete('/admin/category/{id}/delete', [AdminController::class, 'delete_category'])->name('admin.category.delete');
  Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
  Route::get('/admin/product/add', [AdminController::class, 'add_product'])->name('admin.product.add');
  Route::post('/admin/product/store', [AdminController::class, 'product_store'])->name('admin.product.store');
  Route::get('/admin/product/{id}/edit', [AdminController::class, 'edit_product'])->name('admin.product.edit');
  Route::put('/admin/product/update', [AdminController::class, 'update_product'])->name('admin.product.update');
  Route::delete('/admin/product/{id}/delete', [AdminController::class, 'delete_product'])->name('admin.product.delete');
  Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
  Route::get('/admin/order/items/{order_id}', [AdminController::class, 'order_items'])->name('admin.order.items');
  Route::put('/admin/order/update-status', [AdminController::class, 'update_order_status'])->name('admin.order.status.update');
  Route::delete('/admin/order/{id}/delete', [AdminController::class, 'delete_order'])->name('admin.order.delete');
  Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
  Route::get('admin/reports/generate', [AdminController::class, 'generateReport'])->name('admin-report.generate');



  // Admin Coupon routes
  Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
  Route::get('/admin/coupon/add', [AdminController::class, 'add_coupon'])->name('admin.coupon.add');
  Route::post('/admin/coupon/store', [AdminController::class, 'add_coupon_store'])->name('admin.coupon.store');
  Route::get('/admin/coupon/{id}/edit', [AdminController::class, 'edit_coupon'])->name('admin.coupon.edit');
  Route::put('/admin/coupon/update', [AdminController::class, 'update_coupon'])->name('admin.coupon.update');
  Route::delete('/admin/coupon/{id}/delete', [AdminController::class, 'delete_coupon'])->name('admin.coupon.delete');

  Route::post('/admin/holdBil', [AdminController::class, 'holdBill'])->name('admin.holdBill');
  Route::post('/admin/settings/delivery-charge', [AdminController::class, 'updateDeliveryCharge'])->name('admin.settings.updateDeliveryCharge');
});

Route::get('/search', [ShopController::class, 'search'])->name('product.search');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name("shop.product.details");
Route::get('/shop/category/{slug}', [ShopController::class, 'categoryProducts'])->name('shop.byCategorySlug');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
//Route::post('/cart/store', [CartController::class, 'addToCart'])->name('cart.store');
Route::put('/cart/increase-qunatity/{rowId}', [CartController::class, 'increase_item_quantity'])->name('cart.increase.qty');
Route::put('/cart/reduce-qunatity/{rowId}', [CartController::class, 'reduce_item_quantity'])->name('cart.reduce.qty');
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart/remove/{rowId}', [CartController::class, 'remove_item_from_cart'])->name('cart.remove');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');

Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/remove/{rowId}', [WishlistController::class, 'remove_item_from_wishlist'])->name('wishlist.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('wishlist.empty');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.move.to.cart');

Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/place-order', [CartController::class, 'place_order'])->name('cart.place.order');
Route::get('/order-confirmation', [CartController::class, 'confirmation'])->name('cart.confirmation');
Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('cart.coupon.apply');
Route::delete('/cart/remove-coupon', [CartController::class, 'remove_coupon_code'])->name('cart.coupon.remove');

Route::middleware(['auth', 'auth.role:POS,POSADM'])->group(function () {
  Route::get('/pos', [PosController::class, 'index'])->name('pos.index');


  Route::get('/pos/brands', [PosController::class, 'brands'])->name('pos.brands');
  Route::get('/pos/brand/add', [PosController::class, 'add_brand'])->name('pos.brand-add');
  Route::post('/pos/brand/store', [PosController::class, 'add_brand_store'])->name('pos.brand-store');
  Route::get('/pos/brand/edit/{id}', [PosController::class, 'edit_brand'])->name('pos.brand-edit');
  Route::put('/pos/brand/update', [PosController::class, 'update_brand'])->name('pos.brand-update');
  Route::delete('/pos/brand/{id}/delete', [PosController::class, 'delete_brand'])->name('pos.brand-delete');
  Route::get('/pos/categories', [PosController::class, 'categories'])->name('pos.categories');
  Route::get('/pos/category/add', [PosController::class, 'add_category'])->name('pos.category-add');
  Route::post('/pos/category/store', [PosController::class, 'add_category_store'])->name('pos.category.store');
  Route::get('/pos/category/{id}/edit', [PosController::class, 'edit_category'])->name('pos.category-edit');
  Route::put('/pos/category/update', [PosController::class, 'update_category'])->name('pos.category-update');
  Route::delete('/pos/category/{id}/delete', [PosController::class, 'delete_category'])->name('pos.category-delete');
  Route::get('/pos/products', [PosController::class, 'products'])->name('pos.products');
  Route::get('/pos/product/add', [PosController::class, 'add_product'])->name('pos.product-add');
  Route::post('/pos/product/store', [PosController::class, 'product_store'])->name('pos.product.store');
  Route::get('/pos/product/{id}/edit', [PosController::class, 'edit_product'])->name('pos.product-edit');
  Route::put('/pos/product/update', [PosController::class, 'update_product'])->name('pos.product.update');
  Route::delete('/pos/product/{id}/delete', [PosController::class, 'delete_product'])->name('pos.product.delete');
  // Route::get('/pos/scan-product/{barcode}', [PosController::class, 'scanAndAddProduct']);
  // Route::middleware('auth')->post('/pos/save-bill', [PosController::class, 'saveBill'])->name('pos.saveBill');

  Route::get('/pos/bill/print/{id}', [PosController::class, 'printBill'])->name('pos.bill.print');
  Route::get('/pos/paidbills', [PosController::class, 'paidBills'])->name('pos.paidBills');
  Route::get('/pos/bills', [PosController::class, 'bills'])->name('pos.bills');
  Route::delete('/pos/bill/{id}/delete', [PosController::class, 'delete_bill'])->name('pos.bill.delete');
  Route::get('/pos/reports', [PosController::class, 'reports'])->name('pos.reports');
  // Route::get('/pos/import', [PosController::class, 'importByExcel'])->name('pos.import');
  // Route::get('/pos/importExcel', [PosController::class, 'importExcel'])->name('pos.importExcel');
  Route::get('/pos/customers', [PosController::class, 'customers'])->name('pos.customers');
  Route::get('/pos/customer/add', [PosController::class, 'add_customer'])->name('pos.customer-add');
  Route::post('/pos/customer/store', [PosController::class, 'add_customer_store'])->name('pos.customer-store');
  Route::get('/pos/customer/edit/{id}', [PosController::class, 'edit_customer'])->name('pos.customer-edit');
  Route::put('/pos/customer/update', [PosController::class, 'update_customer'])->name('pos.customer-update');
  Route::delete('/pos/customer/{id}/delete', [PosController::class, 'delete_customer'])->name('pos.customer-delete');
  Route::get('/reports/generate', [PosController::class, 'generateReport'])->name('report.generate');
  Route::get('/pos/return', [PosController::class, 'return'])->name('pos.return');

  Route::post('/pos/returnBill', [POSController::class, 'returnBill'])->name('pos.returnBill');
  Route::get('/pos/view/{bill}', [POSController::class, 'view'])->name('pos.view');

  Route::get('/pos/scan/{barcode}', [POSController::class, 'scan'])->name('pos.scan');
  Route::post('/pos/hold-bill', [PosController::class, 'holdBill'])->name('pos.hold-bill');
  Route::post('/pos/save-bill', [PosController::class, 'saveBill'])->name('pos.save-bill');
  Route::post('/pos/checkout', [POSController::class, 'checkout'])->name('pos.checkout');
  Route::post('/pos/checkoutSaved', [PosController::class, 'checkoutSaved'])->name('pos.checkoutSaved');
  Route::get('/pos/settings', [POSController::class, 'settings'])->name('pos.settings');
  Route::post('/pos/setMaxDiscount', [POSController::class, 'setMaxDiscount'])->name('pos.setMaxDiscount');
  Route::get('/products/import', [POSController::class, 'showImportForm'])->name('products.import.form');
  Route::post('/products/import', [POSController::class, 'import'])->name('products.import');

  Route::get('/products/export-barcodes', [PosController::class, 'exportBarcodes'])->name('products.exportBarcodes');
  Route::post('/products/import-barcodes', [PosController::class, 'importBarcodes'])->name('products.importBarcodes');
});
