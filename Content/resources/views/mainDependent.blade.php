@extends('master')
@section('content')
<div class="content-body">

<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">

<div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
<div class="card">
<div class="card-header border-0 pb-0">
<h4 class="card-title">Dependants</h4>
</div>

<div class="row">
<div class="col-sm-8"></div>
<div class="col-sm-4" >
<input class="form-control"  type="text" placeholder="Search" aria-label="Search" id="myInput" onkeyup="myFunction()" style="width:300px; margin:10px;">
</div>
<div>

</div>
</div>

<div class="card-body">
<div class="table-responsive">
<table class="table table-responsive-sm mb-0" id="myTable" >
<thead> 
<tr>
<th><strong>Sr#</strong></th>
<th><strong>Code</strong></th>
<th><strong>Name</strong></th>
<th><strong>Relation</strong></th>
<th><strong>DOB</strong></th>
<th><strong>Marital Status</strong></th>
<th><strong>Room Limit</strong></th>
<th><strong>Hospitilization</strong></th>
<th><strong>Normal Delivery</strong></th>
<th><strong>C-Section</strong></th>
<th><strong>Actions</strong></th>
</tr>
</thead>
<tbody>

@foreach($EmpData as $row)
<tr>
<td> 1 </td>
<td> {{ $row->HPI_EMP_CODE }} - 0</td>
<td> {{ $row->HPI_EMP_NAME }}</td>
<td> Self </td>
<td> {{ $row->HPI_EMP_DOB }}</td>
<td> {{ $row->HPI_MARITAL_STATUS }} </td>
<td> {{ $planDetail->froom }} </td>
<td> {{ $planDetail->fhosp }} </td>
@if($row->HPI_EMP_SEX == "F" && $row->HPI_MARITAL_STATUS == "M")
<td> {{ $planDetail->fnrmdelv }} </td>
<td> {{ $planDetail->fcsec }} </td>
@else
<td></td>
<td></td>
@endif


<td>
<a href="{{'../../Claimintimation/' . $row->HPI_EMP_CODE . '/' . $planDetail->fplan . '/0'}}" class="btn btn-primary shadow btn-xs sharp"><i class="fa fa-plus-circle"></i></a>
<a href="{{'../../claimDetail/' . $row->HPI_EMP_CODE . '/0'}}" class="btn btn-success shadow btn-xs sharp"><i class="fa fa-info-circle"></i></a>
</td>
</tr>
@endforeach

@php $count = 2; @endphp
@foreach($newsData as $row)
<tr>
<td> {{ $count }}</td>
<td> {{ $row->HPI_EMP_CODE }} - {{ $row->HFM_SRNO }}</td>
<td> {{ $row->HFM_NAME }}</td>
@php $years = \Carbon\Carbon::parse($row->HFM_DT_OF_BIRTH)->age; @endphp

@if( strcmp($row->PRM_DESC, "Son") == 0 && $years >= 28)
<td  style="color:#FF0000;"> {{ $row->PRM_DESC }}</td>
<td  style="color:#FF0000;"> {{ $row->HFM_DT_OF_BIRTH }}</td>
@else
<td> {{ $row->PRM_DESC }}</td>
<td> {{ $row->HFM_DT_OF_BIRTH }}</td>
@endif
<td> {{ $row->HFM_MARITAL_STATUS }}</td>
<td> {{ $planDetail->froom }} </td>
<td> {{ $planDetail->fhosp }} </td>
@if($row->PRM_DESC == "Wife")
<td> {{ $planDetail->fnrmdelv }} </td>
<td> {{ $planDetail->fcsec }} </td>
@else
<td></td>
<td></td>
@endif

@if( strcmp($row->PRM_DESC, "Son") == 0 && $years >= 28) @else
<td>
<a href="{{'../../Claimintimation/' . $row->HPI_EMP_CODE . '/' . $planDetail->fplan . '/' . $row->HFM_SRNO}}" class="btn btn-primary shadow btn-xs sharp"><i class="fa fa-plus-circle"></i></a>
<a href="{{'../../claimDetail/' . $row->HPI_EMP_CODE . '/' . $row->HFM_SRNO}}" class="btn btn-success shadow btn-xs sharp"><i class="fa fa-info-circle"></i></a>
</td>
@endif
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