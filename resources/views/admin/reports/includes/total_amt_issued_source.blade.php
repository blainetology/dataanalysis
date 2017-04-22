<div class="row">
	<div class="col-md-4">
		<label>Production Spreadsheet ID</label><br/>
		{{ Form::text('Spreadsheet ID',(!empty($input['rules']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Date Column</label><br/>
		{{ Form::text('Date Column',(!empty($input['rules']) ? $input['rules']['date'] : null),['name'=>'rules[date]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Month Column</label><br/>
		{{ Form::text('Month Column',(!empty($input['rules']) ? $input['rules']['month'] : null),['name'=>'rules[month]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-3">
		<label>Source Column</label><br/>
		{{ Form::text('Source Column',(!empty($input['rules']) ? $input['rules']['source'] : null),['name'=>'rules[source]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-3">
		<label>FIA Column</label><br/>
		{{ Form::text('FIA Column',(!empty($input['rules']) ? $input['rules']['fia'] : null),['name'=>'rules[fia]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-3">
		<label>AUM Column</label><br/>
		{{ Form::text('AUM Column',(!empty($input['rules']) ? $input['rules']['aum'] : null),['name'=>'rules[aum]', 'class'=>'form-control'])}}
	</div>
</div>
