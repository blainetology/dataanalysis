<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpreadsheetContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spreadsheet_contents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('spreadsheet_id')->index();
            $table->integer('added_by')->default(0)->index();
            $table->integer('revision_id')->default(0)->index();
            for($x=1;$x<=52;$x++)
                $table->text('col'.$x)->nullable();
            $table->timestamps();
        });

        \DB::update("ALTER TABLE spreadsheet_contents AUTO_INCREMENT = 10000;");
       # \App\SpreadsheetContent::create(['spreadsheet_id'=>3000,'added_by'=>1000,'col1'=>'KEZ99','col2'=>'John Doe','col3'=>'2016-03-01','col4'=>'radio','col5'=>'1000','col6'=>'3400']);
       # \App\SpreadsheetContent::create(['spreadsheet_id'=>3000,'added_by'=>1001,'col1'=>'KUPD','col2'=>'John Doe','col3'=>'2016-04-21','col4'=>'radio','col5'=>'1200','col6'=>'3260']);
       # \App\SpreadsheetContent::create(['spreadsheet_id'=>3000,'added_by'=>1001,'col1'=>'KUPD','col2'=>'John Smith','col3'=>'2016-04-13','col4'=>'radio','col5'=>'1315','col6'=>'3175']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spreadsheet_contents');
    }
}
