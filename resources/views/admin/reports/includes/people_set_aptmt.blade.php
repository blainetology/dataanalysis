<div class="row">
	<div class="col-md-6">
		<label>Spreadsheet</label><br/>
		{{ Form::select('Spreadsheet ID',$spreadsheets->pluck('name','id'),(!empty($input['rules']) && isset($input['rules']['spreadsheet']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-6">
		<label>Date of Contact Column</label><br/>
		{{ Form::text('Date Column',(!empty($input['rules']) && isset($input['rules']['date']) ? $input['rules']['date'] : null),['name'=>'rules[date]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-6">
		<label>Set 1st Column</label><br/>
		{{ Form::text('Set Column',(!empty($input['rules']) && isset($input['rules']['set']) ? $input['rules']['set'] : null),['name'=>'rules[set]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-6">
		<label>Kept 1st Column</label><br/>
		{{ Form::text('Kept Column',(!empty($input['rules']) && isset($input['rules']['kept']) ? $input['rules']['kept'] : null),['name'=>'rules[kept]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-6">
		If present, the report will include the total amount written, broken down by advisor<br/>
		<label>Advisor Column</label><br/>
		{{ Form::text('Advisor Column',(!empty($input['rules']) && isset($input['rules']['advisor']) ? $input['rules']['advisor'] : null),['name'=>'rules[advisor]', 'class'=>'form-control'])}}
	</div>
</div>

