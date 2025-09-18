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
        Schema::create('additionals_updates', function (Blueprint $table) {
            $table->id();
            $table->string('service_id');
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->date('date');
            $table->timestamps();

            $table->foreign('service_id')->references('service')->on('services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additionals_updates');
    }
};
