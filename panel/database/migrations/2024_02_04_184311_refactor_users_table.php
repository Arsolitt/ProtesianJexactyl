<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Jexactyl\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedFloat('credits')->default(0);
            $table->unsignedInteger('server_slots')->default(2);
            $table->string('role')->default(User::USER_LEVEL_USER);
        });
        User::all()->each(function ($user) {
            $user->update(['credits' => $user->store_balance,
                'server_slots' => $user->store_slots,
                'role' => $user->root_admin ? User::USER_LEVEL_ADMIN : User::USER_LEVEL_USER]);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('store_slots');
            $table->dropColumn('store_ports');
            $table->dropColumn('store_backups');
            $table->dropColumn('store_databases');
            $table->dropColumn('store_cpu');
            $table->dropColumn('store_memory');
            $table->dropColumn('store_disk');
            $table->dropColumn('store_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
