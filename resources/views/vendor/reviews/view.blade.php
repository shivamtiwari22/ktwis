@extends('vendor.layout.app')

@section('meta_tags')

@endsection


@section('title')

@endsection


@section('css')
<style>
    .star {
        color: #ccc;
        font-size: 24px;
    }

    .filled-star {
        color: goldenrod;
    }


    .half-star {
        position: relative;
        display: inline-block;
        font-size: 24px;
    }

    .half-star::before {
        content: '\2605';
        position: absolute;
        left: 0;
        width: 50%;
        overflow: hidden;
        color: goldenrod;
        z-index: 1;
    }

    .half-star::after {
        content: '\2606';
        position: absolute;
        left: 50%;
        width: 0%;
        overflow: hidden;
        color: goldenrod;
        z-index: 0;
    }
</style>
@endsection


@section('main_content')
<!-- Warning Alert Modal -->
<div style="display: flex; justify-content: space-between; align-items: flex-end;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{route('vendor.reviews.index')}}">Review</a></li>
            <li class="breadcrumb-item active" aria-current="page">View</li>
        </ol>
    </nav>
</div>
<hr>
<div class="card mt-1 p-2">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mt-2 mb-3">Product Reviews</h4>
                    <div class="table-responsive">
                        <div class="row">
                            <div class="col-sm-4">
                                <h5>Customer</h5>
                            </div>
                            <div class="col-sm-4">
                                <h5>Rating</h5>
                            </div>
                            <div class="col-sm-4">
                                <h5>Comment</h5>
                            </div>
                        </div><hr>

                        @foreach ($products as $review)
                       
                        <div class="row">
                            <div class="col-sm-4">
                                <h5 class="font-14 my-1 fw-normal">{{ $review->user->name }}</h5>
                                <span class="text-muted font-13">{{ $review->created_at->format('d F Y') }}</span>
                            </div>
                            <div class="col-sm-4">
                                @php
                                    $averageRating = $review->rating;
                                    $starRating = '';
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $averageRating) {
                                            $starRating .= '<span class="star filled-star">&#9733;</span>';
                                        } else if ($i - 0.5 <= $averageRating) {
                                            $starRating .= '<span class="star half-star">&#9733;</span>';
                                        } else {
                                            $starRating .= '<span class="star">&#9734;</span>';
                                        }
                                    }
                                    echo $starRating;
                                    @endphp
                                    <h5 class="font-14 my-1 fw-normal">({{ $review->rating }})</h5>
                            </div>
                            <div class="col-sm-4">
                                <h5 class="font-14 my-1 fw-normal">{{ $review->comment }}</h5>
                            </div>
                            <hr>
                        </div>
                 
                        @endforeach
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection