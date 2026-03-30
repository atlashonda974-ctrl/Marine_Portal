@extends('master')
@section('content')

<style>

        .multipleSelection {
            width: 500px;
        }
 
        .selectBox {
            position: relative;
        }
 
        .selectBox select {
            width: 100%;
            font-weight: bold;
        }
 
        .overSelect {
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }
 
        #checkBoxes {
            display: none;
        }
 
        #checkBoxes label {
            display: block;
        }
 
        #checkBoxes label:hover {
            color: white;
        }
    </style>
</style>


<script>
 let show = true;
 
 function showCheckboxes() {
     let checkboxes = document.getElementById("checkBoxes");

     if (show) {
         checkboxes.style.display = "block";
         show = false;
     } else {
         checkboxes.style.display = "none";
         show = true;
     }
 }
</script>


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
<form class="form-horizontal" role="form" method="POST" id="my-form" action="{{ url('/createBrand') }}" id="my-form" autocomplete="off" enctype="multipart/form-data">
                        {!! csrf_field() !!}



<div class="form-row">
<div class="form-group col-md-4">
<label>Brand Name</label>
<input type="text" class="form-control" placeholder="Brand Name" name="brand_name" required>
</div>

<div class="form-group col-md-4">
<label>Vehicle Type</label>
<select class="form-control" name="veh_type" required> 
<option  disabled selected value="">Choose Vehicle type</option>
            <option value="C">Car</option>
			<option value="B">Bike</option>
</select>
</div>


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


@endsection