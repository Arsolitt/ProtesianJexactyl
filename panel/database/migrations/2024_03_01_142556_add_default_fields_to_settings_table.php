<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        DB::table('settings')->insertOrIgnore([
            ['key' => 'server:editing', 'value' => false],
            ['key' => 'server:deletion', 'value' => false],
            ['key' => 'store:enabled', 'value' => false],
            ['key' => 'store:currency', 'value' => 'RUB'],
            ['key' => 'store:cost:memory', 'value' => 0.0537109375],
            ['key' => 'store:cost:disk', 'value' => 0.00390625],
            ['key' => 'store:cost:slot', 'value' => 50],
            ['key' => 'store:cost:allocation', 'value' => 5],
            ['key' => 'store:cost:backup', 'value' => 5],
            ['key' => 'store:cost:database', 'value' => 5],
            ['key' => 'store:limit:min:memory', 'value' => 512],
            ['key' => 'store:limit:min:disk', 'value' => 1024],
            ['key' => 'store:limit:min:allocations', 'value' => 1],
            ['key' => 'store:limit:min:backups', 'value' => 0],
            ['key' => 'store:limit:min:databases', 'value' => 0],
            ['key' => 'store:limit:max:memory', 'value' => 16384],
            ['key' => 'store:limit:max:disk', 'value' => 102400],
            ['key' => 'store:limit:max:allocations', 'value' => 25],
            ['key' => 'store:limit:max:backups', 'value' => 25],
            ['key' => 'store:limit:max:databases', 'value' => 25],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
};
