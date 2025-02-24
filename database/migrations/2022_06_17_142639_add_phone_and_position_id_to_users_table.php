<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'phone')) {
            $table->string('phone')->nullable()->after('password');
        }
        if (!Schema::hasColumn('users', 'position_id')) {
            $table->unsignedBigInteger('position_id')->after('phone');
        }
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
{
    Schema::table('users', function (Blueprint $table) {
        if (Schema::hasColumn('users', 'phone')) {
            $table->dropColumn('phone');
        }
        if (Schema::hasColumn('users', 'position_id')) {
            $table->dropColumn('position_id');
        }
    });
}

};
