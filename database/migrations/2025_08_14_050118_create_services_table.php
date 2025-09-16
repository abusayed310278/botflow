<?php

// database/migrations/xxxx_xx_xx_create_services_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();// From SMM API
            $table->string('name');
            $table->string('type')->nullable();
            $table->decimal('rate', 10, 4)->nullable(); // Original price from SMM API
            $table->decimal('custom_rate', 10, 4)->nullable(); // Admin-adjusted price
            $table->integer('min')->default(0);
            $table->integer('max')->default(0);
            $table->boolean('dripfeed')->default(false);
            $table->boolean('refill')->default(false);
            $table->boolean('cancel')->default(false);
            $table->string('category')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
