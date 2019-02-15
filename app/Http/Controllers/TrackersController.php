<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;   
use App\Log;   
use App\Tracker;
use App\Report;
use App\Spreadsheet;   
use App\SpreadsheetColumn;   
use App\SpreadsheetContent;   

class TrackersController extends Controller
{

    public function index()
    {
        if(!\Auth::user()->isEditor())
            abort(401);

        $data = [
            'trackers' => Tracker::all(),
            'spreadsheets' => Spreadsheet::all(),
            'isAdminView'   => true
        ];
        return view('admin.trackers.index',$data);
    }

    public function create()
    {
        $input = \Request::old();
        $input['rules'] = [];
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::withTrashed()->get()->pluck('business_name','id')->toArray(),
            'isAdminView'   => true
        ];
        return view('admin.trackers.create',$data);
    }

    public function store(Request $request)
    {
        $input = \Request::all();
        $input['rules'] = '[]';
        $input['opened_at'] = \DB::raw('NOW()');
        $input['data'] = json_encode([]);
        $tracker = Tracker::create($input);
        Log::logtracker($tracker->id,'created');
        return redirect()->route('trackers.edit',$tracker->id);
    }

    public function show($id)
    {
        //
        $tracker = Tracker::find($id);
        if(!$tracker)
            die('no tracker');
        $rules = json_decode($tracker->rules,true);
        $sheet_content = [];
        $sheet_cols = [];
        $sections = [];
        // iterate over the section rules
        foreach($rules as $section_id=>$rule){
            // only check a section if the columns field has data
            if(!empty($rule['columns'])){
                // assign the section label
                $sections[$section_id]['label'] = $rule['label'];
                // split the columns field into individual rows for parsing
                $columns = explode("\n",$rule['columns']);
                $col_labels = [];
                $col_indices = [];
                $col_map = [];
                $col_types = [];
                // get all the column labels 
                foreach($columns as $col_index=>$column){
                    $row = explode("||",$column);
                    foreach($row as $row_index=>$field)
                        $row[$row_index]=trim($field);
                    list($col_spreadsheet,$col_sorting,$col_date,$col_index,$col_equation,$col_format,$col_label,$col_method,$col_conditional) = $row;
                    $col_labels[]=$row;
                    $col_map[$col_label]=$col_index;
                    $col_indices[strlen($col_index)][]=$col_index;
                    $col_types[$col_index]=$col_format;
                }
                krsort($col_indices);
                $temp = [];
                foreach($col_indices as $cols){
                    rsort($cols);
                    foreach($cols as $col)
                        $temp[]=$col;
                }
                $col_indices=$temp;

                #$sections[$section_id]['col_labels']=$col_labels;
                $sections[$section_id]['col_indices']=$col_indices;
                $sections[$section_id]['col_map']=$col_map;
                $sections[$section_id]['col_types']=$col_types;
                // parse the columns
                foreach($columns as $col_index=>$column){
                    // split the column row into the various rules
                    $row = explode("||",$column);
                    // trim each column rules
                    foreach($row as $row_index=>$field)
                        $row[$row_index]=trim($field);
                    list($col_spreadsheet,$col_sorting,$col_date,$col_index,$col_equation,$col_format,$col_label,$col_method,$col_conditional) = $row;
                    // only proceed if the first 7 required column rules are set
                    if(isset($col_conditional)){
                        // get the column count for each spreadsheet
                        if(!isset($sheet_cols[$col_spreadsheet]))
                            $sheet_cols[$col_spreadsheet] = SpreadsheetColumn::where('spreadsheet_id',$col_spreadsheet)->max('column_id');
                        // get the sheet content, limited to just the fields that are needed, based on the column count above
                        if(!isset($sheet_content[$col_spreadsheet])){
                            $query = SpreadsheetContent::where('spreadsheet_id',$col_spreadsheet);
                            for($x=1;$x<=$sheet_cols[$col_spreadsheet];$x++)
                                $query->addSelect('col'.$x);
                            $sheet_content[$col_spreadsheet] = $query->get()->toArray();
                        }
                        // get the column that the section is being sorted by
                        $sort_col = 'col'.array_search($col_sorting, \App\SpreadsheetColumn::$columnLetters);  
                        // get the column that the date range is being compared to 
                        $date_col = 'col'.array_search($col_date, \App\SpreadsheetColumn::$columnLetters);   
                        // iterate over the set spreadsheet for each column and parse the needed content
                        foreach($sheet_content[$col_spreadsheet] as $content){
                            if($content[$date_col] >= \Request::get('start_date',date('Y-01-01')) && $content[$date_col] <= \Request::get('end_date',date('Y-m-d'))){
                            #if(1==1){
                                $sort_label = Tracker::lettersToLabel($col_sorting,$content,$col_indices);
                                if( !isset($sections[$section_id]) || !isset($sections[$section_id]['all']) ){
                                    foreach($col_labels as $a_label){
                                        $index = $a_label[3];
                                        if(!isset($sections[$section_id]['all']['cols'][$index]))
                                            $sections[$section_id]['all']['cols'][$index]=['count'=>0,'rules'=>$a_label,'value'=>0];
                                    }
                                }

                                if( !isset($sections[$section_id]) || !isset($sections[$section_id]['rows']) || !isset($sections[$section_id]['rows'][$sort_label]) ){
                                    foreach($col_labels as $a_label){
                                        $index = $a_label[3];
                                        if(!isset($sections[$section_id]['rows'][$sort_label]['cols'][$index]))
                                            $sections[$section_id]['rows'][$sort_label]['cols'][$index]=['count'=>0,'rules'=>$a_label,'value'=>0];
                                    }
                                }

                                $index = $col_index;

                                // check if this is a spreadsheet column, and not an equations - equations get processes later
                                $is_col = array_search($col_equation, \App\SpreadsheetColumn::$columnLetters);
                                if($is_col || strtolower($col_equation)=='count'){
                                    $value_col = 'col'.$is_col;
                                    $do_total = true;
                                    if(!empty($col_conditional)){
                                        $conditionals = explode('&', $col_conditional);
                                        foreach($conditionals as $conditional){                                    
                                            $ifs = explode(' ',trim($conditional));
                                            $if_col = array_search($ifs[0], \App\SpreadsheetColumn::$columnLetters);
                                            if($if_col){
                                                $if_col = 'col'.$if_col;   
                                                $ifs[2] = str_replace(['start_date','end_date'], [\Request::get('start_date',date('Y-01-01')),\Request::get('end_date',date('Y-m-d'))], $ifs[2]);
                                                if(strstr($ifs[2], '"'))
                                                    $ifs[0] = '"'.$content[$if_col].'"';
                                                else
                                                    $ifs[0] = $content[$if_col];
                                                if(!Tracker::compareStrings($ifs[0],$ifs[2],$ifs[1])){
                                                    $do_total=false;
                                                }
                                            }
                                        }
                                    }
                                    if($do_total){
                                        if($col_method=='total'){
                                            $sections[$section_id]['rows'][$sort_label]['cols'][$index]['value']+=$content[$value_col];
                                            $sections[$section_id]['all']['cols'][$index]['value']+=$content[$value_col];
                                        }
                                        else if($col_method=='count'){
                                            $sections[$section_id]['rows'][$sort_label]['cols'][$index]['value']++;
                                            $sections[$section_id]['all']['cols'][$index]['value']++;
                                        }
                                    }
                                }
                                $sections[$section_id]['rows'][$sort_label]['cols'][$index]['count']++;
                                $sections[$section_id]['all']['cols'][$index]['count']++;
                            }
                        }
                    }
                }
            }
        }
        foreach($sections as $section_id=>$section){
            foreach($sheet_content as $sheet=>$content){
                $sections[$section_id]['all']['count'][$sheet]=count($content);
                if(!empty($sections[$section_id]['all']['cols'])){
                    // setting the value for columns that are equations
                    foreach($sections[$section_id]['all']['cols'] as $col_index=>$col){
                        $is_col = array_search($col['rules'][4], \App\SpreadsheetColumn::$columnLetters);
                        if(!$is_col && strtolower($col['rules'][4])!='count'){
                            $index = $col['rules'][3];
                            $sections[$section_id]['all']['cols'][$index]['value']=\App\Tracker::lettersToValues($col,$sections[$section_id]['all']['cols'],$section['col_indices']);
                        }
                    }
                }
            }
            if(!empty($section['rows'])){
                foreach($section['rows'] as $sort_label=>$columns){
                    // setting the value for columns that are equations
                    foreach($columns['cols'] as $col_index=>$col){
                        $is_col = array_search($col['rules'][4], \App\SpreadsheetColumn::$columnLetters);
                        if(!$is_col && strtolower($col['rules'][4])!='count'){
                            $index = $col['rules'][3];
                            $sections[$section_id]['rows'][$sort_label]['cols'][$index]['value']=\App\Tracker::lettersToValues($col,$sections[$section_id]['rows'][$sort_label]['cols'],$section['col_indices']);
                        }
                    }
                }
            }
        }
        $tracker->update(['data'=>json_encode($sections)]);

        $data = [
            'sections' => $sections,
            'client' => Client::find($tracker->client_id),
            'tracker' => $tracker,
            'client_trackers' => Tracker::where('client_id',$tracker->client_id)->active()->orderBy('list_order','asc')->get(),
            'client_reports' => Report::where('client_id',$tracker->client_id)->active()->orderBy('list_order','asc')->get(),
            'client_spreadsheets' => Spreadsheet::where('client_id',$tracker->client_id)->active()->orderBy('list_order','asc')->get(),
            'start' => \Request::get('start_date',date('Y-01-01')),
            'end' => \Request::get('end_date',date('Y-m-d'))
        ];

        if(\Request::get('excel')){
            \Excel::create(str_slug($tracker->name.'  '.$data['start'].' thru '.$data['end'],'_'), function($excel) use ($data) {
                $data['tracker_name'] = $data['tracker']->name;
                $data['tracker_dates'] = $data['start'].' thru '.$data['end'];
                $data['col_format'] = $this->setColumnFormats($data['sections']);

                $excel->sheet('Tracker', function($sheet) use ($data) {
                    $sheet->setColumnFormat($data['col_format']);
                    $sheet->loadView('client.trackers.excel',$data);
                    $row=4;
                    foreach($data['sections'] as $section){
                        $count=count($section['rows'])+1;
                        for($x=1; $x<=(count($section['col_indices'])+1); $x++){
                            $letter = \App\SpreadsheetColumn::$columnLetters[$x];
                            for($y=$row; $y<=$row+$count; $y++){
                                #echo $letter.$y."\n";
                                $sheet->cells($letter.$y, function($cells) {
                                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                            }
                        }
                        $row+=($count+2);
                    }
                });

                $excel->setActiveSheetIndex(0);
            })->export('xls');
        }

        $tracker->opened_at = \DB::raw('CURRENT_TIMESTAMP');
        $tracker->save();
        Log::logtracker($tracker->id,'viewed');
        return view('client.trackers.show',$data);
    }

    public function edit($id)
    {
        //
        $tracker = Tracker::find($id);
        $input = $tracker->toArray();
        $input['rules'] = json_decode($tracker->rules,true);
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::withTrashed()->get()->pluck('business_name','id')->toArray(),
            'spreadsheets' => Spreadsheet::with('columns')->where('client_id',$tracker->client_id)->get(),
            'isAdminView'   => true
        ];
        return view('admin.trackers.create',$data);
    }

    public function update(Request $request, $id)
    {
        $input = \Request::all();

        if(!empty($input['rules'])){
            // clean up the columns input
            foreach($input['rules'] as $section_index => $rules){
                $temp = [];
                if(!empty($rules['columns'])){
                    foreach(explode("\n",trim($rules['columns'])) as $column){
                        if(!empty(trim($column))){
                            $row = explode('||',$column);

                            $sheet = !empty($row[0]) ? trim($row[0]) : 0;
                            $availableColumns = \App\SpreadsheetColumn::where('spreadsheet_id',$sheet)->pluck('label','column_id');
                            $sorting = !empty($row[1]) ? trim($row[1]) : '';
                            $date = !empty($row[2]) ? trim($row[2]) : '';
                            $index = !empty($row[3]) ? trim($row[3]) : '';
                            $equation = !empty($row[4]) ? trim($row[4]) : '';
                            $format = !empty($row[5]) ? trim($row[5]) : 'numeric';
                            if(isset($row[6]) && !empty(trim($row[6])))
                                $label = trim($row[6]);
                            elseif($aindex = array_search($equation, \App\SpreadsheetColumn::$columnLetters) && isset($availableColumns[array_search($equation, \App\SpreadsheetColumn::$columnLetters)]) )
                                $label = $availableColumns[array_search($equation, \App\SpreadsheetColumn::$columnLetters)];
                            else
                                $label = $index;
                            $total = (!isset($row[7]) || (isset($row[7]) && strtoupper(trim($row[7])) != 'NONE' && strtoupper(trim($row[7])) != 'COUNT')) ? 'total' : strtolower(trim($row[7]));

                            $if = '';
                            if(!empty($row[8])){
                                $row[8] = str_replace(['!=','==','>=','<=','=','<','>','&'], [' != ',' == ',' >= ',' <= ',' = ',' < ',' > ',' & '], $row[8]);
                                $row[8] = str_replace(['    ','   ','  '], ' ', $row[8]);
                                $row[8] = str_replace(['! =','= =','> =','< =','& &'], ['!=','==','>=','<=', '&'], $row[8]);
                                $row[8] = str_replace(['    ','   ','  '], ' ', $row[8]);
                                $row[8] = str_replace(['==','&&'], ['=','&'], $row[8]);
                                $conditionals = explode('&', $row[8]);
                                foreach ($conditionals as $conditional){
                                    $ifs = explode(' ',trim($conditional));
                                    if(isset($ifs[0]) && isset($ifs[1])){
                                        if(!isset($ifs[2])){
                                            $ifs[2]=$ifs[1];
                                            $ifs[1]=$ifs[0];
                                            $ifs[0]=trim($row[3]);
                                        }
                                        $ifs[0] = trim($ifs[0]);
                                        $ifs[1] = trim($ifs[1]);
                                        $ifs[2] = trim($ifs[2]);
                                        $ifs[2] = trim($ifs[2],'"\'');
                                        #echo $ifs[1]." ".(float)$ifs[1]."<br/>";
                                        if($ifs[2] === (string)(float)$ifs[2]){
                                            #echo 'matched'."<br/>";
                                            $ifs[2] = (float)$ifs[2];
                                        }
                                        else{
                                            #echo 'no match'."<br/>";
                                            $ifs[2] = '"'.$ifs[2].'"';
                                        }
                                        if(!empty($if))
                                            $if .= ' & ';
                                        $if .= $ifs[0]." ".$ifs[1]." ".$ifs[2];
                                        #echo $if."<br/>";                   
                                    }
                                }
                            }

                            $temp[] = trim(implode(' || ',[$sheet,$sorting,$date,$index,$equation,$format,$label,$total,$if]));
                        }
                    }
                    $input['rules'][$section_index]['columns'] = implode("\n",$temp);
                }
            }
            $input['rules'] = json_encode($input['rules']);
        }
        $tracker = Tracker::find($id);
        $tracker->update($input);
        Log::logtracker($tracker->id,'updated');
        return redirect()->route('trackers.index');
    }

    public function destroy($id)
    {
        //
        $tracker = Tracker::find($id);
        $tracker->delete();
        Log::logtracker($tracker->id,'deleted');
        return redirect()->route('trackers.index');
    }

    public function duplicate($id)
    {
        $tracker = Tracker::find($id);
        if($tracker){
            $input = $tracker->toArray();
            $input['name'] .= ' (copy)';
            $newtracker = Tracker::create($input);
            Log::logtracker($newtracker->id,'created');
            return redirect()->route('trackers.edit',$newtracker->id);
        }
        return redirect()->route('trackers.index');
    }


    private function setColumnFormats($sections){
        $row=5;
        $cols = [];
        foreach($sections as $section){
            $x=2;
            foreach($section['col_types'] as $index=>$format){
                $start=$row;
                $end=$start+count($section['rows']);
                $letter = \App\SpreadsheetColumn::$columnLetters[$x];
                $label=$letter.$start.':'.$letter.$end;
                if($format=='integer')
                    $cols[$label] = '0';
                else if($format=='dollar')
                    $cols[$label] = '$#,##0.00';
                else if($format=='numeric')
                    $cols[$label] = '#,##0.00';
                else if($format=='percent')
                    $cols[$label] = '0.0%';
                else
                    $cols[$label] = 'General';

                $x++;
            }
            $row=$end+3;
        }
        #print_r($cols);
        #exit;
        return $cols;

    }

}
