<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // Foreign keys (adjust FKs if your PKs differ)
            // $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->unsignedBigInteger('service_id')->nullable()->index();
            $table->unsignedBigInteger('new_subscriptions_id')->nullable()->index();

            // Core fields
            $table->text('order_url');

            $table->unsignedBigInteger('order_api')->default(0)->index(); // 0 = manual, else provider id
            $table->string('order_status', 32)->default('pending')->index();
            $table->string('order_error', 255)->default('-')->index();
            $table->longText('order_detail')->nullable(); // can be 'cronpending' or JSON payload

            $table->unsignedInteger('order_quantity')->default(0);
            $table->decimal('order_charge', 12, 4)->default(0);
            $table->decimal('order_extra', 12, 4)->default(0);

            $table->unsignedInteger('order_start')->default(0);
            $table->unsignedInteger('order_remains')->default(0);
            $table->dateTime('last_check')->nullable();

            $table->decimal('api_charge', 12, 4)->default(0);
            $table->decimal('api_currencycharge', 12, 4)->default(1);
            $table->string('api_orderid', 191)->nullable();
            $table->unsignedBigInteger('api_serviceid')->nullable();

            $table->decimal('order_profit', 12, 4)->default(0);

            // Dripfeed / subscriptions
            $table->unsignedTinyInteger('dripfeed')->default(0)->index(); 
            $table->unsignedBigInteger('dripfeed_id')->nullable()->index();
            $table->string('dripfeed_status', 32)->nullable()->index();
            $table->string('dripfeed_totalcharges', 32)->nullable()->index();
            $table->string('dripfeed_runs', 32)->nullable()->index();
            $table->string('dripfeed_delivery', 32)->nullable()->index();
            $table->string('dripfeed_interval', 32)->nullable()->index();
            $table->string('dripfeed_totalquantity', 32)->nullable()->index();


            $table->unsignedTinyInteger('subscriptions_type')->default(0)->index(); 
            $table->unsignedBigInteger('subscriptions_id')->nullable()->index();
            $table->string('subscriptions_status', 32)->nullable()->index();
            $table->string('subscriptions_username')->nullable();
            $table->string('subscriptions_post')->nullable();
            $table->string('subscriptions_delivery')->nullable();
            $table->string('subscriptions_delay')->nullable();
            $table->string('subscriptions_min')->nullable();
            $table->string('subscriptions_max')->nullable();
            $table->string('subscriptions_expiry')->nullable();
            $table->unsignedBigInteger('country_id')->nullable()->index();


            $table->unsignedTinyInteger('refill')->default(0);

            // Foreign key constraints (uncomment if those tables/keys exist)
            // $table->foreign('client_id')->references('client_id')->on('clients')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('services')->cascadeOnDelete();
            $table->foreign('dripfeed_id')->references('id')->on('dripfeeds')->cascadeOnDelete();
            $table->foreign('new_subscriptions_id')->references('id')->on('new_subscriptions')->cascadeOnDelete();
            $table->foreign('country_id')->references('id')->on('countries')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
