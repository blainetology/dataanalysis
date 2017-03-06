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
            $table->text('conditional')->nullable();
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
        foreach($labels as $index=>$column){
            if(!isset($column[2]))
                $column[2]=[];
            \App\SpreadsheetColumn::create(['spreadsheet_id'=>3000,'column'=>($index+1),'label'=>$column[0],'type'=>$column[1],'validation'=>json_encode($column[2])]);
        }

        $labels = [
            ["Date of contact","date"],
            ["Month of contact","text",["in"=>"January,February,March,April,May,June,July,August,September,October,November,December"]],
            ["Last Name","text"],
            ["First Name","text"],
            ["Marketing Source","text",["in"=>"radio,referral,personal,style ad,lunch n learn,college course"]],
            ["Zip Code","numeric"],
            ["Seminar Date","date"],
            ["Seminar Type","text",["in"=>"Retirement Today,401(k) and IRA Workshop,Retire Well"]],
            ["Set 1st Appointment","text",["in"=>"yes,no"]],
            ["Kept 1st Appointment","text",["in"=>"yes,no"]],
            ["Qualified","text",["in"=>"yes,no"]],
            ["Advisor","text"],
            ["SKQ","text",["in"=>"yes,no"]],
            ["Set 2nd","text",["in"=>"yes,no"]],
            ["Kept 2nd","text",["in"=>"yes,no"]],
            ["Set 3rd","text",["in"=>"yes,no"]],
            ["Kept 3rd","text",["in"=>"yes,no"]],
            ["Became Client","text",["in"=>"yes,no","not yet"]],
            ["Status","text"],
            ["Notes","notes"]
        ];
        foreach($labels as $index=>$column){
            if(!isset($column[2]))
                $column[2]=[];
            if(!isset($column[3]))
                $column[3]=[];
            \App\SpreadsheetColumn::create(['spreadsheet_id'=>3001,'column'=>($index+1),'label'=>$column[0],'type'=>$column[1],'validation'=>json_encode($column[2]),'conditional'=>json_encode($column[3])]);
        }

        $labels = [
            ["Last Name","text"],
            ["First Name","text"],
            ["Marketing Source","text",["in"=>"radio,referral,personal,style ad,lunch n learn,college course"]],
            ["Seminar Date","date"],
            ["Seminar Type","text",["in"=>"Retirement Today,401(k) and IRA Workshop,Retire Well"]],
            ["Writing Advisor","text"],
            ["Date Written","date"],
            ["Month Written","text",["in"=>"January,February,March,April,May,June,July,August,September,October,November,December"]],
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
        foreach($labels as $index=>$column){
            if(!isset($column[2]))
                $column[2]=[];
            \App\SpreadsheetColumn::create(['spreadsheet_id'=>3002,'column'=>($index+1),'label'=>$column[0],'type'=>$column[1],'validation'=>json_encode($column[2])]);
        }
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
