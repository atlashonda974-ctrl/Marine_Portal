@extends('master')
@section('content')



<div class="content-body">

<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">

<div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
<div class="card">
<div class="card-header border-0 pb-0">
<h4 class="card-title">Collection Reports</h4>
<div class="col-sm-4">
</div>
</div>


<form class="form-horizontal" role="form" method="POST" action="{{ url('colReport') }}" id="my-form" autocomplete="off">
                        {!! csrf_field() !!}
<div class="row" style="margin-top:10px; margin-left:10px;">

<div class="col-sm-3">
<input name="from_date"  class="form-control" id="from_date" type="date" value="{{ $start_date }}" required>
</div>

<div class="col-sm-3">
<input name="to_date"  class="form-control" id="to_date" type="date" value="{{ $end_date }}" required>
</div>

 <div class="col-md-3 d-flex align-items-center">
                    <label for="status_filter" class="form-label me-2" style="white-space: nowrap; width: 100px;">Status</label>
                    <select name="status_filter" id="status_filter" class="form-control select2">
                        <option value="all" {{ request('status_filter', 'all') == 'all' ? 'selected' : '' }}>All</option>
                        <option value="outstanding" {{ request('status_filter') == 'outstanding' ? 'selected' : '' }}>Outstanding</option>
                    </select>
                </div>


<div class="col-sm-3">
<button type="submit" class="btn btn-primary" style="height:35px; width:150px; margin:10px;">Get Report</button>
</div>

</div>
</form>


<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered display" id="example">
<thead style="background: #003478 !important;"> 

        <tr>
            <th style="color:#FFFFFF !important;">Reference No</th>   <!-- 0 -->
            <th style="color:#FFFFFF !important;">Gross Premium</th>  <!-- 1 -->
            <th style="color:#FFFFFF !important;">Net Premium</th>    <!-- 2 -->
            <th style="color:#FFFFFF !important;" >Total SI</th>       <!-- 3 -->
            <th style="color:#FFFFFF !important;">Total Collection</th> <!-- 4 -->
            <th style="color:#FFFFFF !important;">Outstanding</th>    <!-- 5 -->
        </tr>
    </thead>
    <tbody>
        @foreach($data as $record)
            <tr>
                <td>{{ $record->GDH_DOC_REFERENCE_NO ?? 'N/A' }}</td>
                <td>{{ number_format($record->GDH_GROSSPREMIUM) ?? 0 }}</td>
                <td>{{ number_format($record->GDH_NETPREMIUM) ?? 0 }}</td>
                <td>{{ number_format($record->GDH_TOTALSI) ?? 0 }}</td>
                <td>{{ number_format($record->TOT_COL) ?? 0 }}</td>
                <td>{{ number_format(($record->GDH_GROSSPREMIUM ?? 0) - ($record->TOT_COL ?? 0)) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
</div>
</div>

</div>
</div>
</div>
</div>

@endsection