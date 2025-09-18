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
        Schema::create('payments', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Customer reference (you previously had an FK to clients.client_id)
            $table->unsignedInteger('client_id')->nullable()->index();

            // Money fields
            $table->decimal('client_balance', 10, 2)->nullable();  
            $table->decimal('payment_amount', 10, 4)->default(0);              

            // Optional/private code
            $table->string('payment_privatecode', 191)->nullable();

            // Integers/tinyints inferred from values (12, 1, 1)
            $table->unsignedInteger('payment_method')->nullable();  
            $table->unsignedTinyInteger('payment_status')->default(0);  
            $table->unsignedTinyInteger('payment_delivery')->default(0); 

            // Short text fields (“No”, “Auto” in screenshot)
            $table->string('payment_note', 191)->nullable();       
            $table->string('payment_mode', 191)->nullable();        

            // Datetimes (second one was '0000-00-00 00:00:00' → use nullable)
            $table->dateTime('payment_create_date');               
            $table->dateTime('payment_update_date')->nullable();    
            // IP address (IPv4/IPv6 safe length)
            $table->string('payment_ip', 45)->nullable();           
            // Extra / bank / t_id from screenshot
            $table->string('payment_extra', 191)->nullable();     
            $table->string('payment_bank', 64)->nullable();
            // $table->unsignedBigInteger('ticket_id')->nullable()->index();

            // If you want Laravel-managed timestamps as well (optional):
             $table->timestamps();

            // ---- Foreign keys (optional; uncomment & adjust to your real targets) ----
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
        Schema::dropIfExists('payments');
    }
};
