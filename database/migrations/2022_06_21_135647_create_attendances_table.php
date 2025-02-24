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
    if (!Schema::hasTable('attendances')) {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description', 500);
            $table->time('start_time');
            $table->time('batas_start_time');
            $table->time('end_time');
            $table->time('batas_end_time');
            $table->string('code')->nullable();
            $table->timestamps();
        });
    }
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
};
