<div class="row">
	<div class="col-md-6">
		<label>Spreadsheet</label><br/>
		{{ Form::select('Spreadsheet ID',$spreadsheets->pluck('name','id'),(!empty($input['rules']) && isset($input['rules']['spreadsheet']) ? $input['rules']['spreadsheet'] : null),['name'=>'rules[spreadsheet]', 'id'=>'spreadsheet', 'class'=>'form-control'])}}
		<br/>
		<label>Date Column</label><br/>
		{{ Form::text('Date Column',(!empty($input['rules']) && isset($input['rules']['date']) ? $input['rules']['date'] : null),['name'=>'rules[date]', 'class'=>'form-control'])}}
	</div>
	<div class="col-md-6">
		<label>Location Columns Format</label><br/>
		{{ Form::text('Location Format',(!empty($input['rules']) && isset($input['rules']['location']) ? $input['rules']['location'] : null),['name'=>'rules[location]', 'class'=>'form-control'])}}
		<small>Can be one column letter, if the address or city or state is in one column (e.g. 'A') or multiple columns to create a complete address (e.g. city, state, zip are different columns but need to be put together - C, S Z)</small>
	</div>
</div>
<br/><br/>
<div class="row">
	<div class="col-md-7">
		<label>Columns to Include</label><br/>
		<div class="small">
		uses a || (double pipe) delimiter<br/>
		<strong>letter or operation || format || label || totalling method || optional conditional</strong><br/>
		{{ Form::textarea('Columns to Include',(!empty($input['rules']) && isset($input['rules']['columns']) ? $input['rules']['columns'] : null),['name'=>'rules[columns]', 'class'=>'form-control', 'rows'=>12])}}
		</div>
	</div>
	<div class="col-md-5">
		<br/>
		<small>
		<strong>letter or operation:</strong> the column letter or a math operation for achievinng the value (e.g. A or A+B or A/D) - you may also use 'count' to use the total number of records in the dataset (e.g. A/count, or just 'count' if you want a column showing the total records count in the report)<br/>
		<strong>column format:</strong> numeric|dollar|integer|percent|string<br/>
		<strong>column label:</strong> the column header in the report table - defaults to the spreadsheet column label if no label is provided and just a column letter is provided<br/>
		<strong>totalling method:</strong> total|count|none - should the values for that column be a sum, an incrementing count of the number of records, or 'none' (as-is)<br/>
		<strong>optional conditional clause:</strong> only total or count if the column matches the conditional check (e.g. <em>= 0</em> or <em>&gt; 1000</em>)
		</small>
	</div>
</div>
<br/><br/>
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