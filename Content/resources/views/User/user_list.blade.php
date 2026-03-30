@extends('master')
@section('content')



<div class="content-body">

<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">


<div class="col-xl-12 col-xxl-12 col-lg-12 col-md-12">
<div class="card">
<div class="card-header border-0 pb-0">
<center> <h4 class="card-title">User List</h4> </center>
<div class="col-sm-4">
<a href="{{ url('/createUser') }}"><input  class="btn waves-effect waves-light btn-ft btn-success"  type="button" value="Create User" style="margin-left:20px;"></a>
</div>
</div>






<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered display" id="example">
<thead style="background: #003478 !important;"> 
<tr>
<th style="color: white !important;width: 100px;"><strong>Sr#</strong></th>
<th style="color: white !important;"><strong>Name</strong></th>
<th style="color: white !important;"><strong>Dealer</strong></th>
<th style="color: white !important;"><strong>ABB</strong></th>
<th style="color: white !important;"><strong>Brand</strong></th>
<th style="color: white !important;"><strong>Contact Person</strong></th>
<th style="color: white !important;"><strong>Contact Number</strong></th>
<th style="color: white !important;"><strong>Status</strong></th>
<th style="color: white !important;"><strong>Action</strong></th>
</tr>
</thead>
<tbody>
@php $count = 1; @endphp
@foreach($userData as $row)
<tr>
<td style="color:#000000 !important;width: 100px;"> {{ $count }}</td>
<td style="color:#000000 !important; "> {{ $row->name }}</td> 
<td style="color:#000000 !important; "> {{ $row->dealer_code }}</td> 
<td style="color:#000000 !important; "> {{ $row->abb }}</td> 
<td style="color:#000000 !important; "> {{ $row->brand_name }}</td> 
<td style="color:#000000 !important; "> {{ $row->con_per }}</td> 
<td style="color:#000000 !important; "> {{ $row->con_no }}</td> 
@if($row->status == "Y")
<td style="color:#000000 !important;"> Active </td>  @else <td style="color:#000000 !important;"> In-Active </td>  
@endif
<td> 
  <!-- <a  href="{{ url('/productUpdate/' . $row->id) }}"><i class="fa fa-pencil" aria-hidden="true"></i> </a> -->
</td>
</tr>
@php $count++; @endphp
@endforeach
</tbody>
</tbody>
</table>
</div>
</div>
</div>
</div>
</div>
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
</script>

@endsection