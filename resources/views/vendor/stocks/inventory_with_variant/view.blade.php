@extends('vendor.layout.app')

@section('meta_tags')
<!-- Meta tags -->
@endsection

@section('title')
<!-- Title -->
@endsection

@section('css')
<!-- CSS styles -->
@endsection

@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.inventory.index')}}">Inventory</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Inventory with Variant</li>
    </ol>
</nav>
<form id="inventory_with_variant" enctype="multipart/form-data">
    @csrf
    <table class="table table-default form-control" id="variantsTable">
        <thead>
            <tr>
                <th>Variants
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="Product variants"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Image
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The image of the variant"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>SKU
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="SKU (Stock Keeping Unit) is the seller specific identifier. It will help to manage your inventory"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Stock quantity
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="Number of items you have on your warehouse"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Purchase price
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="Recommended field. This will help to calculate profits and generate reports"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Price
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The price without any tax. Tax will be calculated automatically based on the shipping zone."><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Offer price
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The offer price will be effective between the offer start and end dates"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th><i class="fa fa-trash-o"></i></th>
            </tr>
        </thead>
        <tbody style="zoom: 0.80;">
             @foreach($variants as $item)
            <tr>
                <td>
                    <div class="form-group" id="my_variant">
                   
                        @php
                        $attrValueIds = explode(',', $item->attr_value_id);
                        $attributeValues = \App\Models\AttributeValue::whereIn('id', $attrValueIds)->get();
                        @endphp
                  


                        @foreach ($attributeValues as $attributeValue)
                        {{ $attributeValue->attribute_value }},
                        @endforeach
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <label class="img-btn-sm">
                            <a href="{{asset('public/vendor/featured_image/inventory_with_variant/'.$item->image_variant ) }}" target="_blank">
                                <img src="{{asset('public/vendor/featured_image/inventory_with_variant/'.$item->image_variant ) }}" alt="{{$item->image_variant}}" height="100px" width="80px">
                            </a>
                        </label>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control variant_id" placeholder="" name="variant_id" type="hidden" value="{{ $item->id }}">
                        <input class="form-control sku" placeholder="Seller SKU" required="" name="sku" type="text" disabled value="{{ $item->sku }}">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control quantity" placeholder="Stock quantity" required="" name="stock_quantity" disabled type="number" value="{{ $item->stock_quantity }}">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control purchasePrice" step="any" placeholder="Purchase price" name="purchase_price" disabled type="number" value="{{ $item->purchase_price }}">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control salePrice" step="any" placeholder="Price" required="" name="price" type="number" disabled value="{{ $item->price }}">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control offerPrice" step="any" placeholder="Offer price" name="offer_price" type="number" disabled value="{{ $item->offer_price }}">
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</form>
@endsection

@section('script')

@endsection