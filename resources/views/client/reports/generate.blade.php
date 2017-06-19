@extends('layouts.pdf')

@section('content')
<div class="container">
    @if($report->template->pdf == 1)
        <div class="row pagebreak">
            <div class="col-md-12">
            @include('client.reports.includes.'.$report->template->file)
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
.pagebreak{page-break-before: always;}
html, body {
    font-family: 'Arial', Arial, sans-serif !important;
    font-size:9pt;
}
tbody td, tbody th{white-space: nowrap;}
</style>
@append

@section('scripts')
@append