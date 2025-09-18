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
         Schema::create('referrals', function (Blueprint $table) {
            $table->id();

            // FK to clients.client_id (as in your other tables)
            $table->unsignedInteger('referral_client_id')->nullable()->index();

            // Counters
            $table->unsignedInteger('referral_clicks')->default(0);
            $table->unsignedInteger('referral_sign_up')->default(0);

            // Money values (use 2 decimals; adjust if you prefer 4)
            $table->decimal('referral_totalFunds_byRefered', 12, 2)->default(0);
            $table->decimal('referral_earned_commision', 12, 2)->default(0);
            $table->decimal('referral_requested_commision', 12, 2)->default(0);
            $table->decimal('referral_total_commision', 12, 2)->default(0);
            $table->decimal('referral_rejected_commision', 12, 2)->default(0);

            // Status + code
            $table->unsignedTinyInteger('referral_status')->default(0); 
            $table->string('referral_code', 64)->nullable()->index();

            $table->timestamps();

            // Foreign key (adjust onDelete as you prefer)
            $table->foreign('referral_client_id')
                  ->references('client_id')->on('clients')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
