<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();               // Coupon Code
            $table->decimal('piece', 10, 2)->default(0);    // <- piece (per your spec)
            $table->decimal('amount', 10, 2)->default(0);   // amount/face value

            $table->timestamps();

            $table->index('piece');
            $table->index('amount');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
