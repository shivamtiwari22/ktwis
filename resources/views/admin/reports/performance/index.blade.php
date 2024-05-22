@extends('admin.layout.app')


@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Performance</li>
    </ol>
</nav>
<div class="row">
    <div class="col-lg-3">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-account-multiple widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Number of Customers">New Vendor</h5>
                <h3 class="mt-3 mb-3">{{$vendorCount}}</h3>
                <p class="mb-0 text-muted">
                    <span class="text-nowrap">Since last month</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card widget-flat">
            <div class="card-body">
                <div class="float-end">
                    <i class="mdi mdi-account-multiple widget-icon"></i>
                </div>
                <h5 class="text-muted fw-normal mt-0" title="Number of Customers">Trailing Vendors</h5>
                <h3 class="mt-3 mb-3">{{$vendor_trail_Count}}</h3>
                <p class="mb-0 text-muted">
                    <span class="text-nowrap">Since now</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection


@section('script')

@endsection