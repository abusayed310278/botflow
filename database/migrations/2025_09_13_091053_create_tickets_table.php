<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id(); // <-- uses `id` as PK

            $table->unsignedBigInteger('client_id');
            $table->string('subject', 225);

            // Business flags (kept as enums like your table)
            // In many cases these could be booleans, but we’ll match your schema.
            $table->enum('client_new', ['1','2'])->default('2');
            $table->string('status')->default('pending');
            $table->enum('support_new', ['1','2'])->default('1');
            $table->enum('canmessage', ['1','2'])->default('2');

            // Replaces your `time` and `lastupdate_time`
            $table->timestamps();

            // Helpful indexes
            $table->index('client_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
