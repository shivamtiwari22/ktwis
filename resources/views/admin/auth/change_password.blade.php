@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection


@section('main_content')

<body>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- toastr -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Font Awesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

    <style>
        .red {
            color: red;
        }

        .card {
            max-width: 400px;
            margin: 0 auto;
        }

        .password-container {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }

        .password-toggle i {
            font-size: 20px;
        }
    </style>

</body>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Change Password</li>
    </ol>
</nav>

<div class="card p-4 mt-4">
    <h2>Change <span class="badge badge-success-lighten">Password</span></h2>
    <hr>
    <form action="#" id="profileForm" enctype="multipart/form-data" method="post">
        <input type="hidden" name="id" id="id" value="{{ $user->id }}">
        <label for="fullname" class="form-label">Old Password :<span class="red">*</span></label>
        <div class="password-container">
            <input class="form-control" type="password" id="old_pass" value="" name="old_pass" placeholder="Old Password">
            <span class="password-toggle"  onclick="togglePasswordVisibility('old_pass')">
                <i class="fa fa-eye-slash" id="old_pass_toggle"></i>
            </span>
        </div>
        <br>

        <label for="emailaddress" class="form-label">New Password :<span class="red">*</span></label>
        <div class="password-container">
            <input class="form-control" type="password" id="new_pass" name="new_pass" placeholder="New Password">
            <span class="password-toggle"  onclick="togglePasswordVisibility('new_pass')">
                <i class="fa fa-eye-slash" id="new_pass_toggle"></i>
            </span>
        </div>
        <br>

        <label for="emailaddress" class="form-label">Confirm Password :<span class="red">*</span></label>
        <div class="password-container">
            <input class="form-control" type="password" id="confirm_pass" name="confirm_pass" placeholder="Confirm Password">
            <span class="password-toggle" onclick="togglePasswordVisibility('confirm_pass')">
                <i class="fa fa-eye-slash"  id="confirm_pass_toggle"></i>

            </span>
        </div>
        <br>

        <div class="d-flex text-center justify-content-end">
            <button class="btn btn-primary mx-auto" type="submit"><i class="mdi mdi-login"></i> Update Password </button>
        </div>
    </form>
</div>
@endsection


@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(function() {
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#profileForm').validate({
                rules: {
                    old_pass: {
                        required: true
                    },
                    new_pass: {
                        required: true,
                        minlength: 8,
                        maxlength: 16,
                        containsSpecialCharacter: true
                    },
                    confirm_pass: {
                        required: true,
                        equalTo: "#new_pass",
                        maxlength: 16,
                        containsSpecialCharacter: true
                    }
                },
                messages: {
                    old_pass: {
                        required: "Please enter your old password."
                    },
                    new_pass: {
                        required: "Please enter your new password.",
                        minlength: "Password must be at least 8 characters long.",
                        maxlength: "Password cannot exceed 16 characters.",
                        containsSpecialCharacter: "Password must contain at least one special character."
                    },
                    confirm_pass: {
                        required: "Please confirm your new password.",
                        equalTo: "Passwords do not match.",
                        maxlength: "Password cannot exceed 16 characters.",
                        containsSpecialCharacter: "Password must contain at least one special character."
                    }
                },
                submitHandler: function(form) {
                    var formData = new FormData(form);

                    $.ajax({
                        url: "{{ route('admin.change.password.store') }}",
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            console.log(response.message);
                            // console.log(response.location);
                            if (response.status) {
                                $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success");
                                setTimeout(function() {
                                    window.location.href = response.location;
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", response.message, "top-right", "rgba(0,0,0,0.2)", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            var errorMessage = JSON.parse(xhr.responseText).message;
                            console.log(errorMessage);
                            $.NotificationApp.send("Error", errorMessage, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                    });
                }
            });

            $.validator.addMethod("containsSpecialCharacter", function(value, element) {
                return this.optional(element) || /[^A-Za-z0-9]/.test(value);
            }, "Password must contain at least one special character.");

            $('#new_pass, #confirm_pass').on('input', function() {
                var maxLength = 16;
                if ($(this).val().length > maxLength) {
                    $(this).val($(this).val().slice(0, maxLength));
                }
            });
        })
    });
</script>
<script>
    function togglePasswordVisibility(inputId) {
        var input = document.getElementById(inputId);
        var toggleIcon = document.getElementById(inputId + '_toggle');
        if (input.type === 'password') {
            input.type = 'text';
            toggleIcon.classList.add('fa-eye');
            toggleIcon.classList.remove('fa-eye-slash');
        } else {
            input.type = 'password';
            toggleIcon.classList.add('fa-eye-slash');
            toggleIcon.classList.remove('fa-eye');
        }
    }
</script>

@endsection