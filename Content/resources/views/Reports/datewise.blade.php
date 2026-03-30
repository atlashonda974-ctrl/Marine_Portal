@extends('master')
@section('content')



<div class="content-body">

<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">

<div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
<div class="card">
<div class="card-header border-0 pb-0">
<h4 class="card-title">DateWise Reports</h4>
<div class="col-sm-4">
</div>
</div>


<form class="form-horizontal" role="form" method="POST" action="{{ url('datewiseRep') }}" id="my-form" autocomplete="off">
                        {!! csrf_field() !!}
<div class="row" style="margin-top:10px; margin-left:10px;">

<div class="col-sm-3">
<input name="from_date"  class="form-control" id="from_date" type="date" value="{{ $fromDate }}" required>
</div>

<div class="col-sm-3">
<input name="to_date"  class="form-control" id="to_date" type="date" value="{{ $toDate }}" required>
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

<th style="color:#FFFFFF !important;"><strong>Sr#</strong></th>
<th style="color:#FFFFFF !important;"><strong>Application Date</strong></th>
<th style="color:#FFFFFF !important;"><strong>MOP</strong></th> 
<th style="color:#FFFFFF !important;"><strong>Portal No</strong></th>
<th style="color:#FFFFFF !important;"><strong>Insured Name</strong></th>
<th style="color:#FFFFFF !important;"><strong>Voyage No</strong></th>
<th style="color:#FFFFFF !important;"><strong>LC Number</strong></th>
<th style="color:#FFFFFF !important;"><strong>Foreign Currency</strong></th>
<th style="color:#FFFFFF !important;"><strong>Sum Insured (Rs)</strong></th>
<th style="color:#FFFFFF !important;"><strong>Gross Pre</strong></th>
<th style="color:#FFFFFF !important;"><strong>NET pre</strong></th>
<th style="color:#FFFFFF !important;"><strong>GIS</strong></th>
</tr>
</thead>
<tbody>
@php $count = 1; @endphp
@foreach($polData as $row)
<tr>
<td style="color:#000000 !important;"> {{ $count }}</td>
<td style="color:#000000 !important;"> {{ \Carbon\Carbon::parse($row->created_at)->format('d/m/Y h:i a') }}</td> 
<td style="color:#000000 !important; "> {{ $row->mop }}</td> 
<td style="color:#000000 !important; "> {{ $row->portal_no }}</td> 
<td style="color:#000000 !important; width: 200px;"> {{ $row->insured }}</td> 
<td style="color:#000000 !important; "> {{ $row->voyage_no }}</td> 

<td style="color:#000000 !important; "> {{ $row->lc_no }}</td> 

<td style="color:#000000 !important; "> {{ number_format($row->si_fc) }}</td> 
<td style="color:#000000 !important; "> {{ number_format($row->si_rs) }}</td> 
<td style="color:#000000 !important; "> {{ number_format($row->gross_pre) }}</td> 
<td style="color:#000000 !important; "> {{ number_format($row->net_pre) }}</td> 

<td style="color:#000000 !important; "> {{ $row->gis_no }}</td> 
</tr>
@php $count++; @endphp
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