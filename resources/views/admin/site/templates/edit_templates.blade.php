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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.appereance.templates') }}">Templates</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Template</li>
        </ol>
    </nav>
    <h2>Edit <span class="badge badge-success-lighten">Template</span></h2>
    <hr>
    <form id="add_pages_form">
        @csrf
        <div>
            <label for="title">Name:<span class="red">*</span></label>
            <input type="hidden" id="id" name="id" class="form-control" placeholder="Name"
                value="{{ $template->id }}">
            <input type="text" id="name" name="name" class="form-control" placeholder="Name"
                value="{{ $template->name }}">
        </div><br>
        <div class="row">
            <div class="col-sm-4">
                <label for="type">Template Type:<span class="red">*</span></label>
                <select id="template_type" name="template_type" class="form-control">
                    <option value="">Select Type</option>
                    <option value="0" {{ $template->template_type === '0' ? 'selected' : '' }}>HTML</option>
                    <option value="1" {{ $template->template_type === '1' ? 'selected' : '' }}>Plain Text</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="type">Template For:<span class="red">*</span></label>
                <select id="template_for" name="template_for" class="form-control">
                    <option value="">Select Type</option>
                    <option value="0" {{ $template->template_for === '0' ? 'selected' : '' }}>Website</option>
                    <option value="1" {{ $template->template_for === '1' ? 'selected' : '' }}>Merchant</option>
                </select>
            </div>
            <div class="col-sm-4">
                <label for="status">Status:<span class="red">*</span></label>
                <select id="template_status" name="template_status" class="form-control">
                    <option value="">Select Status</option>
                    <option value="active" {{ $template->status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $template->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div><br>

        <div class="row">
            <div class="col-sm-4">
                <label for="sender_email">Sender Email:<span class="red">*</span></label>
                <input type="email" id="sender_email" name="sender_email" class="form-control"
                    placeholder="Enter Sender Email" value="{{ $template->sender_email }}">
            </div>
            <div class="col-sm-4">
                <label for="meta_title">Sender Name:<span class="red">*</span></label>
                <input type="text" id="sender_name" name="sender_name" class="form-control"
                    placeholder="Enter Sender Name" value="{{ $template->sender_name }}">
            </div>
            <div class="col-sm-4">
                <label for="meta_title">Subject :<span class="red">*</span></label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="Subject"
                    value="{{ $template->subject }}">
            </div>
        </div><br>

        <div>
            <label for="meta_description">Short Codes:<span class="red">*</span></label>
            <textarea type="text" id="short_codes" name="short_codes" class="form-control">{{ $template->short_codes }}</textarea>
        </div><br>

        <div>
            <label for="content">Body:</label>
            <textarea id="content" name="content" class="form-control summernote">{{ $template->body }}</textarea>
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
                            maxlength: 40,
                            emailWithDomain: true,
                            email: true,

                        },
                        sender_name: {
                            required: true,
                            maxlength: 256,

                        },
                        subject: {
                            required: true,
                            maxlength: 256,

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

                    $.ajax({
                        url: "{{ route('admin.appereance.update_templates') }}",
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
                
            });

            function validateForm_edit() {
                var isValid = true;

                $('.error-message').remove();

                // $('#name, #template_type,#template_for,#template_status,#sender_email,#sender_name,#subject,#short_codes')
                //     .each(function() {
                //         if ($.trim($(this).val()) === '') {
                //             var errorMessage = 'This field is required';
                //             $(this).addClass('is-invalid');
                //             $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                //                 '</span>');
                //             isValid = false;
                //         } else {
                //             $(this).removeClass('is-invalid');
                //         }
                //     });

                var contentValue = $('.summernote').summernote('code');
                if ($.trim(contentValue) === '' || contentValue === '<p><br></p>') {
                    var errorMessage = 'This field is required';
                    $('#content').addClass('is-invalid');
                    $('.note-frame').after('<span class="error-message" style="color:red;">' + errorMessage +
                        '</span>');
                    isValid = false;
                } else {
                    $('#content').removeClass('is-invalid');
                }

                    // $('#name, #template_type,#template_for,#template_status,#sender_email,#sender_name,#subject,#short_codes,#content')
                    //     .on('input change', function() {
                    //         $(this).removeClass('is-invalid');
                    //         $(this).next('.error-message').remove();
                    //     });

                return isValid;
            }
        });
    </script>
@endsection
