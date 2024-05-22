<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Log In | Ecommerce Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('public/assets/images/asset_10.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Your custom CSS file (if you have any) -->
    <link rel="stylesheet" href="styles.css">
    <!-- App css -->
    <link href="{{ asset('public/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
    <link href="{{ asset('public/assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style" />

</head>
<style>
    .red {
        color: red;
    }

    .error {
        color: red;
    }

    .loader {
        position: fixed;
        z-index: 9999;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100px;
        height: 100px;
        display: none;
        border-radius: 50%;
    }

    .loader .spinner-border {
        position: absolute;
        top: 40%;
        left: 50%;
    }

    .otp-input {
        margin-right: 10px;
        width: 40px;
    }
    .password-container {
  position: relative;
}

.toggle-password{
    position: relative;
    top:30px;
    left: 61%;
}
.toggle-passwordes{
    position: relative;
    top:30px;
    left: 56%;
}

#new_pass[type="password"] {
  font-family: "fontawesome"; /* To avoid password dots showing */
}

.authentication-bg {
         background-image: url('{{ asset('public/vendor/side_img.jpg') }}') !important;
         background-position:center;
         background-repeat: no-repeat;
        background-size: cover;
    }


</style>

<body class="loading authentication-bg" data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header text-center bg-danger">
                            <a href="index.html">
                                <h3 class="text-white">Vendor </h3>
                                {{-- <span><img src="{{asset('public/assets/images/logo.png')}}" alt="" height="18"></span> --}}
                            </a>
                        </div>

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">New Password </h4>
                                <p class="text-muted mb-4">
                                </p>
                            </div>

                            <form id="login">
                                <div class="mb-3">
                                    <input type="hidden" name="email" id="email" value="{{$email}}">
                                    <div class="mb-3">
                                        <label for="floatingInput ">New Password <span class="text-danger">*</span></label>
                                        <i class="toggle-password fas fa-eye " onclick="togglePasswordVisibility()"></i>
                                        <input type="password" name="new_pass" id="new_pass" class="form-control"  />
                                    </div>
                                    <div class="mb-3">
                                        <label for="floatingInput">Confirm Password <span class="text-danger">*</span></label>
                                        <i class="toggle-passwordes fas fa-eye " onclick="togglePasswordVisibilitys()"></i>
                                        <input type="password" name="confirm_pass" id="confirm_pass" class="form-control"  />
                                    </div>
                                </div>
                                <div class="d-grid mb-3 mb-0 text-center">
                                    <button class="btn btn-danger" type="submit">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->
    <div id="loader" class="loader">
        <div class="spinner-border avatar-lg text-primary" role="status"></div>
    </div>
    <footer class="footer footer-alt">
    </footer>

    <!-- bundle -->
    <script src="{{ asset('public/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        function togglePasswordVisibilitys() {
          const passwordInput = document.getElementById("confirm_pass");
          const eyeIcon = document.querySelector(".toggle-passwordes");
        
          if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
          } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
          }
        }
        </script>
    <script>
        function togglePasswordVisibility() {
          const passwordInput = document.getElementById("new_pass");
          const eyeIcon = document.querySelector(".toggle-password");
        
          if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
          } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
          }
        }
        </script>
        
    <script>
        $(document).ready(function() {
            $('#login').validate({
                rules: {
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
        });
    </script>

    <script>
        $(function() {
            $(document).on('submit', '#login', function(e) {
                e.preventDefault();

                $('#loader').show();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.new_password.store') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                    error: function(jqXHR, exception) {},
                    complete: function() {
                        $('#loader').hide();
                    }
                });
            })
        });
    </script>


</body>

</html>