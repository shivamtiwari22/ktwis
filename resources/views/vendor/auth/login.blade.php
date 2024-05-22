<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Vendor Login | Ktwis </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('public/assets/images/asset_10.png') }}">
    <!-- toastr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css"
        rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <!-- App css -->
    <link href="{{ asset('public/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
    <link href="{{ asset('public/assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style" />

</head>
<style>
    .toast-success {
        background-color: #28a745 !important;
        color: #fff !important;
    }

    .toast-error {
        background-color: #dc3545 !important;
        color: #fff !important;
    }

    .uil-eye {
        position: absolute;
        top: 46%;
        right: 15%;
        cursor: pointer;
        color: lightgray;
        font-size: 23px
    }

    .error {
        color: red;
    }

    .auth-fluid-right {
        background-image: url('{{ asset('public/vendor/side_img.jpg') }}');
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        position: relative;

    }

    .auth-fluid-right::before {
        content: "";
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        position: absolute;
        background-color: #000;
        opacity: 0.1;
    }
</style>

<body class="authentication-bg pb-0" data-layout-config='{"darkMode":false}'>

    <div class="auth-fluid">
        <!--Auth fluid left content -->
        <div class="auth-fluid-form-box">
            <div class="align-items-center d-flex h-100">
                <div class="card-body">

                    <!-- Logo -->
                    <div class="auth-brand text-center text-lg-start">
                        <a href="#" class="logo-dark">
                            <span><img src="{{ asset('public/assets/images/asset_9.png') }}" alt=""
                                    height="18"></span>
                        </a>

                        {{-- <a href="index.html" class="logo-light">
                            <span><img src="{{ asset('public/assets/images/logo.png') }}" alt=""
                                    height="18"></span>
                        </a> --}}
                        {{-- <h2 class="text-danger"><b>GSPARK</b> </h2> --}}
                    </div>

                    <!-- title-->
                    <h4 class="mt-0">Sign In</h4>
                    <p class="text-muted mb-4">Enter your email address and password to access account.</p>

                    <!-- form -->
                    <form action="#" id="loginForm">
                        <div class="mb-3">
                            <label for="emailaddress" class="form-label">Email address / Phone number<span
                                    class="text-danger">*</span></label>
                            <input class="form-control" type="text" id="emailaddress" name="email"
                                @if (isset($_COOKIE['email'])) value="{{ $_COOKIE['email'] }}" @endif
                                placeholder="Enter your email">

                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input class="form-control" type="password" name="password" id="password"
                                    placeholder="Enter your password"
                                    @if (isset($_COOKIE['password'])) value="{{ $_COOKIE['password'] }}" @endif>
                                <div class="input-group-text" data-password="false">
                                    <span class="password-eye"></span>
                                </div>
                            </div>
                            <label id="password-error" class="error" for="password"></label>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="remember_me"
                                        id="checkbox-signin">
                                    <label class="form-check-label" for="checkbox-signin">Remember
                                        me</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <a href="{{ route('vendor.forgot.password') }}"
                                    class="text-muted float-end"><small>Forgot
                                        your
                                        password?</small></a>
                            </div>
                        </div>
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-danger" type="submit"><i class="mdi mdi-login"></i> Log In
                            </button>
                        </div>
                        <!-- social-->
                        {{-- <div class="text-center mt-4">
                            <p class="text-muted font-16">Sign in with</p>
                            <ul class="social-list list-inline mt-3">
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);"
                                        class="social-list-item border-primary text-primary"><i
                                            class="mdi mdi-facebook"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);" class="social-list-item border-danger text-danger"><i
                                            class="mdi mdi-google"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);" class="social-list-item border-info text-info"><i
                                            class="mdi mdi-twitter"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);"
                                        class="social-list-item border-secondary text-secondary"><i
                                            class="mdi mdi-github"></i></a>
                                </li>
                            </ul>
                        </div> --}}
                    </form>
                    <!-- end form-->

                    <!-- Footer-->
                    <footer class="footer footer-alt">
                        <p class="text-muted">Don't have an account? <a href="{{ route('vendor.register') }}"
                                class="text-muted ms-1"><b>Sign Up</b></a></p>
                    </footer>

                </div> <!-- end .card-body -->
            </div> <!-- end .align-items-center.d-flex.h-100-->
        </div>
        <!-- end auth-fluid-form-box-->

        <!-- Auth fluid right content -->
        <div class="auth-fluid-right text-center ">
            <div class="auth-user-testimonial">
                {{-- <h2 class="mb-3">I love the color!</h2>
                <p class="lead"><i class="mdi mdi-format-quote-open"></i> It's a elegent templete. I love it very
                    much! . <i class="mdi mdi-format-quote-close"></i>
                </p>
                <p>
                    - {{ $userName }}
                </p> --}}
            </div> <!-- end auth-user-testimonial-->
        </div>
        <!-- end Auth fluid right content -->
    </div>
    <!-- end auth-fluid-->

    <!-- bundle -->
    <script src="{{ asset('public/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>

