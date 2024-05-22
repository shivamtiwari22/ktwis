@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
<title>Page</title>
@endsection


@section('css')
    <style>
        .red {
            color: red;
        }
        .card {
            margin-bottom : unset;
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
                        <p class="mt-3">Are You Sure to Delete this Vendor Application</p>
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.appereance.seo.pages') }}">Seo Page</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Page</li>
        </ol>
    </nav>
    <h2>Add <span class="badge badge-success-lighten">Page</span></h2>
    <hr>
    <form id="add_pages_form">
        @csrf
        <div class="row">
            <div class="col-sm-4">
                <label for="type">Type:<span class="red">*</span> </label>
                <select id="type" name="type" class="form-select">
                    <option value="" selected disabled>Select Type</option>
                    <option value="Faq">Faq</option>
                    <option value="Search">Search</option>
                    <option value="Wishlist">Wishlist</option>
                    <option value="Dashboard">Dashboard</option>
                    <option value="Order-Detail">Order Detail</option>
                    <option value="Contact-Seller">Contact Seller</option>
                    <option value="Raise-Dispute">Raise Dispute</option>
                    <option value="Cancel-Items">Cancel Items</option>
                    <option value="Dispute-Detail">Dispute Detail</option>
                    <option value="Cancel-Items">Cancel Items</option>
                    <option value="Vendor">Vendor</option>
                    <option value="Payment">Payment</option>
                    <option value="Order-Confirmed">Order Confirmed</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="meta_title">Meta Title: </label>
                <input type="text" id="meta_title" name="meta_title" class="form-control">
            </div>
            <div class="col-sm-4">
                <label for="meta_description">Meta Description:</label>
                <input type="text" id="meta_description" name="meta_description" class="form-control">
            </div>
         
        </div><br>

    

        <div class="row">
        
                <div class="col-sm-4">
                    <label for="key_features">Og Tag :</label>
                    <textarea name="ogtag" id="" class="form-control"></textarea>
                </div>
                <div class="col-sm-4">
                    <label for="description">Schema Markup :</label>
                    <textarea name="schema_markup" id="" class="form-control"></textarea>
                </div>
        </div><br>

        <div>
            <label for="content">Keywords:</label>
            <textarea  id="" cols="30" rows="10" name="keywords" class="form-control"></textarea>
        </div>
        <br>

      

        <div class="d-flex mt-2">
            <button type="submit" class="btn btn-success mx-auto"> Submit</button>
        </div><br>
    </form>
@endsection

@section('script')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#title').on('input', function() {
                var name = $(this).val();
                var slug = name.toLowerCase().replace(/\s+/g, '-');
                $('#slug').val(slug);
            });
        });
    </script>
 


    <script>
        $(document).ready(function() {
            $('#add_pages_form').submit(function(e) {
                e.preventDefault();

                if (validateForm_edit()) {
                    var form = $(this);
                    var formData = new FormData(form[0]);

                    $.ajax({
                        url: "{{ route('admin.appereance.seo.store_pages') }}",
                        method: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log(response);
                            if (response.status) {
                                $.NotificationApp.send("Success", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "success")
                                setTimeout(function() {
                                    window.location.href = response.location;
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error")
                            }
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = JSON.parse(xhr.responseText).message;
                            console.log(xhr.responseText);
                            $.NotificationApp.send("Error", errorMessage, "top-right",
                                "rgba(0,0,0,0.2)", "error");

                        }
                    });
                }
            });

            function validateForm_edit() {
                    var isValid = true;

                    $('.error-message').remove();

                $('#title, #type,#page_status').each(function() {
                    var input = $(this).attr('id');
                    var value = $.trim($(this).val());
                    if ($.trim($(this).val()) === '' ) {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    }  else {

                        $(this).removeClass('is-invalid');
                    }
                });


                $('#title, #type,#page_status,#content,#banner_image','#meta_title','#meta_description').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }

          
        });
    </script>
@endsection
