@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.shipping.rates')}}">Shipping Rates</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Rate</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.shipping.rates')}}" class="btn btn-primary">View Shipping Rates</a>
    </div>
    <form id="rate_form">
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="simpleinput" class="form-label">Name</label>
                <input type="hidden" class="form-control" name="id" value="{{$rates->id}}" id="id" >
                <input type="text" class="form-control" name="name" value="{{$rates->name}}" id="name" disabled>
            </div>
            <div class="mb-3 col-lg-6">
                <label for="example-select" class="form-label">Shipping Carrier</label>
                <select name="carrier_id" id="carrier_id" class="form-control" disabled>
                    <option value="">-- Select Carrier --</option>
                    @foreach ($carriers as $carrier)
                    <option value="{{$carrier->id}}" {{ $rates->carrier_id == $carrier->id ? 'selected' : '' }}>{{$carrier->name}}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="simpleinput" class="form-label"> Delivery Takes</label>
                <input type="number" class="form-control" id="delivery_days" value="{{$rates->delivery_time}}" disabled name="delivery_days" placeholder="2 to 5 days">
            </div>
            <div class="mb-3 col-lg-4">
                <label for="example-select" class="form-label">Minimum Order Weight</label>
                <input type="number" class="form-control" value="{{$rates->minimum_order_weight}}" disabled name="minimum" value="0">
            </div>
            <div class="mb-3 col-lg-4">
                <label class="form-label">Maximum Order Weight</label>
                <input type="number" class="form-control" value="{{$rates->max_order_weight}}" disabled name="maximum" value="100">
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="simpleinput" class="form-label">Status</label>
                <select name="status_rate" id="status_rate" class="form-control" disabled>
                    <option value="">-- Select Status --</option>
                    <option value="active" {{ $rates->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $rates->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="mb-3 col-lg-4">
                <label class="form-label">Rate</label>
                <input type="number" class="form-control" name="rate" value="{{$rates->rate}}" id="rate" disabled>
            </div>
            <div class="mb-3 col-lg-4">
                <label class="form-label">Free Shipping</label>
                <div class="mb-3">
                    <input type="hidden" name="free_shipping" id="free_shipping" value="{{ $rates->is_free ? 1 : 0 }}" disabled>
                    <input type="checkbox" id="switch2" data-switch="primary" value="1" readonly {{ $rates->is_free ? 'checked' : '' }} onclick="updateCheckboxValue(this)">
                    <label for="switch2" data-on-label="Free" data-off-label="Paid"></label>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection


@section('script')


@endsection