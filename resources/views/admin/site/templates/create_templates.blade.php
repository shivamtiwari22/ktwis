@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .red {
            color: red;
        }

        .card {
            margin-bottom: unset;
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
            <li class="breadcrumb-item"><a href="{{ route('admin.appereance.templates') }}">Email Templates</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Template</li>
        </ol>
    </nav>
    <h2>Add <span class="badge badge-success-lighten">Template</span></h2>
    <hr>
    <form id="add_pages_form">
        @csrf
        <div>
            <label for="title">Name:<span class="red">*</span></label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Name">
        </div><br>
        <div class="row">
            <div class="col-sm-4">
                <label for="type">Template Type:<span class="red">*</span></label>
                <select id="template_type" name="template_type" class="form-control">
                    <option value="">Select Type</option>
                    <option value="0">HTML</option>
                    <option value="1">Plain Text</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="type">Template For:<span class="red">*</span></label>
                <select id="template_for" name="template_for" class="form-control">
                    <option value="">Select Type</option>
                    <option value="0">Website</option>
                    <option value="1">Merchant</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="status">Status:<span class="red">*</span></label>
                <select id="template_status" name="template_status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div><br>

        <div class="row">
            <div class="col-sm-4">
                <label for="sender_email">Sender Email:<span class="red">*</span></label>
                <input type="email" id="sender_email" name="sender_email" class="form-control"
                    placeholder="Enter Sender Email">
            </div>
            <div class="col-sm-4">
                <label for="meta_title">Sender Name:<span class="red">*</span></label>
                <input type="text" id="sender_name" name="sender_name" class="form-control"
                    placeholder="Enter Sender Name">
            </div>
            <div class="col-sm-4">
                <label for="meta_title">Subject :<span class="red">*</span></label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="Subject">
            </div>
        </div><br>

        <div>
            <label for="meta_description">Short Codes:<span class="red">*</span></label>
            <textarea type="text" id="short_codes" name="short_codes" class="form-control"></textarea>
        </div><br>

        <div>
            <label for="content">Body:<span class="red"></span></label>
            <textarea id="content" name="content" class="form-control summernote"></textarea>
            <span id="note-error" style="color: red"></span>

        </div>
        <div class="d-flex">
            <button type="submit" class="btn btn-success mx-auto"> Submit</button>
        </div>
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
            $('#add_pages_form').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 36,
                    },
                    template_type: {
                        required: true,

                    },
                    template_for: {
                        required: true,
                    },
                    template_status: {
                        required: true,
                    },
                    sender_email: {
                        required: true,
                        maxlength: 36,
                        email: true,
                        emailWithDomain: true
                    },
                    sender_name: {
                        required: true,
                        maxlength: 26,

                    },
                    subject: {
                        required: true,
                        maxlength: 26,

                    },
                    short_codes: {
                        required: true,
                        maxlength: 40,

                    },
                    content: {
                        required: true,
                    },

                },
            });
            $.validator.addMethod("emailWithDomain", function(value, element) {
                return this.optional(element) || /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(value);
            }, "Please enter a valid email address.");
        })
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('submit', '#add_pages_form', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                if (validateForm_edit()) {
                    $.ajax({
                        url: "{{ route('admin.appereance.store_templates') }}",
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
                            console.log(xhr.responseText);
                            $.NotificationApp.send("Error", xhr.responseText, "top-right",
                                "rgba(0,0,0,0.2)", "error");

                        }
                    });
                }
            });
        });


        function validateForm_edit() {

            var isValid = true;

            // $('.error-message').remove();
            // var contentValue = $('.summernote').summernote('code');

            // if ($.trim(contentValue) === '' || contentValue === '<p><br></p>') {
            //     var errorMessage = 'This field is required';
            //     // $('#content').addClass('is-invalid');
            //     // $('#content').after('<span class="error-message" style="color:red;">' + errorMessage +
            //     //     '</span>');
            //     $('#note-error').text(errorMessage);
            //     isValid = false;
            //     console.log('yes');
            // } else {
            //     $('#content').removeClass('is-invalid');
            //     $('#note-error').text('');
            //     console.log('no');

            // }

            return isValid;


        }
    </script>
@endsection
