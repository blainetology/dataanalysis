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
            $table->integer('template_id')->index();
            $table->string('name');
            $table->text('rules');
            $table->integer('active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
        \DB::update("ALTER TABLE reports AUTO_INCREMENT = 4000;");


        Schema::create('report_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('file');
            $table->integer('active')->default(1);
        });
        \DB::update("ALTER TABLE report_templates AUTO_INCREMENT = 1000;");


        \App\ReportTemplate::create(['name'=>'Number of People Who Have Set an Aptmt','file'=>'_people_set_aptmt']);
        \App\ReportTemplate::create(['name'=>'Total Amount Written','file'=>'_total_amt_written']);
        \App\ReportTemplate::create(['name'=>'Total Amount Pending','file'=>'_total_amt_pending']);

        \App\Report::create(['name'=>'Number of People Who Have Set an Aptmt','client_id'=>2000,'template_id'=>1000,'rules'=>'[]']);
        \App\Report::create(['name'=>'Total Amount Written','client_id'=>2000,'template_id'=>1001,'rules'=>'[]']);
        \App\Report::create(['name'=>'Total Amount Pending','client_id'=>2000,'template_id'=>1002,'rules'=>'[]']);
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
