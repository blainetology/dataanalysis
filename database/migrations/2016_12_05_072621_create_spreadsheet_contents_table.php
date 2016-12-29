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
            $table->integer('year')->default(0)->index();
            $table->integer('month')->default(0)->index();
            for($x=1;$x<=26;$x++)
                $table->text('col'.$x)->nullable();
            $table->timestamps();
        });

        \DB::update("ALTER TABLE spreadsheet_contents AUTO_INCREMENT = 10000;");
        \App\SpreadsheetContent::create(['spreadsheet_id'=>3000,'added_by'=>1000,'year'=>date('Y'),'month'=>date('n'),'col1'=>'KEZ99','col2'=>'John Doe','col3'=>'radio','col4'=>'1000','col5'=>'3400']);
        \App\SpreadsheetContent::create(['spreadsheet_id'=>3000,'added_by'=>1001,'year'=>date('Y'),'month'=>date('n')-1,'col1'=>'KUPD','col2'=>'John Doe','col3'=>'radio','col4'=>'1200','col5'=>'3260']);
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
