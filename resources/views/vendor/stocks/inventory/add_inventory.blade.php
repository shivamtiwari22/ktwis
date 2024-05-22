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
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.inventory.index') }}">Inventory</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Inventory</li>
        </ol>
    </nav>
    <div class="form-control ">
        <form id="add_inventory" class="mb-3" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-sm-4">
                    <label for="featured_image">Image (jpg, jpeg, png, 2MB max) <span class="text-danger">*</span></label>
                    <input type="hidden" value="{{ $p_id }}" name="p_id" required>
                    <input type="file" accept="image/*" name="featured_image" required id="featured_image"
                        class="form-control">
                </div>
                <div class="col-sm-2"></div>
                <div class="col-sm-6">
                    <div id="imageContainer"></div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-4">
                    <label for="name">SKU <span class="text-danger">*</span></label>
                    <input type="text" name="sku" id="sku" required class="form-control">
                </div>
                <div class="col-sm-4">
                    <label for="stock_qty">Stock Qty <span class="text-danger">*</span></label>
                    <input type="number" name="stock_qty" id="stock_qty" min="1" required class="form-control">
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-sm-3">
                    <label for="purchase_price">Purchase Price <span class="text-danger">*</span></label>
                    <input type="number" name="purchase_price" id="purchase_price" min="1" required class="form-control">
                </div>
                <div class="col-sm-3">
                    <label for="price">Price <span class="text-danger">*</span></label>
                    <input type="number" name="price" id="price" required min="1" class="form-control">
                </div>
                <div class="col-sm-3">
                    <label for="offer_price">Offer Price <span class="text-danger">*</span></label>
                    <input type="number" name="offer_price" id="offer_price" min="1" required class="form-control">
                </div>
                <div class="col-sm-3">
                    <button type="submit" id="save_inventory" class="btn btn-primary"
                        style="float: right;margin-top: 20px;">Save</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#save_inventory').click(function() {
                event.preventDefault();

                if (validateForm()) {
                    var formData = new FormData($('#add_inventory')[0]);
                    $.ajax({
                        url: "{{ route('vendor.inventory.store_inventory') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log(response);
                            toastr.clear();
                            toastr.success(response.message, 'Success', {
                                class: 'toast-success',
                                timeOut: 3000,
                                closeButton: true
                            });
                            setTimeout(function() {
                                window.location.href = response.location;
                            }, 1000);
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            toastr.clear();
                            toastr.error(error, 'Error', {
                                timeOut: 3000,
                                closeButton: true
                            });
                        }
                    });
                }
            });

            function validateForm() {
                var isValid = true;

                // Reset the error messages
                $('.error-message').remove();

                // Validate each form field
                $('#featured_image,#sku, #stock_qty, #purchase_price, #price, #offer_price').each(function() {
                    var $input = $(this);
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } 
                    if ($input.attr('id') === 'featured_image') {
                        var file = $input[0].files[0]; // Get the selected file
                        if (file) {
                            var allowedExtensions = ['png', 'jpg', 'jpeg'];
                            var maxFileSize = 2 * 1024 * 1024; // 2MB in bytes

                            var fileExtension = file.name.split('.').pop().toLowerCase();
                            if (allowedExtensions.indexOf(fileExtension) === -1) {
                                var errorMessage = 'Only PNG, JPEG, and JPG files are allowed';
                                $input.addClass('is-invalid');
                                $input.after('<span class="error-message" style="color:red;">' +
                                    errorMessage + '</span>');
                                isValid = false;
                            } else if (file.size > maxFileSize) {
                                var errorMessage = 'File size cannot exceed 2MB';
                                $input.addClass('is-invalid');
                                $input.after('<span class="error-message" style="color:red;">' +
                                    errorMessage + '</span>');
                                isValid = false;
                            }
                        }
                    }
                    else if ($input.attr('id') !== 'sku' && $input.attr('id') !== 'featured_image' && parseFloat($input.val()) < 0) {
                        var errorMessage = 'Value cannot be negative';
                        $input.addClass('is-invalid');
                        $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } 
                    else {
                        $(this).removeClass('is-invalid');
                    }

                 
                });

                // Remove error message on input or change event
                $('#featured_image, #sku, #stock_qty, #purchase_price, #price, #offer_price').on(
                    'input change',
                    function() {
                        $(this).removeClass('is-invalid');
                        $(this).next('.error-message').remove();
                    });

                return isValid;
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#featured_image').change(function(e) {
                e.preventDefault();
                var formData = new FormData($('#add_inventory')[0]);
                $.ajax({
                    url: "{{ route('vendor.inventory.add.view_image') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#imageContainer').html('<img src="' + response.imageDataUrl +
                                '" width="55px" style="border:1px solid black;">');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
