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
        <li class="breadcrumb-item"><a href="{{route('vendor.carrier.list')}}">Carrier</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add New</li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('vendor.carrier.list')}}" class="btn btn-primary">View All Carriers</a>
        </div>
        <form id="coupon_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{$carrier->name}}" disabled>
                    <input type="hidden" class="form-control" name="id" value="{{$carrier->id}}">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="example-select" class="form-label">Tracking URL</label>
                    <input type="text" class="form-control" name="url" value="{{$carrier->tracking_url}}" disabled>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Phone</label>
                    <input type="number" class="form-control" name="phone" value="{{$carrier->phone}}" disabled>
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="example-select" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email"  value="{{$carrier->email}}" disabled>
                </div>
                <div class="mb-3 col-lg-4">
                    <label class="form-label">Logo</label>
                    @if($carrier->logo)<div><img src="{{ asset('public/admin/shipping/carriers/logo/' . $carrier->logo) }}" alt="{{ $carrier->name }}" height="30px"></div>@endif
                </div>
            </div>
            <div class="row">
                <label for="simpleinput" class="form-label">Status</label>
                <div class="mb-3">
                    <input type="hidden" name="status" value="{{$carrier->status}}">
                    <input type="checkbox" id="switch2" {{ $carrier->status == "1" ? 'checked' : '' }} data-switch="primary" value={{$carrier->status}} onclick="updateCheckboxValue(this)" disabled>
                    <label for="switch2" data-on-label="On" data-off-label="Off"></label>          
                </div>
            </div>
        </form>
    </div>
@endsection


@section('script')
@endsection
