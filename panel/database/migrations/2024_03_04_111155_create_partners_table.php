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
        Schema::create('partners', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->unsignedInteger('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('partner_discount')->default(0);
            $table->unsignedInteger('referral_discount')->default(0);
            $table->unsignedInteger('referral_reward')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