</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>

<script>
    $(document).ready(function() {
        $('#loginForm').validate({
            rules: {
                email: {
                    required: true,
                    maxlength: 256,
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 16,
                },

            },
        })

        $.validator.addMethod("specialChars", function(value, element) {
            return this.optional(element) || /[!@#$%^&*(),.?":{}|<>]/.test(value);
        }, "Please include at least one special character.");
    })
</script>


<script>
    var firebaseConfig = {
        apiKey: "AIzaSyDpzrHVoIgNR8Mf8bKvX7k1z-gfq-YRxL8",
        authDomain: "gspark-1bdae.firebaseapp.com",
        projectId: "gspark-1bdae",
        storageBucket: "gspark-1bdae.appspot.com",
        messagingSenderId: "231857528437",
        appId: "1:231857528437:web:ca6e08f9e7d06d0a43e23c",
        measurementId: "G-H2TM7V6RKX"
    };
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function token() {
        messaging
            .requestPermission()
            .then(function() {
                return messaging.getToken({vapidKey:"BNHvrfeViDLJC3PI29sLmjbi5rC9mDLmjUAochbHPdzjsnB_uMgCY-U0GkH0p0rTcHWPgIgAfILAYq7c9ctj1NY"})
            })
            .then(function(response) {
                console.log(response);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route('vendor.fcmToken') }}',
                    type: 'POST',
                    data: {
                        token: response
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        // alert('Token stored.');
                        console.log('Token Stored');
                    },
                    error: function(error) {
                        // alert(error);
                        console.log(error);
                    },
                });
            }).catch(function(error) {
                // alert(error);
                console.log(error);
            });

    }
    messaging.onMessage(function(payload) {
        const title = payload.notification.title;
        const options = {
            body: payload.notification.body,
            icon: 'public/assets/images/notify_icon.jpeg',
        };
        new Notification(title, options);
    });
</script>





<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('submit', '#loginForm', function(event) {
            event.preventDefault();
            // if (!validateForm()) {
            //     return;
            // }
            var email = $('#emailaddress').val().trim();
            var password = $('#password').val().trim();
            var checkbox = $('#checkbox-signin').prop('checked');

            var formData = {
                email: email,
                password: password,
                remember_me: checkbox
            };

            $.ajax({
                url: "{{ route('vendor.login_vendor') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        if (response.error) {
                            toastr.error(response.message, 'Error', {
                                class: 'toast-error',
                                timeOut: 2000,
                                closeButton: true
                            });
                            window.location.href = "{{ route('vendor.verification') }}";
                        } else {
                            toastr.clear();
                            toastr.success('Login successful!', 'Success', {
                                class: 'toast-success',
                                timeOut: 3000,
                                closeButton: true
                            });
                            token();
                            window.location.href = "{{ route('vendor.dashboard') }}";
                        }

                    } else {
                        toastr.clear();
                        toastr.error(response.message, 'Error', {
                            class: 'toast-error',
                            timeOut: 2000,
                            closeButton: true
                        });

                    }
                }
            });
        });

        function validateForm() {
            var isValid = true;

            $('.error-message').remove();

            var email = $('#emailaddress').val().trim();
            var password = $('#password').val().trim();

            if (email === '') {
                var errorMessage = 'Please enter your email address';
                $('#emailaddress').addClass('is-invalid');
                $('#emailaddress').after('<span class="error-message" style="color:red;">' + errorMessage +
                    '</span>');
                isValid = false;
            } else if (email.length < 6 || email.length > 255) {
                var errorMessage = '"Email must be between 6 and 255 characters"';
                $('#emailaddress').addClass('is-invalid');
                $('#emailaddress').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else {
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    var errorMessage = 'Please enter a valid email address';
                    $('#emailaddress').addClass('is-invalid');
                    $('#emailaddress').after('<span class="error-message" style="color:red;">' + errorMessage +
                        '</span>');
                    isValid = false;
                } else {
                    $('#emailaddress').removeClass('is-invalid');
                }
            }

            if (password === '') {
                var errorMessage = 'Please enter your password';
                $('#password').addClass('is-invalid');
                $('#password-error').append('<span class="error-message" style="color:red;">' + errorMessage +
                    '</span>');
                isValid = false;
            } else if (password.length < 8 || password.length > 15) {
                var errorMessage = 'Password must be between 8 and 15 characters';
                $('#password').addClass('is-invalid');
                $('#password-error').append('<span class="error-message" style="color:red;">' + errorMessage +
                    '</span>');
                isValid = false;
            } else {
                $('#password').removeClass('is-invalid');
            }

            $('#emailaddress, #password').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
                $('#password-error').remove();
            });

            return isValid;
        }
    });
</script>

@if (Session::has('message'))
    <script>
        $(document).ready(function() {
            $.NotificationApp.send("Success", '{{ Session::get('message') }}', "top-right",
                "rgba(0,0,0,0.2)", "success")
        });
    </script>
@endif

</html>
