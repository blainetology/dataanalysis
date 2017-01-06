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
            $table->string('type')->default('string');
            $table->text('validation')->nullable();
            $table->timestamps();
        });
        \DB::update("ALTER TABLE spreadsheet_columns AUTO_INCREMENT = 5000;");
        $labels = [1=>"Name", 2=>"Advisor", 3=>"Run Date", 4=>"Source", 5=>"Cost", 6=>"Generated Income"];
        foreach($labels as $column=>$label)
          \App\SpreadsheetColumn::create(['spreadsheet_id'=>3000,'column'=>$column,'label'=>$label]);

        $labels = [1=>"Name", 2=>"Marketing Source", 3=>"Type", 4=>"Advisor Name", 5=>"Location", 6=>"Seminar Date", 7=>"Notes", 8=>"Set", 9=>"Kept"];
        foreach($labels as $column=>$label)
          \App\SpreadsheetColumn::create(['spreadsheet_id'=>3001,'column'=>$column,'label'=>$label]);
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
