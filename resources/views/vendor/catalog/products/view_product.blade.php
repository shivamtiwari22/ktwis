@extends('vendor.layout.app')

@section('meta_tags')

@endsection


@section('title')

@endsection


@section('css')

@endsection


@section('main_content')

<!--  -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.products.index')}}">Products</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Products</li>
    </ol>
</nav>
<div class="form-group">
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            <div class="box-header ">
                <h3 class="box-title"> View Product</h3>
            </div>
            <div>
                <div class="form-control">
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="name">Name : </label>
                            <label for="name" class="product_color">{{$product->name}}</label>
                        </div>
                        <div class="col-sm-6">
                            <label for="status">Status :</label>
                            <label for="status" class="product_color">{{ucwords($product->status)}}</label>
                        </div>
                    </div>
                </div>

                <div class="form-control">
                    <label for="description">Description :</label>
                    <label for="description" class="product_color">{{ mb_substr($product->description, 0,80)}}</label>
                </div>

                <div class="form-control">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="gallery_images">Gallery Images: </label>
                        </div>
                        <div class="col-sm-8">
                            @foreach($gallery_images as $image)
                            <a href="{{ asset('public/vendor/gallery_images/' . $image) }}" target="_blank"> <img src="{{ asset('public/vendor/gallery_images/' . $image) }}" style="width: 40px; height: 40px;" alt="Image"></a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="form-control">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="featured_image">Featured Image : </label>
                        </div>
                        <div class="col-sm-8">
                            <a href="{{ asset('public/vendor/featured_image/' . $product->featured_image) }}" target="_blank"> <img src="{{ asset('public/vendor/featured_image/' . $product->featured_image) }}" style="width: 40px; height: 40px;" alt="Image"></a>
                        </div>
                    </div>
                </div>
                <div class="form-control">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="categories">Categories : </label>
                            @foreach ($product->categories as $category)
                            <li>{{ $category->category_name }}</li>
                            @endforeach
                        </div>
                        <div class="col-sm-4">
                            <label for="requires_shipping">Requires Shipping : </label>
                            <input type="checkbox" name="requires_shipping" id="requires_shipping" disabled class="product_color" <?php if ($product->requires_shipping == 1) {
                                                                                                                                        echo 'checked';
                                                                                                                                    } ?>>
                        </div>

                        <div class="col-sm-4">
                            <label for="requires_shipping">has Variant : </label>
                            <input type="checkbox" name="has_varaint" disabled class="product_color"  {{$product->has_variant == 1 ? "checked" : ""}}    >
                        </div>
                    </div>
                </div>

                <div class="form-control">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="brand">Brand : </label>
                            <label for="brand" class="product_color">{{$product->brand}} </label>
                        </div>
                        <div class="col-sm-4">
                            <label for="model_number">Model Number : </label>
                            <label for="model_number" class="product_color">{{$product->model_number}}</label>
                        </div>
                        <div class="col-sm-4">
                            <label for="min_order_qty">Minimum Order Quantity :</label>
                            <label for="min_order_qty" class="product_color">{{$product->min_order_qty}}</label>
                        </div>
                    </div>
                </div>

                <div class="form-control">
                    <div class="row">
                        <div class="col-sm-4">
                            <label for="slug">Slug :</label>
                            <label for="slug" class="product_color">{{$product->slug}}</label>
                        </div>
                        <div class="col-sm-4">
                            <label for="tags">Tags :</label>
                            <label for="tags" class="product_color">{{$product->tags}}</label>
                        </div>
                        <div class="col-sm-4">
                            <label for="weight">Weight (g) :</label>
                            <label for="weight" class="product_color">{{$product->weight}}</label>
                        </div>
                    </div>
                </div>


                <div class="form-control">
                    <?php
                    $dimensions = explode(',', $product->dimensions);
                    $length = $dimensions[0];
                    $width = $dimensions[1];
                    $height = $dimensions[2];
                    ?>
                    <div class="row">
                        <label for="dimension">Dimensions (cm) </label>
                        <div class="col-sm-4 border">
                            <label for="dimension">Length :</label>
                            <label for="dimension" class="product_color">{{$length}} </label>
                        </div>
                        <div class="col-sm-4 border">
                            <label for="dimension">Width :</label>
                            <label for="dimension" class="product_color">{{$width}} </label>
                        </div>
                        <div class="col-sm-4 border">
                            <label for="dimension">Height :</label>
                            <label for="dimension" class="product_color">{{$height}} </label>
                        </div>
                    </div>
                </div>

                <div class="form-control">
                    <label for="key_features">Key Features : </label>
                    <label for="key_features" class="product_color">{{   mb_substr($product->key_features, 0,200)}}</label>
                    <!-- <textarea name="key_features" id="key_features" class="form-control"></textarea> -->
                </div>

                <div class="form-control">
                    <label for="linked_items">Linked Items :</label>
                    <label for="linked_items" class="product_color">{{$product->linked_items}}</label>
                </div>

                <div class="form-control">
                    <label for="meta_title">Meta Title :</label>
                    <label for="meta_title" class="product_color">{{$product->meta_title}}</label>
                </div>

                <div class="form-control">
                    <label for="meta_description">Meta Description :</label>
                    <label for="meta_description" class="product_color">{{$product->meta_description}}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-1"></div>
</div>


@endsection

@section('script')
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>


@endsection