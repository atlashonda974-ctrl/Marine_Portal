@extends('master')
@section('content')


<div class="content-body">
<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">


<div class="card">
<div class="card-header">
<h4 class="card-title">Quotation</h4>
</div>
<div class="card-body">
<div class="basic-form">
<form class="form-horizontal" role="form" method="POST" id="my-form" action="{{ url('/vehicleUpdate/' . $vehicle->id) }}" id="my-form" autocomplete="off" enctype="multipart/form-data">
                        {!! csrf_field() !!}


<div class="form-row">
<div class="form-group col-md-4">
<label>Vehicle Name</label>
<input type="text" class="form-control" placeholder="Vehicle Name" name="veh_name" value="{{ $vehicle->veh_name }}" required>
</div>


<div class="form-group col-md-4">
<label>CC</label>
<input type="text" class="form-control" placeholder="CC" name="cc" value="{{ $vehicle->cc }}" required>
</div>


<div class="form-group col-md-4">
<label>Seating Capacity</label>
<input type="number" class="form-control" placeholder="seating" name="seating" required>
</div>

</div>



<div class="form-row">
<div class="form-group col-md-4">
<label>Brand Name</label>
<select class="form-control" name="brand_name" required> 
<option  disabled selected value="">Choose Brand</option>
@foreach($brandData as $row)
		<option value="{{ $row->id }}" {{ $vehicle->brand == $row->id ? 'selected' : '' }}>{{ $row->brand_name }}</option>
@endforeach
</select>
</div>


<div class="form-group col-md-4">
<label>Status</label>
<select class="form-control" name="status" required> 
<option  disabled selected value="">Choose Status</option>
<option value="Y" {{ $vehicle->status == 'Y' ? 'selected' : '' }}>Active</option>
<option value="N" {{ $vehicle->status == 'N' ? 'selected' : '' }}>In-Active</option>
</select>
</div>
</div>

<button type="submit" class="btn btn-primary">Submit</button>
</form>
</div>
</div>
</div>
</div>




</div>
</div>
</div>
</div>


@endsection