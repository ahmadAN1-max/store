<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->string('reference');
            $table->decimal('total_price', 10, 2);
            $table->integer('total_items');
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->enum('payment_method', ['cash', 'card', 'other'])->default('cash');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // علاقات
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
