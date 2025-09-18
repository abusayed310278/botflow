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
       Schema::create('referrals_payouts', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('client_id')->nullable()->index();

            // from table headers
            $table->string('code', 32)->unique();               
            $table->decimal('amount_requested', 12, 2)->default(0); // "Amount Requested"

            // 0=pending, 1=approved, 2=paid, 3=rejected (adjust as needed)
            $table->string('status')->default('pending'); 

            // "Payout Created At" / "Payout Updated At"
            $table->timestamps();

            $table->foreign('client_id')
                  ->references('client_id')->on('clients')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_payouts');
    }
};
