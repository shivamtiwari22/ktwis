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
            <li class="breadcrumb-item"><a href="{{ route('admin.appereance.pages') }}">Page</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Page</li>
        </ol>
    </nav>
    <h2>Add <span class="badge badge-success-lighten">Page</span></h2>
    <hr>
    <form id="add_pages_form">
        @csrf
        <div class="row">
            <div class="col-sm-4">
                <label for="title">Title:<span class="red">*</span></label>
                <input type="text" id="title" name="title" class="form-control">
            </div>
            <div class="col-sm-4">
                <label for="type">Type:<span class="red">*</span> </label>
                <select id="type" name="type" class="form-control">
                    <option value="">Select Type</option>
                    <option value="Privacy Policy">Privacy Policy</option>
                    <option value="Terms & Conditions For Customers">Terms & Conditions For Customers</option>
                    <option value="Terms & Conditions For Merchants">Terms & Conditions For Merchants</option>
                    <option value="Return and Refund Policy">Return and Refund Policy</option>
                    <option value="Shipping Policy">Shipping Policy</option>
                    <option value="About Us">About Us</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="banner_image">Banner Image<span class="red">*</span> (JPG, JPEG, PNG, 2MB
                    max) </label>
                <input type="file" id="banner_image" name="banner_image" class="form-control" accept="image/*">
            </div>
        </div><br>

        <div class="row">
            <div class="col-sm-4">
                <label for="status">Status:<span class="red">*</span> </label>
                <select id="page_status" name="page_status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
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
            <label for="slug">Slug:</label>
            <input type="text" id="slug" name="slug" readonly class="form-control">
            </div>
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
            <input type="text" id="" name="keywords" class="form-control">
        </div>
        <br>

        <div>
            <label for="content">Content:<span class="red">*</span></label>
            <textarea id="content" name="content" class="form-control summernote"></textarea>
            <span id="note-error"  style="color: red"></span>

        </div>

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
    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200
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
                        url: "{{ route('admin.appereance.store_pages') }}",
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


                $('#banner_image').each(function() {
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
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    } else {
                        var errorMessage = 'Please select an image';
                        $input.addClass('is-invalid');
                        $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    }
                })


                var contentValue = $('.summernote').summernote('code');
                if ($.trim(contentValue) === '' || contentValue === '<p><br></p>') {
                    var errorMessage = 'This field is required';
                    // $('#content').addClass('is-invalid');
                    // $('#content').after('<span class="error-message" style="color:red;">' + errorMessage +
                    //     '</span>');
                        $('#note-error').text(errorMessage 
                        );
                    isValid = false;
                } else {
                    $('#content').removeClass('is-invalid');
                    $('#note-error').text('');
                }


                // $('#meta_title,#meta_description').each(function() {
                //     var value = $.trim($(this).val());
                //     // console.log(value);
                //     if (value.length < 4 || value.length > 26 ) {
                //          if(value != ''){
                //             var errorMessage = 'This filed must be between 4 and 26 characters.';
                //             $(this).addClass('is-invalid');
                //             $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                //                 '</span>');
                //             isValid = false;
                //     }
                //     // isValid = false;
                        
                //     } else {

                //         $(this).removeClass('is-invalid');
                //     }
                
                // });


                $('#title, #type,#page_status,#content,#banner_image','#meta_title','#meta_description').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }

          
        });
    </script>
@endsection
