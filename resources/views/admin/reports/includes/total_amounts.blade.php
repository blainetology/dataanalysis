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
	<div class="col-md-8">
		If present, the report will include the total amounts, broken down by each column listed<br/>
		<label>Section Columns</label><br/>
		{{ Form::text('Advisor Column',(!empty($input['rules']) && isset($input['rules']['sections']) ? $input['rules']['sections'] : null),['name'=>'rules[sections]', 'class'=>'form-control'])}}
	</div>
</div>
<br/>
<div class="row">
	<div class="col-md-8">
		If set, the report will include the total amounts, broken down by weeks<br/>
		<label>Week Begins</label><br/>
		{{ Form::select('Week Begins',['none'=>"don't show",'sun'=>'sunday','mon'=>'monday'],(!empty($input['rules']) && isset($input['rules']['week']) ? $input['rules']['week'] : null),['name'=>'rules[week]', 'class'=>'form-control'])}}
	</div>
</div>
