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
            $table->string('type')->default('text');
            $table->text('validation')->nullable();
            $table->timestamps();
        });
        \DB::update("ALTER TABLE spreadsheet_columns AUTO_INCREMENT = 5000;");
        $labels = [
            ["Mailing Cost","currency"], 
            ["Number of Mailers","numeric"], 
            ["Seminar Dates","date"], 
            ["Seminar Location","text"], 
            ["Seminar Type","text"], 
            ["Buying Units Responded","numeric"],
            ["# of Referral Units Attended","numeric"],
            ["Buying Units Attended","numeric"],
            ["Buying units interested in coming in","numeric"],
            ["Buying Units Scheduled","numeric"],
            ["Names of Units who Scheduled","text"]
        ];
        foreach($labels as $index=>$column)
          \App\SpreadsheetColumn::create(['spreadsheet_id'=>3000,'column'=>($index+1),'label'=>$column[0],'type'=>$column[1]]);

        $labels = [
            ["Date of contact","date"],
            ["Month of contact","text"],
            ["Last Name","text"],
            ["First Name","text"],
            ["Marketing Source","text"],
            ["Zip Code","numeric"],
            ["Seminar Date","date"],
            ["Seminar Type","text"],
            ["Set 1st Appointment","text"],
            ["Kept 1st Appointment","text"],
            ["Qualified","text"],
            ["Advisor","text"],
            ["SKQ","text"],
            ["Set 2nd","text"],
            ["Kept 2nd","text"],
            ["Set 3rd","text"],
            ["Kept 3rd","text"],
            ["Became Client","text"],
            ["Status","text"],
            ["Notes","notes"]
        ];
        foreach($labels as $index=>$column)
          \App\SpreadsheetColumn::create(['spreadsheet_id'=>3001,'column'=>($index+1),'label'=>$column[0],'type'=>$column[1]]);

        $labels = [
            ["Last Name","text"],
            ["First Name","text"],
            ["Marketing Source","text"],
            ["Seminar Date","date"],
            ["Seminar Type","text"],
            ["Writing Advisor","text"],
            ["Date Written","date"],
            ["Month Written","text"],
            ["FIA Business Written","currency"],
            ["AUM Business Written","currency"],
            ["Life written","currency"],
            ["Type of Life","text"],
            ["FIA business issue date","date"],
            ["FIA Business Amount Issued","currency"],
            ["AUM invested Date","date"],
            ["AUM Amount invested","currency"],
            ["AUM Amount not invested","currency"]
        ];
        foreach($labels as $index=>$column)
          \App\SpreadsheetColumn::create(['spreadsheet_id'=>3002,'column'=>($index+1),'label'=>$column[0],'type'=>$column[1]]);
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
