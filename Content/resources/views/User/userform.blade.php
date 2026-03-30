@extends('master')
@section('content')

<div class="content-body">
<div class="container-fluid">
<div class="row">
<div class="col-xl-9 col-xxl-12">


<div class="card">
<div class="card-header">
<h4 class="card-title">Create New User</h4>
</div>
<div class="card-body">
<div class="basic-form">
<form class="form-horizontal" role="form" method="POST" id="my-form" action="{{ url('/createUser') }}" autocomplete="off">
    {!! csrf_field() !!}

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Username</label>
            <input type="text" class="form-control" placeholder="Username" name="name">
        </div>

        <div class="form-group col-md-4">
            <label>Password</label>
            <input type="password" class="form-control" placeholder="Password" name="password">
        </div>

        <div class="form-group col-md-4">
            <label>Email</label>
            <input type="email" class="form-control" placeholder="Email" name="email">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>City</label>
            <input type="text" class="form-control" placeholder="City" name="city">
        </div>

        <div class="form-group col-md-4">
            <label>Zone</label>
            <select class="form-control" name="loc_zone" id="loc_zone" required> 
                <option disabled selected value="">Choose Zone</option>
                <option value="N">North</option>
                <option value="S">South</option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Dealer Code</label>
            <input type="text" class="form-control" placeholder="Dealer Code" name="dealer_code">
        </div>

        <div class="form-group col-md-4">
            <label>ABB</label>
            <input type="text" class="form-control" placeholder="ABB" name="abb">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Branch Code Conv.</label>
            <input type="number" class="form-control" placeholder="Branch Code Conv" name="loc_code">
        </div>

        <div class="form-group col-md-4">
            <label>Branch Code Takaful</label>
            <input type="number" class="form-control" placeholder="Branch Code Takaful" name="loc_code_tak">
        </div>

        <div class="form-group col-md-4">
            <label>Agency Code</label>
            <input type="number" class="form-control" placeholder="Agency Code" name="agency">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Contact Person</label>
            <input type="text" class="form-control" placeholder="Contact Person" name="con_per">
        </div>

        <div class="form-group col-md-4">
            <label>Contact Number</label>
            <input type="number" class="form-control" placeholder="Contact Number" name="con_no">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-4">
            <label>Brand Name</label>
            <select class="form-control" name="brand" required> 
                <option disabled selected value="">Choose Brand</option>
                @foreach($brandData as $row)
                    <option value="{{ $row->id }}">{{ $row->brand_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-4">
            <label>GIS Integration</label>
            <select class="form-control" name="intg_tag" required> 
                <option disabled selected value="">Choose Integration Status</option>
                <option value="Y">Allow</option>
                <option value="N">Prohibit</option>
            </select>
        </div>

        <div class="form-group col-md-4">
            <label>Status</label>
            <select class="form-control" name="status" required> 
                <option disabled selected value="">Choose Status</option>
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