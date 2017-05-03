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
	<div class="col-md-6">
		If present, the report will include the total amount written, broken down by marketing source<br/>
		<label>Source Column</label><br/>
		{{ Form::text('Source Column',(!empty($input['rules']) && isset($input['rules']['source']) ? $input['rules']['source'] : null),['name'=>'rules[source]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-12">
		If present, the report will include the total amount written, broken down by seminars<br/>
	</div>
	<div class="col-md-6">
		<label>Seminar Type Column</label><br/>
		{{ Form::text('Seminar Type Column',(!empty($input['rules']) && isset($input['rules']['seminar_type']) ? $input['rules']['seminar_type'] : null),['name'=>'rules[seminar_type]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-6">
		<label>Seminar Date Column</label><br/>
		{{ Form::text('Seminar Date Column',(!empty($input['rules']) && isset($input['rules']['seminar_date']) ? $input['rules']['seminar_date'] : null),['name'=>'rules[seminar_date]', 'class'=>'form-control'])}}
	</div>
</div>