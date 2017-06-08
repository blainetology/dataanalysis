@extends('layouts.pdf')

@section('content')
<div class="container">
    @foreach($reports as $report)
        @if($report->template->pdf == 1)
            <div class="row pagebreak">
                <div class="col-md-12">
                <h3 class="text-info" style="margin-bottom:0; padding-bottom:0;">{{ $report->label }} Report</h3>
                </div>
                <div class="col-md-12">
                @include('client.reports.includes.'.$report->template->file)
                </div>
            </div>
        @endif
    @endforeach
</div>
@endsection

@section('styles')
<style>
.pagebreak{page-break-before: always;}
html, body {
    font-family: 'Arial', Arial, sans-serif !important;
    font-size:10pt;
}
tbody td, tbody th{white-space: nowrap;}
</style>
@append

@section('scripts')
@append