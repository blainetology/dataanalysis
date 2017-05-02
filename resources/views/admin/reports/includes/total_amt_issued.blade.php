<div class="row">
	<div class="col-md-4">
		<label>Spreadsheet</label><br/>
		{{ Form::select('Spreadsheet ID',$spreadsheets->pluck('name','id'),(!empty($input['rules']) && isset($input['rules']['spreadsheet']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Date Written Column</label><br/>
		{{ Form::text('Date Column',(!empty($input['rules']) && isset($input['rules']['date']) ? $input['rules']['date'] : null),['name'=>'rules[date]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>Month Written Column</label><br/>
		{{ Form::text('Month Column',(!empty($input['rules']) && isset($input['rules']['month']) ? $input['rules']['month'] : null),['name'=>'rules[month]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-4">
		<label>Marketing Source Column</label><br/>
		{{ Form::text('Source Column',(!empty($input['rules']) && isset($input['rules']['source']) ? $input['rules']['source'] : null),['name'=>'rules[source]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>FIA Issued Column</label><br/>
		{{ Form::text('FIA Column',(!empty($input['rules']) && isset($input['rules']['fia']) ? $input['rules']['fia'] : null),['name'=>'rules[fia]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-4">
		<label>AUM Invested Column</label><br/>
		{{ Form::text('AUM Column',(!empty($input['rules']) && isset($input['rules']['aum']) ? $input['rules']['aum'] : null),['name'=>'rules[aum]', 'class'=>'form-control'])}}
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
