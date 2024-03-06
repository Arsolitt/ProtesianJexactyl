<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referral_uses', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('code_used');
            $table->unsignedInteger('user_id')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('referrer_id')->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('code_used');
            $table->index('user_id');
            $table->index('referrer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('referral_uses');
    }
};
