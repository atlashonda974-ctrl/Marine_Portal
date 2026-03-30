@extends('master')
@section('content')



<div class="content-body">
<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">


<div class="card">
<div class="card-header">
<h4 class="card-title">Brand</h4>
</div>
<div class="card-body">
<div class="basic-form">
<form class="form-horizontal" role="form" method="POST" id="my-form" action="{{ url('/brandUpdate/' . $brand->id) }}" id="my-form" autocomplete="off" enctype="multipart/form-data">
                        {!! csrf_field() !!}


                        <div class="form-row">
<div class="form-group col-md-4">
<label>Brand Name</label>
<input type="text" class="form-control" placeholder="Brand Name" name="brand_name" value="{{ $brand->brand_name }}" required>
</div>

<div class="form-group col-md-4">
    <label>Vehicle Type</label>
    <select class="form-control" name="veh_type" required>
        <option disabled value="">Choose Vehicle Type</option>
        <option value="C" {{ $brand->status == 'C' ? 'selected' : '' }}>Car</option>
        <option value="B" {{ $brand->status == 'B' ? 'selected' : '' }}>Bike</option>
    </select>
</div>


<div class="form-group col-md-4">
    <label>Status</label>
    <select class="form-control" name="status" required>
        <option disabled value="">Choose Status</option>
        <option value="Y" {{ $brand->status == 'Y' ? 'selected' : '' }}>Active</option>
        <option value="N" {{ $brand->status == 'N' ? 'selected' : '' }}>In-Active</option>
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