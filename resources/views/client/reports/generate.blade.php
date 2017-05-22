@extends('layouts.pdf')

@section('content')
<div class="container">
    @foreach($reports as $report)
    <div class="row pagebreak">
        <div class="col-md-12">
        <h3 class="text-info" style="margin-bottom:0; padding-bottom:0;">{{ $report->label }} Report</h3>
        </div>
        <div class="col-md-12">
        {{ $report->rules }}
        @include('client.reports.includes.'.$report->template->file)
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('styles')
<style>
.pagebreak{page-break-before: always;}
</style>
@append

@section('scripts')
@append