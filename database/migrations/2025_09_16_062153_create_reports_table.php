<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // time grain
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month')->nullable(); // NULL = yearly rollup

            // dimensions
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable(); // <-- per your request

            // metrics
            $table->unsignedInteger('orders_count')->default(0);
            $table->decimal('quantity_sum', 18, 4)->default(0);
            $table->decimal('gross_amount', 18, 2)->default(0);
            $table->decimal('cost_amount',  18, 2)->default(0);
            $table->decimal('refund_amount',18, 2)->default(0);
            $table->decimal('fee_amount',   18, 2)->default(0);
            $table->decimal('net_amount',   18, 2)->default(0);
            $table->decimal('profit_amount',18, 2)->default(0);

            $table->timestamps();

            // indexes
            $table->index(['year','month'], 'rep_year_month');
            $table->index(['service_id','year','month'], 'rep_service');
            $table->index(['category_id','year','month'], 'rep_category');
            $table->index(['payment_id','year','month'], 'rep_payment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
