<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerjalanansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

     public function render()
    {
        $perjalanans = Perjalanan::all(); // Fetch all perjalanan records
        return view('livewire.perjalanan-table', compact('perjalanans'));
    }
    
    public function up()
    {
        Schema::create('perjalanans', function (Blueprint $table) {
            $table->id();  // Primary key
            $table->string('title');  // Title of the perjalanan
            $table->date('date_start');  // Start date
            $table->time('start_time');  // Start time
            $table->date('date_end');  // End date
            $table->time('end_time');  // End time
            $table->binary('file_perjalanan')->nullable();  // File of perjalanan (PDF)
            $table->timestamps();  // Created at and updated at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('perjalanans');
    }
}
