<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('client_id')->index();
            $table->string('name');
            $table->integer('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        \DB::update("ALTER TABLE reports AUTO_INCREMENT = 4000;");

        \App\Report::create(['name'=>'Number of People Who Have Set an Aptmt','client_id'=>2000]);
        \App\Report::create(['name'=>'Total Amount Written','client_id'=>2000]);
        \App\Report::create(['name'=>'Total Amount Pending','client_id'=>2000]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reports');
    }
}
