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
        <li class="breadcrumb-item"><a href="{{route('vendor.coupon.list')}}">Coupon</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Coupons</li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('vendor.coupon.list')}}" class="btn btn-primary">View All Coupons</a>
        </div>
        <form id="coupon_form">
            <input type="hidden" value="{{$coupon->id}}" name="coupon_id" disabled>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Code</label>
                    <input type="text" class="form-control" name="code" value="{{$coupon->code}}" disabled>
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="example-select" class="form-label">Coupon Type</label>
                    <select class="form-select" data-id="{{ $coupon->id }}" name="coupon_type" disabled>
                        <option value="fixed" {{ $coupon->coupon_type == "fixed" ? "selected" : "" }}>Fixed</option>
                        <option value="Percentage" {{ $coupon->coupon_type == "Percentage" ? "selected" : "" }}>Percentage</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Amount</label>
                    <input type="text" class="form-control" name="amount"  value="{{$coupon->amount}}" disabled>
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="example-select" class="form-label">No. of Coupons</label>
                    <input type="number" class="form-control" name="no_of_coupons" value="{{$coupon->no_of_coupons}}" disabled>
                </div>
                <div class="mb-3 col-lg-4">
                    <label class="form-label">Expiry Date</label>
                    <input type="date" class="form-control date" id="birthdatepicker" name="expiry_date" value="{{$coupon->expiry_date}}" disabled>
                </div>
            </div>
            <div>
                <label for="example-select" class="form-label">Coupon Status</label>
                <select class="change_status form-select" data-id="{{ $coupon->id }}" name="status" disabled>
                    <option value="pending" {{ $coupon->status == "pending" ? "selected" : "" }}>Pending</option>
                    <option value="published" {{ $coupon->status == "published" ? "selected" : "" }}>Publish</option>
                </select>
        </form>
    </div>
@endsection


@section('script')
@endsection
