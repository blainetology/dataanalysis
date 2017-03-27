<div class="row">
	<div class="col-md-4">
		<label>Spreadsheet ID</label><br/>
		{{ Form::text('spreadsheet ID',(!empty($input['rules']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Set Column</label><br/>
		{{ Form::text('Set Column',(!empty($input['rules']) ? $input['rules']['set'] : null),['name'=>'rules[set]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Kept Column</label><br/>
		{{ Form::text('Kept Column',(!empty($input['rules']) ? $input['rules']['kept'] : null),['name'=>'rules[kept]', 'class'=>'form-control'])}}
	</div>
</div>
