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
<form class="form-horizontal" role="form" method="POST" id="my-form" action="{{ url('/productUpdate/' . $product->id) }}" id="my-form" autocomplete="off" enctype="multipart/form-data">
                        {!! csrf_field() !!}


                        <div class="form-row">
<div class="form-group col-md-4">
<label>Product Name</label>
<input type="text" class="form-control" placeholder="Product Name" name="product_name" value="{{ $product->prod_name }}" required>
</div>


<div class="form-group col-md-4">
<label>Brand Name</label>
<select class="form-control" name="brand_name" required> 
<option  disabled selected value="">Choose Brand</option>
@foreach($brandData as $row)
		<option value="{{ $row->id }}" {{ $product->brand == $row->id ? 'selected' : '' }}>{{ $row->brand_name }}</option>
@endforeach
</select>
</div>
</div>


<label>Cover Type</label></br>
<div class="form-row">
<div class="form-group col-md-3">
<label>New (%)</label>
<input type="number" class="form-control" placeholder="New Cover" name="new_cvr" value="{{ $product->new_cvr }}" step="any" required>
</div>



<div class="form-group col-md-3">
<label>Used (%)</label>
<input type="number" class="form-control" placeholder="Used Cover" name="used_cvr"  value="{{ $product->used_cvr }}" step="any"  required>
</div>



<div class="form-group col-md-3">
<label>Renew (%)</label>
<input type="number" class="form-control" placeholder="Renew Cover" name="renew_cvr" value="{{ $product->rewl_cvr }}" step="any"  required>
</div>

</div>



<div class="form-row">
<div class="form-group col-md-4">
<label>Fixed</label>
<select class="form-control" name="fixed" required> 
<option  disabled selected value="">Choose Fixed Status</option>
<option value="Y" {{ $product->fixed == 'Y' ? 'selected' : '' }}>Yes</option>
<option value="N" {{ $product->fixed == 'N' ? 'selected' : '' }}>No</option>
</select>
</div>

<div class="form-group col-md-4">
<label>Fixed Sum Insured</label>
<input type="number" class="form-control" placeholder="Fixed Sum Insured" name="fixed_si" value="{{ $product->fixed_si }}" required>
</div>


<div class="form-group col-md-4">
<label>Fixed Premium</label>
<input type="number" class="form-control" placeholder="Fixed Premium" name="fixed_pre" value="{{ $product->fixed_pre }}" required>
</div>

</div>


<div class="form-row">
<div class="form-group col-md-4">
<label>Status</label>
<select class="form-control" name="status" required> 
<option  disabled selected value="">Choose Status</option>
<option value="Y" {{ $product->status == 'Y' ? 'selected' : '' }}>Active</option>
<option value="N" {{ $product->status == 'N' ? 'selected' : '' }}>In-Active</option>
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