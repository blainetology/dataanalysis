<div class="row">
	<div class="col-md-4">
		<label>Spreadsheet</label><br/>
		{{ Form::select('Spreadsheet ID',$spreadsheets->pluck('name','id'),(!empty($input['rules']) && isset($input['rules']['spreadsheet']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Date Column</label><br/>
		{{ Form::text('Date Column',(!empty($input['rules']) && isset($input['rules']['date']) ? $input['rules']['date'] : null),['name'=>'rules[date]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-8">
		<label>Columns to Total</label><br/>
		{{ Form::text('Columns to Total',(!empty($input['rules']) && isset($input['rules']['columns']) ? $input['rules']['columns'] : null),['name'=>'rules[columns]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-12">
		If present, only the spreadsheet content that meets the following conditional check will be included in the totaled report
	</div>
	<div class="col-md-4">
		<label>Column</label><br/>
		{{ Form::text('Conditional Column',(!empty($input['rules']) && isset($input['rules']['conditional']) ? $input['rules']['conditional'] : null),['name'=>'rules[conditional]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-2">
		<label>Operator</label><br/>
		{{ Form::select('Conditional Operator',["="=>"equals","!="=>"not equals","<"=>"less than",">"=>"greater than","<="=>"less than or equal to",">="=>"greater than or equal to"],(!empty($input['rules']) && isset($input['rules']['operator']) ? $input['rules']['operator'] : null),['name'=>'rules[operator]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Value</label><br/>
		{{ Form::text('Conditional Value',(!empty($input['rules']) && isset($input['rules']['value']) ? $input['rules']['value'] : null),['name'=>'rules[value]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-8">
		If present, the report will include the total amounts, broken down by each column listed<br/>
		<label>Section Columns</label><br/>
		{{ Form::text('Advisor Column',(!empty($input['rules']) && isset($input['rules']['sections']) ? $input['rules']['sections'] : null),['name'=>'rules[sections]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-6">
		If set, the report will include the total amounts, broken down by months<br/>
		<label>Show Months</label><br/>
		{{ Form::select('Show Months',['yes'=>"show",'no'=>"don't show"],(!empty($input['rules']) && isset($input['rules']['month']) ? $input['rules']['month'] : null),['name'=>'rules[month]', 'class'=>'form-control'])}}<br/>
		{{ Form::select('Show Months in Sections',['yes'=>"show months in sections",'no'=>'hide months in sections'],(!empty($input['rules']) && isset($input['rules']['monthsections']) ? $input['rules']['monthsections'] : null),['name'=>'rules[monthsections]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-6">
		If set, the report will include the total amounts, broken down by weeks<br/>
		<label>Show Weeks</label><br/>
		{{ Form::select('Show Weeks',['none'=>"don't show",'sun'=>'yes, week begins on sunday','mon'=>'yes, week begins on monday'],(!empty($input['rules']) && isset($input['rules']['week']) ? $input['rules']['week'] : null),['name'=>'rules[week]', 'class'=>'form-control'])}}<br/>
		{{ Form::select('Show Weeks in Sections',['yes'=>"show weeks in sections",'no'=>'hide weeks in sections'],(!empty($input['rules']) && isset($input['rules']['weeksections']) ? $input['rules']['weeksections'] : null),['name'=>'rules[weeksections]', 'class'=>'form-control'])}}
	</div>
</div>
