<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            // PK
            $table->increments('client_id'); // int(11) AUTO_INCREMENT PRIMARY KEY

            // Core fields
            $table->string('name', 225)->nullable();                 // NULL
            $table->string('email', 225)->unique();                  // UNIQUE
            $table->string('username', 225)->nullable()->unique();   // UNIQUE + allows multiple NULLs
            $table->enum('admin_type', ['1','2'])->default('2');
            $table->text('password');
            $table->string('telephone', 225)->nullable();

            // Money & balance
            $table->decimal('balance', 21, 4);
            $table->enum('balance_type', ['1','2'])->default('2');
            $table->double('debit_limit')->nullable();
            $table->decimal('spent', 21, 4);

            // Dates / auth info
            $table->dateTime('register_date');
            $table->dateTime('login_date')->nullable();
            $table->string('login_ip', 225)->nullable();
            $table->text('apikey'); // kept as TEXT (not unique; many APIs use long keys)

            // Types
            $table->enum('tel_type', ['1','2'])->default('1');
            $table->enum('email_type', ['1','2'])->default('1');
            $table->enum('client_type', ['1','2'])->default('2');

            // Misc
            $table->text('access')->nullable();
            $table->string('lang', 255)->default('tr');
            $table->double('timezone')->default(0);
            $table->enum('currency_type', ['INR','USD'])->default('USD');
            $table->text('ref_code');                 // not unique (referrals can repeat)
            $table->text('ref_by')->nullable();
            $table->enum('change_email', ['1','2'])->default('2');
            $table->integer('resend_max');            // requires value on insert
            $table->string('currency', 225)->default('1');
            $table->string('passwordreset_token', 225);
            $table->integer('coustm_rate');           // (spelling preserved)
            $table->string('verified', 3)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
