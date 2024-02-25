<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignId('user')->constrained('users')->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->enum('status', ['open', 'paid', 'canceled'])->default('open');
            $table->unsignedInteger('amount')->default(0);
            $table->string('currency')->default('RUB');
            $table->string('method')->default('manual');
            $table->timestamps();
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
