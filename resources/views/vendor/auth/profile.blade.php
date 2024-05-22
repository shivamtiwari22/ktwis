@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection


@section('main_content')

    <body>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <!-- toastr -->
        {{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <style>
            .red {
                color: red;
            }
        </style>
    </body>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
        </ol>
    </nav>

    <div class="card p-4 mt-4">
        <h2>Edit <span class="badge badge-success-lighten">Profile</span></h2>
        <hr>
        <form action="#" id="profileForm" enctype="multipart/form-data" method="post">
            <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">



            <div class="row">
                <div class="col-sm-6">
                    @if ($user->profile_pic)
                        <img src="{{ asset(asset('public/vendor/profile_pic/' . $user->profile_pic)) }}" width="100px"
                            height="100px" class="mb-2" alt="Profile"> <br>
                    @else
                        <img src="{{ asset(asset('public/assets/images/users/avatar-1.jpg')) }}" width="50px"
                            height="60px" class="mb-2" alt="Profile"> <br>
                    @endif
                    <label for="fullname" class="form-label">Profile Image</label> <span>(JPG, JPEG, PNG, 2MB max)</span>
                    <input type="file" name="profile_pic" id="profile_pic" class="form-control">
                </div>
                <div class="col-sm-6">

                    <label for="fullname" class="form-label ">Full Name <span class="text-danger">*</span></label>
                    <input class="form-control" type="text" id="fullname" value="{{ $user->name }}" name="name"
                        placeholder="Enter your full name">

                    <div>
                        <label for="emailaddress" class="form-label mt-2">Email address</label>
                        <input class="form-control" type="email" readonly id="emailaddress" value="{{ $user->email }}"
                            placeholder="Enter your email">
                    </div>

                    <div>
                        <label for="tax" class="form-label mt-2">Mobile Number <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter your mobile number"
                            name="mobile_number" value="{{ $user->mobile_number }}">
                    </div>

                    <label for="tax_number" class="form-label mt-2">DOB <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="dob" max="{{ date('Y-m-d') }}"
                        value="{{ $user->dob }}">
                </div>
            </div>

            <div class="d-flex  text-center">
                <button class="btn btn-primary" type="submit"><i class="mdi mdi-login"></i> Update </button>
            </div>
        </form>
    </div>



    </div>


    <script>
        $(document).ready(function() {


            var today = new Date();
            var dd = today.getDate();
            var mm = today.getMonth() + 1; // January is 0!
            var yyyy = today.getFullYear() - 8;
            if (dd < 10) {
                dd = '0' + dd;
            }
            if (mm < 10) {
                mm = '0' + mm;
            }
            today = yyyy + '-' + mm + '-' + dd;

            console.log(today);
            // Set the "max" attribute of the date input to today's date
            $("#dob").attr("max", today);

           
            $('#profileForm').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 35,
                    },
                    mobile_number: {
                        required: true,
                        number: true,
                        maxlength: 10,
                    },
                    dob: {
                        required: true,
                    },
                    profile_pic: {
                        imageFormat: true,
                        filesize: 2024
                    }

                },
                messages: {
                    profile_pic: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    }
                },
            })
            $.validator.addMethod("specialChars", function(value, element) {
                return this.optional(element) || /[!@#$%^&*(),.?":{}|<>]/.test(value);
            }, "Please include at least one special character.");
            $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");
        })
    </script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).on('submit', '#profileForm', function(event) {
                event.preventDefault();

                var form = $(this);
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('vendor.profile.update') }}",
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // console.log(response);

                        if (response.status) {
                            $('#fullname').val(response.name);
                            $('#emailaddress').val(response.email);


                            toastr.clear();
                            toastr.success('Profile Update successfully!', 'Success', {
                                class: 'toast-success',
                                timeOut: 3000,
                                closeButton: true
                            });
                            window.location.href = "{{ route('vendor.dashboard') }}";
                     
                        } else {
                            toastr.error(response.msg, 'Error', {
                                class: 'toast-success',
                                timeOut: 3000,
                                closeButton: true
                            });

                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.clear();
                        var errorMessage = JSON.parse(xhr.responseText).msg;
                        toastr.error(errorMessage, 'Error', {
                            timeOut: 3000,
                            closeButton: true
                        });
                        console.log(JSON.parse(xhr.responseText).msg);
                        console.error(error);

                    }
                });

            });

            function validateForm_edit() {
                var isValid = true;

                $('.error-message').remove();

                $('#fullname, #emailaddress, #tax_type, #tax_pin').each(function() {
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');

                        // Validate field length
                        if ($(this).attr('id') === 'fullname') {
                            var name = $.trim($(this).val());
                            if (name.length > 256) {
                                var errorMessage = 'Name must not exceed 256 characters';
                                $(this).addClass('is-invalid');
                                $(this).after('<span class="error-message" style="color:red;">' +
                                    errorMessage +
                                    '</span>');
                                isValid = false;
                            }
                        } else if ($(this).attr('id') === 'emailaddress') {
                            var email = $.trim($(this).val());
                            if (email.length > 256) {
                                var errorMessage = 'Email must not exceed 256 characters';
                                $(this).addClass('is-invalid');
                                $(this).after('<span class="error-message" style="color:red;">' +
                                    errorMessage +
                                    '</span>');
                                isValid = false;
                            }
                        } else if ($(this).attr('id') === 'tax_pin') {
                            var taxPin = $.trim($(this).val());
                            if (taxPin.length > 256) {
                                var errorMessage = 'Tax Pin must not exceed 256 characters';
                                $(this).addClass('is-invalid');
                                $(this).after('<span class="error-message" style="color:red;">' +
                                    errorMessage +
                                    '</span>');
                                isValid = false;
                            }
                        }
                    }
                });

                $('#fullname, #emailaddress, #tax_type, #tax_pin').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }

        });
    </script>
    <script>
        document.getElementById('fullname').addEventListener('input', function() {
            var maxLength = 256;
            var input = this.value;

            if (input.length > maxLength) {
                this.value = input.slice(0, maxLength);
                document.getElementById('validationMessage').textContent =
                    '*Fullname cannot exceed 256 characters.';
            } else {
                document.getElementById('validationMessage').textContent = '';
            }
        });
    </script>
    <script>
        document.getElementById('tax_pin').addEventListener('input', function() {
            var maxLength = 256;
            var input = this.value;

            if (input.length > maxLength) {
                this.value = input.slice(0, maxLength);
                document.getElementById('taxPinValidationMessage').textContent =
                    '*Tax PIN cannot exceed 256 characters.';
            } else {
                document.getElementById('taxPinValidationMessage').textContent = '';
            }
        });
    </script>
@endsection


@section('script')
@endsection
