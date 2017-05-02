<div class="row">
	<div class="col-md-6">
		<label>Spreadsheet</label><br/>
		{{ Form::select('Spreadsheet ID',$spreadsheets->pluck('name','id'),(!empty($input['rules']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-4">
		<label>Seminar Name Column</label><br/>
		{{ Form::text('Seminar Column',(!empty($input['rules']) ? $input['rules']['seminar'] : null),['name'=>'rules[seminar]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Seminar Date Column</label><br/>
		{{ Form::text('Dat Column',(!empty($input['rules']) ? $input['rules']['date'] : null),['name'=>'rules[date]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Month Written Column</label><br/>
		{{ Form::text('Month Column',(!empty($input['rules']) ? $input['rules']['month'] : null),['name'=>'rules[month]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-4">
		<label>FIA Written Column</label><br/>
		{{ Form::text('FIA Column',(!empty($input['rules']) ? $input['rules']['fia'] : null),['name'=>'rules[fia]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>AUM Written Column</label><br/>
		{{ Form::text('AUM Column',(!empty($input['rules']) ? $input['rules']['aum'] : null),['name'=>'rules[aum]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Life Written Column</label><br/>
		{{ Form::text('Life Column',(!empty($input['rules']) ? $input['rules']['life'] : null),['name'=>'rules[life]', 'class'=>'form-control'])}}
	</div>
</div>
