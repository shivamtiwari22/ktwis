@extends('admin.layout.app')

@section('meta_tags')

@endsection


@section('title')

@endsection


@section('css')

@endsection


@section('main_content')
<br><br>
<div class="row">
    <div class="col-sm-3">
        <div class="card border-info mb-3" style="max-width: 18rem;">
            <div class="card-header">Order</div>
            <div class="card-body text-info">
                <h5 class="card-title">Order</h5>
                <p class="card-text"><a href="{{route('admin.order')}}">{{$order}}</a></p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-info mb-3" style="max-width: 18rem;">
            <div class="card-header">Cart</div>
            <div class="card-body text-info">
                <h5 class="card-title">Total Carts</h5>
                <p class="card-text"><a href="{{route('admin.cart')}}">{{$cart}}</a></p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-info mb-3" style="max-width: 18rem;">
            <div class="card-header">Wishlist</div>
            <div class="card-body text-info">
                <h5 class="card-title">Total Wishlist</h5>
                <p class="card-text"><a href="{{route('admin.wishlist')}}">{{$wishlist}}</a></p>
            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card border-info mb-3" style="max-width: 18rem;">
            <div class="card-header">Cancellation</div>
            <div class="card-body text-info">
                <h5 class="card-title">Total Cancellation</h5>
                <p class="card-text"><a href="{{route('admin.cancel')}}"> {{$cancellation}} </a></p>
            </div>
        </div>
    </div>
</div>


@endsection


@section('script')
<script>
    const messaging = firebase.messaging();
    getToken(messaging, {vapidKey: "BEtvuQXMxcXOV8img2TyCsodoS3JazMDxw3HNA5UVFisw8beKd-82D8Dp7ypr_Pqi2975qoqmLQLEQU-ZifIO4M"});
    </script>
    
@endsection