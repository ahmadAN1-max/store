<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bills', function (Blueprint $table) {
              $table->dropColumn('status');
        });
          Schema::table('bills', function (Blueprint $table) {
        $table->enum('status', ['paid','unpaid','paidWebsite', 'unpaidWebsite'])->default('unpaid');
        $table->string('name')->nullable();
        $table->string('phone_number')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('bills', function (Blueprint $table) {
        $table->dropColumn(['status', 'name', 'phone_number']);
        $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
    });
    }
};
