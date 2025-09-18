<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dripfeeds', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Columns requested
            $table->enum('dripfeed_status', [
                'active', 'paused', 'completed', 'canceled', 'expired', 'limit'
            ])->default('active')->index();

            $table->unsignedTinyInteger('subscriptions_type')->default(1)->index();

            $table->decimal('dripfeed_totalcharges', 12, 2)->default(0);
            $table->unsignedInteger('dripfeed_runs')->default(0);
            $table->unsignedInteger('dripfeed_delivery')->default(0);
            $table->unsignedInteger('dripfeed_totalquantity')->default(0);

            $table->timestamps(); // created_at, updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dripfeeds');
    }
};
