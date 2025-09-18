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
        Schema::table('orders', function (Blueprint $table) {
            // // Make sure the column exists and is the correct type
            // $table->int('client_id')->nullable()->after('id');

            // // Add foreign keys
            // $table->foreign('client_id')
            //       ->references('client_id')->on('clients')
            //       ->onDelete('cascade');  // or cascade, restrict, etc.

           $table->string('service')->nullable()->after('order_status');
           $table->dateTime('date')->nullable()->after('last_check');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // $table->dropForeign(['client_id']);
            $table->dropColumn(['date', 'service']);

        });
    }
};
