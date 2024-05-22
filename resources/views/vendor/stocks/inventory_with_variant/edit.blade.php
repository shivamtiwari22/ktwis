@extends('vendor.layout.app')

@section('meta_tags')
    <!-- Meta tags -->
@endsection

@section('title')
    <!-- Title -->
@endsection

@section('css')
    <!-- CSS styles -->

    <style>
        .image {
            width: 100%;
        }
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.inventory.index') }}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Inventory with Variant</li>
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
                    <th>Image(jpg, jpeg, png, 2MB max) <span class="text-danger">*</span>
                        {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The image of the variant"><sup><i class="fa fa-question"></i></sup></small> --}}
                    </th>
                    <th>SKU <span class="text-danger">*</span>
                        {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="SKU (Stock Keeping Unit) is the seller specific identifier. It will help to manage your inventory"><sup><i class="fa fa-question"></i></sup></small> --}}
                    </th>
                    <th>Stock quantity <span class="text-danger">*</span>
                        {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="Number of items you have on your warehouse"><sup><i class="fa fa-question"></i></sup></small> --}}
                    </th>
                    <th>Purchase price <span class="text-danger">*</span>
                        {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="Recommended field. This will help to calculate profits and generate reports"><sup><i class="fa fa-question"></i></sup></small> --}}
                    </th>
                    <th>Price <span class="text-danger">*</span>
                        {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The price without any tax. Tax will be calculated automatically based on the shipping zone."><sup><i class="fa fa-question"></i></sup></small> --}}
                    </th>
                    <th>Offer price <span class="text-danger">*</span>
                        {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The offer price will be effective between the offer start and end dates"><sup><i class="fa fa-question"></i></sup></small> --}}
                    </th>
                    <th><i class="fa fa-trash-o"></i></th>
                </tr>
            </thead>
            <tbody style="zoom: 0.80;">

                @php  $count = 0;  @endphp
                @foreach ($permutations as $permutation)
                    <tr>
                        <td>
                            <div class="form-group" id="my_variant">

                                <?php
                                $attr_value_ids = [];
                                $attr_ids = [];
                                ?>


                                @foreach ($permutation as $attributeValue)
                                    {{ App\Models\AttributeValue::where('id', $attributeValue)->first()->attribute_value }},
                                    <?php
                                    $attr_value_id = App\Models\AttributeValue::where('id', $attributeValue)
                                        ->pluck('id')
                                        ->toArray();
                                    $attr_id = App\Models\AttributeValue::where('id', $attributeValue)
                                        ->pluck('attribute_id')
                                        ->toArray();
                                    $attr_value_ids = array_merge($attr_value_ids, $attr_value_id);
                                    $attr_ids = array_merge($attr_ids, $attr_id);
                                    ?>
                                @endforeach
                                <input type="hidden" name="combinations[]" value="{{ implode(',', $attr_value_ids) }}">
                                <input type="hidden" name="attr_ids[]" value="{{ implode(',', $attr_ids) }}">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">

                                <input name="image[]" type="file" accept="image/*" id="variant_img_{{$count}}" class="image" > 
                               

                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input class="form-control variant_id" placeholder="" name="variant_id[]" type="hidden"
                                    value="{{ isset($variants[$count]) ? $variants[$count]->id : '' }}">
                                <input class="form-control sku" placeholder="Seller SKU" id="sku" name="sku[]"
                                    type="text" value="{{ isset($variants[$count]) ? $variants[$count]->sku : '' }}">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input class="form-control quantity" placeholder="Stock quantity" id="stock_quantity"
                                    name="stock_quantity[]" type="number" min="1"
                                    value="{{ isset($variants[$count]) ? $variants[$count]->stock_quantity : '' }}">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input class="form-control purchasePrice" step="any" placeholder="Purchase price"
                                    id="purchase_price" name="purchase_price[]" min="1" type="number"
                                    value="{{ isset($variants[$count]) ? $variants[$count]->purchase_price : '' }}">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input class="form-control salePrice" step="any" placeholder="Price" id="price"
                                    name="price[]" type="number" min="1"
                                    value="{{ isset($variants[$count]) ? $variants[$count]->price : '' }}">
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input class="form-control offerPrice" step="any" placeholder="Offer price"
                                    id="offer_price" name="offer_price[]" type="number" min="1"
                                    value="{{ isset($variants[$count]) ? $variants[$count]->offer_price : '' }}">
                            </div>
                        </td>
                    </tr>

                    @php
                    $count++;
                    @endphp
                @endforeach
            </tbody>
            <td colspan="7">
                <div>
                    <button type="submit" class="btn btn-primary" style="display: block; margin: 0 auto;">Update</button>
                </div>
            </td>
        </table>
    </form>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#inventory_with_variant').submit(function(e) {
                e.preventDefault();
                if (validateForm()) {
                    var formData = new FormData(this);

                    $.ajax({
                        url: "{{ route('vendor.inventory.update_variant') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log(response.message);
                            $.NotificationApp.send("Success", response.message, "top-right",
                                "rgba(0,0,0,0.2)", "success");

                            setTimeout(function() {
                                window.location.href = "{{ route('vendor.inventory.index')}}"
                            }, 1000);
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                }
            });

            function validateForm() {
                var isValid = true;

                $('.error-message').remove();

                $('#sku, #stock_quantity, #purchase_price, #price , #offer_price').each(function() {
                    var $input = $(this);

                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });


                $('.image').each(function() {
                    var $input = $(this);

                    var file = $input[0].files[0]; // Get the selected file
                    if (file) {
                        var allowedExtensions = ['png', 'jpg', 'jpeg'];
                        var maxFileSize = 2 * 1024 * 1024; // 2MB in bytes

                        var fileExtension = file.name.split('.').pop().toLowerCase();
                        if (allowedExtensions.indexOf(fileExtension) === -1) {
                            var errorMessage = 'Only PNG, JPEG, and JPG files are allowed';
                            $input.addClass('is-invalid');
                            $input.after(' <span class="error-message" style="color:red;">' + errorMessage +
                                '</span>');
                            isValid = false;
                        } else if (file.size > maxFileSize) {
                            var errorMessage = 'File size cannot exceed 2MB';
                            $input.addClass('is-invalid');
                            $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                                '</span>');
                            isValid = false;
                        }
                    }
                });


                $('#sku, #stock_quantity, #purchase_price, #price , #offer_price', '.image').on(
                    'input change',
                    function() {
                        $(this).removeClass('is-invalid');
                        $(this).next('.error-message').remove();
                    });

                return isValid;
            }
        });
    </script>
@endsection
