@extends('master')
@section('content')


<div class="content-body">
<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">


<div class="card">
<div class="card-header">
<h4 class="card-title">Product</h4>
</div>
<div class="card-body">
<div class="basic-form">
<form class="form-horizontal" role="form" method="POST" id="my-form" action="{{ url('/createProduct') }}" id="my-form" autocomplete="off" enctype="multipart/form-data">
                        {!! csrf_field() !!}



<div class="form-row">
<div class="form-group col-md-4">
<label>Product Name</label>
<input type="text" class="form-control" placeholder="Product Name" name="product_name" required>
</div>


<div class="form-group col-md-4">
<label>Brand Name</label>
<select class="form-control" name="brand_name" required> 
<option  disabled selected value="">Choose Brand</option>
@foreach($brandData as $row)
		<option value="{{ $row->id }}">{{ $row->brand_name }}</option>
@endforeach
</select>
</div>
</div>


<label>Cover Type</label></br>
<div class="form-row">
<div class="form-group col-md-4">
<label>New (%)</label>
<input type="number" class="form-control" placeholder="New Cover" name="new_cvr" step="any" required>
</div>



<div class="form-group col-md-4">
<label>Used (%)</label>
<input type="number" class="form-control" placeholder="Used Cover" name="used_cvr" step="any" required>
</div>



<div class="form-group col-md-4">
<label>Renew (%)</label>
<input type="number" class="form-control" placeholder="Renew Cover" name="renew_cvr" step="any" required>
</div>

</div>



<div class="form-row">
<div class="form-group col-md-4">
<label>Fixed</label>
<select class="form-control" name="fixed" required> 
<option  disabled selected value="">Choose Fixed Status</option>
            <option value="Y">Yes</option>
			      <option value="N">No</option>
</select>
</div>

<div class="form-group col-md-4">
<label>Fixed Sum Insured</label>
<input type="number" class="form-control" placeholder="Fixed Sum Insured" name="fixed_si" required>
</div>


<div class="form-group col-md-4">
<label>Fixed Premium</label>
<input type="number" class="form-control" placeholder="Fixed Premium" name="fixed_pre" required>
</div>

</div>



<div class="form-row">
<div class="form-group col-md-4">
<label>Status</label>
<select class="form-control" name="status" required> 
<option  disabled selected value="">Choose Status</option>
            <option value="Y">Active</option>
			<option value="N">In-Active</option>
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

<script>
  document.getElementById("new_cvr").addEventListener("input", function () {
    this.value = this.value.replace(/[^0-9.]/g, ''); // Remove invalid characters
  });
</script>


@endsection