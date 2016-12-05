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
            $table->integer('added_by')->index();
            $table->integer('year')->year('0')->index();
            $table->integer('month')->year('0')->index();
            $table->text('A');
            $table->text('B');
            $table->text('C');
            $table->text('D');
            $table->text('E');
            $table->text('F');
            $table->text('G');
            $table->text('H');
            $table->text('I');
            $table->text('J');
            $table->text('K');
            $table->text('L');
            $table->text('M');
            $table->text('N');
            $table->text('O');
            $table->text('P');
            $table->text('Q');
            $table->text('R');
            $table->text('S');
            $table->text('T');
            $table->text('U');
            $table->text('V');
            $table->text('W');
            $table->text('X');
            $table->text('Y');
            $table->text('Z');
            $table->timestamps();
        });
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
