@extends('master')
@section('content')

<?php

use Illuminate\Support\Facades\Session;
$intg_tag = Session::get('user')['intg_tag']; 
function hasPassed30Days() {
  $userDate = Session::get('user')['updated_at']; 
  if (is_null($userDate)) {
    return true;
  }else{
    $givenDate = new DateTime($userDate);
    $currentDate = new DateTime();
    $difference = $currentDate->diff($givenDate);
    return $difference->days >= 25 && $difference->invert == 1; // invert == 1 means the given date is in the past
  }
  
}
?>

@if(hasPassed30Days())
<script>
alert("Password expires in few days. Please Change your password.");
</script>
@endif


<div class="content-body">

<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">

<div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
<div class="card">
<div class="card-header border-0 pb-0">
<h4 class="card-title">Marine Certificates</h4>
<div class="col-sm-4">
<a href="{{ url('/addInsured') }}"><input  class="btn waves-effect waves-light btn-ft btn-success"  type="button" value="Create Certificate" style="margin-left:20px;"></a>
</div>
</div>


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
<th style="color:#FFFFFF !important;"><strong>Action</strong></th>
</tr>
</thead>
<tbody>
@php $count = 1; @endphp
@foreach($insuData as $row)
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
<td>
   <a  href="{{ url('/viewInsured/' . $row->id) }}" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> </a>
   <a  href="{{ url('/print/' . $row->id) }}" target="_blank"><i class="fa fa-print" aria-hidden="true"></i> </a>
 </td>

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


<script>
function myFunction() {
  var input, filter, table, tr, td, i, t;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 1; i < tr.length; i++) {
    var filtered = false;
    var tds = tr[i].getElementsByTagName("td");
    for(t=0; t<tds.length; t++) {
        var td = tds[t];
        if (td) {
          if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
            filtered = true;
          }
        }     
    }
    if(filtered===true) {
        tr[i].style.display = '';
    }
    else {
        tr[i].style.display = 'none';
    }
  }
}

//jquery

</script>

@endsection