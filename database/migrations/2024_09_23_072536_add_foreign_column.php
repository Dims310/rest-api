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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles', 'id');
        });

        Schema::table('transactions', function(Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('users', 'uuid');
            $table->foreignId('service_id')->constrained('services', 'id');
        });

        Schema::table('service_prices', function(Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles', 'id');
            $table->foreignId('service_id')->nullable()->constrained('services', 'id');
        });

        Schema::table('order_status', function(Blueprint $table) {
            $table->foreignUuid('transaction_id')->constrained('transactions', 'uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
