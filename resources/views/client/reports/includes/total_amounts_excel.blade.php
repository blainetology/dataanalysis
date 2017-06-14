<?php $content = ${'report_'.$report->id}; ?>
<?php $tdwidth = round(100/(count($content['all']['all']['cols'])+4)); ?>
<html>
	<h1 height="32" colspan="{{count($content['columns'])+1}}">{{$report->name}} Report</h1>

	<h2 height="32" colspan="{{count($content['columns'])+1}}">Total Amounts Report</h2>

    @include('client.reports.includes.'.$report->template->file.'_excel_partial',['data'=>$content['all'],'columns'=>$content['columns'],'header'=>'All Together','subheader'=>''])

	@foreach($content['sections'] as $sections)
		@foreach($sections['data'] as $name=>$section)
		    @include('client.reports.includes.'.$report->template->file.'_excel_partial',['data'=>$section,'columns'=>$content['columns'],'header'=>'By '.$sections['label'],'subheader'=>$name])
		@endforeach
	@endforeach

</html>
