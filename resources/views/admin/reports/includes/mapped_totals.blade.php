<div class="row">
	<div class="col-md-4">
		<label>Spreadsheet</label><br/>
		{{ Form::select('Spreadsheet ID',$spreadsheets->pluck('name','id'),(!empty($input['rules']) && isset($input['rules']['spreadsheet']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'id'=>'spreadsheet', 'class'=>'form-control'])}}
		<br/>
		<label>Date Column</label><br/>
		{{ Form::text('Date Column',(!empty($input['rules']) && isset($input['rules']['date']) ? $input['rules']['date'] : null),['name'=>'rules[date]', 'class'=>'form-control'])}}
		<br/>
		<label>Location Columns Format</label><br/>
		{{ Form::text('Location Format',(!empty($input['rules']) && isset($input['rules']['location']) ? $input['rules']['location'] : null),['name'=>'rules[location]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-8">
		<label>Columns to Include</label><br/>
		Enter as "col" or operation || numeric|dollar|integer|string || label || "yes" or "no" to totalling - one per line<br/>
		{{ Form::textarea('Columns to Include',(!empty($input['rules']) && isset($input['rules']['columns']) ? $input['rules']['columns'] : null),['name'=>'rules[columns]', 'class'=>'form-control', 'rows'=>8])}}
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