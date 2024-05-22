@extends('vendor.layout.app')

@section('meta_tags')

@endsection


@section('title')

@endsection


@section('css')
<style>
    .toast-success {
        background-color: #28a745 !important;
        color: #fff !important;
    }

    .toast-error {
        background-color: #dc3545 !important;
        color: #fff !important;
    }
</style>
@endsection


@section('main_content')


<!-- Warning Alert Modal -->
<div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-warning"></i>
                    <h4 class="mt-2">Confirm</h4>
                    <p class="mt-3">Are You Sure to Delete this </p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- search modal -->
<!-- search modal -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.inventory.index')}}">Inventory</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Inventory</li>
    </ol>
</nav>
<div class="form-control">
        <div class="row">
            <div class="col-sm-2">
                <label for="featured_image">Image :</label>
                <input type="hidden" value="{{$inventory->id}}" name="id" required>
                <input type="hidden" value="{{$inventory->p_id}}" name="p_id" required>
                <input type="hidden" disabled accept="image/*" name="featured_image" required id="featured_image" class="form-control">
            </div>
            <div class="col-sm-2">
                <a href="{{ asset('public/vendor/featured_image/inventory/' . $inventory->image) }}" target="_blank"> <img src="{{ asset('public/vendor/featured_image/inventory/' . $inventory->image) }}" style="width: 100px; height: 80px; border: 1px solid black;" alt="Image"></a>
            </div>
            <div class="col-sm-8">
                <div id="imageContainer"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <label for="name">SKU</label>
                <input type="text" name="sku" value="{{$inventory->sku}}" disabled id="sku" required class="form-control">
            </div>
            <div class="col-sm-4">
                <label for="stock_qty">Stock Qty</label>
                <input type="number" value="{{$inventory->stock_qty}}" disabled name="stock_qty" id="stock_qty" required class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                <label for="purchase_price">Purchase Price</label>
                <input type="number" name="purchase_price" id="purchase_price" disabled value="{{$inventory->purchase_price}}" required class="form-control">
            </div>
            <div class="col-sm-3">
                <label for="price">Price</label>
                <input type="number" name="price" id="price" disabled value="{{$inventory->price}}" required class="form-control">
            </div>
            <div class="col-sm-3">
                <label for="offer_price">Offer Price</label>
                <input type="number" name="offer_price" id="offer_price" disabled value="{{$inventory->offer_price}}" required class="form-control">
            </div>
        </div>
</div>

@endsection

@section('script')

@endsection