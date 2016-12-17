<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpreadsheetColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spreadsheet_columns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('spreadsheet_id')->index();
            $table->string('column');
            $table->string('label');
            $table->text('validation')->nullable();
            $table->timestamps();
        });
        \DB::update("ALTER TABLE spreadsheet_columns AUTO_INCREMENT = 5000;");
        \App\SpreadsheetColumn::create(['spreadsheet_id'=>3000,'column'=>1,'label'=>'Name']);
        \App\SpreadsheetColumn::create(['spreadsheet_id'=>3000,'column'=>2,'label'=>'Advisor']);
        \App\SpreadsheetColumn::create(['spreadsheet_id'=>3000,'column'=>3,'label'=>'Source']);
        \App\SpreadsheetColumn::create(['spreadsheet_id'=>3000,'column'=>4,'label'=>'Cost']);
        \App\SpreadsheetColumn::create(['spreadsheet_id'=>3000,'column'=>5,'label'=>'Generated Income']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spreadsheet_columns');
    }
}
