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
        Schema::create('referral_payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('payment_id')->constrained('payments')->nullOnDelete();
            $table->unsignedInteger('user_id')->constrained('users')->nullOnDelete();
            $table->string('referral_code');
            $table->unsignedInteger('payment_amount')->default(0);
            $table->unsignedInteger('reward_percent')->default(0);
            $table->unsignedFloat('reward_amount')->default(0);
            $table->timestamps();

            $table->index('payment_id');
            $table->index('user_id');
            $table->index('referral_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_payments');
    }
};
