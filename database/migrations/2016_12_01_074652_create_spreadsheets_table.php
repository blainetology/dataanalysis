<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpreadsheetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spreadsheets', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->index();
            $table->string('name');
            $table->integer('active')->default(1);
            $table->timestamps();
        });
        \DB::update("ALTER TABLE spreadsheets AUTO_INCREMENT = 3000;");

        \App\Spreadsheet::create(['name'=>'Seminar Tracker','client_id'=>2000]);
        \App\Spreadsheet::create(['name'=>'Marketing Tracker','client_id'=>2000]);
        \App\Spreadsheet::create(['name'=>'Production Tracker','client_id'=>2000]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spreadsheets');
    }
}
