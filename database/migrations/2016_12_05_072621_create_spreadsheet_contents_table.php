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
            $table->integer('validated')->default(1)->index();
            for($x=1;$x<=52;$x++)
                $table->text('col'.$x)->nullable();
            $table->timestamps();
        });

        \DB::update("ALTER TABLE spreadsheet_contents AUTO_INCREMENT = 10000;");
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
