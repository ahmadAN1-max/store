<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('coupons', function (Blueprint $table) {
        $table->unsignedBigInteger('category_id')->nullable(); 
        // assuming عندك عمود discount، غيره إذا بدك موقعه يجي بعد عمود تاني
    });
}

public function down()
{
    Schema::table('coupons', function (Blueprint $table) {
        $table->dropColumn('category_id');
    });
}

};
