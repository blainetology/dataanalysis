<div class="row">
	<div class="col-md-4">
		<label>Production Spreadsheet ID</label><br/>
		{{ Form::text('Spreadsheet ID',(!empty($input['rules']) && isset($input['rules']['spreadsheet']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Date Column</label><br/>
		{{ Form::text('Dat Column',(!empty($input['rules']) && isset($input['rules']['date']) ? $input['rules']['date'] : null),['name'=>'rules[date]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-3">
		<label>Written Column</label><br/>
		{{ Form::text('Advisor Column',(!empty($input['rules']) && isset($input['rules']['written']) ? $input['rules']['written'] : null),['name'=>'rules[written]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-3">
		<label>Conditional Column</label><br/>
		{{ Form::text('Conditional Column',(!empty($input['rules']) && isset($input['rules']['conditional']) ? $input['rules']['conditional'] : null),['name'=>'rules[conditional]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-3">
		<label>Conditional Value</label><br/>
		{{ Form::text('Conditional Value',(!empty($input['rules']) && isset($input['rules']['value']) ? $input['rules']['value'] : null),['name'=>'rules[value]', 'class'=>'form-control'])}}
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
		If set, the report will include the total amount written, broken down by weeks<br/>
		<label>Week Begins</label><br/>
		{{ Form::select('Week Begins',['none'=>"don't show",'sun'=>'sunday','mon'=>'monday'],(!empty($input['rules']) && isset($input['rules']['week']) ? $input['rules']['week'] : null),['name'=>'rules[week]', 'class'=>'form-control'])}}
	</div>
</div>
