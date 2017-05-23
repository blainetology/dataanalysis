<div style="position:fixed; top:50px; left:0; width:200px; height: 100%; padding:20px; box-sizing: border-box;" class="bg-info">
    <div class="row">
        <div class="col-md-12">
            <h4>Spreadsheets</h4>
            @foreach($client_spreadsheets as $rep)
            <a href="/client/spreadsheets/{{ $rep->id }}/edit">{{$rep->name}}</a><br/>
            @endforeach
            <br/>
            <h4>Reports</h4>
            @foreach($client_reports as $rep)
            <a href="/reports/{{ $rep->id }}?{{ $_SERVER['QUERY_STRING'] }}">{{$rep->label}}</a><br/>
            @endforeach
            <br/>
            <a href="/reports/generate/{{$rep->client_id}}?{{ $_SERVER['QUERY_STRING'] }}" class="btn btn-info btn-sm"><i class="fa fa-download" aria-hidden="true"></i> generate pdf</a>
            <br/><br/>
        </div>
    </div>
</div>
