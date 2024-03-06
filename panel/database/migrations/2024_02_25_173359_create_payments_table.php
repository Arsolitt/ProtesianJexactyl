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
            $table->unsignedInteger('user_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->enum('status', ['open', 'paid', 'canceled'])->default('open');
            $table->unsignedInteger('amount')->default(0);
            $table->string('currency')->default('RUB');
            $table->string('gateway')->default('manual');
            $table->text('url')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index(['gateway', 'external_id']);
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
