
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillItemsTable extends Migration
{
    public function up()
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained()->onDelete('cascade'); // رابط مع جدول الفواتير
            $table->unsignedBigInteger('product_id'); // المنتج الأب
            $table->unsignedBigInteger('child_id')->nullable(); // المنتج الفرعي (حجم أو نوع)
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            // إضافة مفاتيح خارجية إذا كنت تريد الربط مع جدول المنتجات
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('child_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bill_items');
    }
}
