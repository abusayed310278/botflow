<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('new_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->unsignedBigInteger('category_id');         // FK to categories
            $table->enum('servicetype', ['1','2']);            // 1=Manual, 2=API
            $table->enum('service_package', ['1','2'])->nullable();
            $table->unsignedBigInteger('service_api');         // FK to providers
            $table->string('service_price')->nullable();
            $table->double('service_min')->default(0);
            $table->double('service_max')->default(0);
            $table->enum('service_speed', ['1','2','3','4'])->default('2');
            $table->enum('price_type', ['normal','special'])->default('normal');
            $table->enum('api_alert', ['1','2'])->default('2');
            $table->enum('status', ['1','2'])->default('1');
            $table->timestamps();

            // Foreign keys
            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnUpdate()->restrictOnDelete();
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_subscriptions');
    }
};
