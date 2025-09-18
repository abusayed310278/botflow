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
            $table->unsignedInteger('client_id')->nullable();

            // add the foreign key
            $table->foreign('client_id')
                  ->references('client_id')   // column in parent table
                  ->on('clients')      // parent table
                  ->onDelete('cascade') // optional
                  ->onUpdate('cascade'); // optional
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['client_id']); // drop FK
            $table->dropColumn('client_id');    // drop column
        });
    }
};
