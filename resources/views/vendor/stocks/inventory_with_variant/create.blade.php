@extends('vendor.layout.app')

@section('meta_tags')

@endsection


@section('title')

@endsection


@section('css')
<style>
    .image {
        width: 100%;
    }
</style>
@endsection


@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.inventory.index')}}">Inventory</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Inventory with Variant</li>
    </ol>
</nav>
<h2>Add Inventory With<span class="badge badge-success-lighten">Variant</span></h2>
<hr>
<form id="inventory_with_variant" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-sm-6">
            <label for="title">Title <span class="text-danger">*</span></label>
            <input class="form-control input-group" type="hidden" name="p_id" value="{{$productID}}" id="p_id">
            <input class="form-control input-group" type="text" required name="title" id="title">
        </div>
        <div class="col-sm-6 form-group">
            <label for="status">Status <span class="text-danger">*</span></label>
            <select name="status" id="status_inv"  class="form-control">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div><br>

    <div class="variant_attribute">

    </div>

    <table class="table table-default" id="variantsTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Variants 
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="Product variants"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Image(jpg, jpeg, png, 2MB max) <span class="text-danger"></span>
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The image of the variant"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>SKU <span class="text-danger">*</span>
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="SKU (Stock Keeping Unit) is the seller specific identifier. It will help to manage your inventory"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Stock quantity <span class="text-danger">*</span>
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="Number of items you have on your warehouse"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Purchase price <span class="text-danger">*</span>
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="Recommended field. This will helps to calculate profits and generate reports"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Price <span class="text-danger">*</span>
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The price without any tax. Tax will be calculated autometically based on shipping zone."><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th>Offer price <span class="text-danger">*</span>
                    {{-- <small class="text-muted" data-toggle="tooltip" data-placement="top" title="The offer price will be effected between the offer start and end dates"><sup><i class="fa fa-question"></i></sup></small> --}}
                </th>
                <th><i class="fa fa-trash-o"></i></th>
            </tr>
        </thead>
        <tbody style="zoom: 0.80;">

            {{-- {{dd($attributeValuePermutations)}} --}}
            @php
            $counter = 1;
            @endphp
            @foreach ($permutations as $permutation)
            <tr>
                <td>
                    <div class="form-group">{{ $counter }}</div>
                    @php
                    $counter++;
                    @endphp
                </td>
                <td>
                    <div class="form-group" id="my_variant">
                        <?php
                        $attr_value_ids = [];
                        $attr_ids = [];
                        ?>
    

                        @foreach ($permutation as $attributeValue)
                        {{ App\Models\AttributeValue::where('id', $attributeValue)->first()->attribute_value }},
                        <?php
                        $attr_value_id = App\Models\AttributeValue::where('id', $attributeValue)->pluck('id')->toArray();
                        $attr_id = App\Models\AttributeValue::where('id', $attributeValue)->pluck('attribute_id')->toArray();
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
                        <label class="img-btn-sm">
                            <input name="image[]" id="image[]" type="file" accept="image/*"  id="variant_img_{{$counter}}" class="image">
                            {{-- <span>Image</span> --}}
                        </label>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control sku" placeholder="Seller SKU" id="sku" name="sku[]" type="text">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control quantity" placeholder="Stock quantity"  id="stock_quantity" name="stock_quantity[]" min="1" type="number">
                    </div> 
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control purchasePrice" step="any" placeholder="Purchase price" name="purchase_price[]" min="1" id="purchase_price" type="number">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control salePrice" step="any" placeholder="Price" name="price[]" min="1" type="number" id="price">
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <input class="form-control offerPrice" step="any" placeholder="Offer price" name="offer_price[]" min="1" type="number" id="offer_price">
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>


    <div class="row">
        <div class="col-sm-6">
            <label for="meta_title">Meta Title:</label>
            <input class="form-control input-group" type="text" name="meta_title" id="meta_title">
        </div>
        <div class="col-sm-6">
            <label for="slug">Slug:</label>
            <input class="form-control input-group" type="text" name="slug" id="slug" readonly>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <label for="meta_description">Meta Description:</label>
            <textarea class="form-control input-group" type="text" name="meta_description" id="meta_description"></textarea>
        </div>
        <div class="col-sm-6">
            <label for="description">Description:</label>
            <textarea class="form-control input-group" name="description" id="description"></textarea>
        </div>
    </div><br>

    <div class="d-flex">
        <button type="submit" class="btn btn-success mx-auto"> Submit</button>
    </div><br>
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
                url: "{{ route('vendor.inventory.store_inventory_with_variant') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response.message);
                    $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success");
                    setTimeout(function() {
                        window.location.href = response.location;
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    $.NotificationApp.send("Error", xhr.responseText, "top-right", "rgba(0,0,0,0.2)", "error");
                }
            });
        }
        });
    });





    function validateForm() {
        console.log("hello");
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
</script>
<script>
    $(document).ready(function() {
        $('#title').on('input', function() {
            var name = $(this).val();

            var slug = name.toLowerCase().replace(/\s+/g, '-');

            $('#slug').val(slug);
        });
    });
</script>
@endsection