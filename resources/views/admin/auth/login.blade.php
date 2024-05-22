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
</style>

<body class="loading authentication-bg"
    data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">

                        <!-- Logo -->
                        <div class="card-header text-center bg-primary">
                            <a href="#">
                                <h3 class="text-white">Admin</h3>
                                <span><img src="{{ asset('public/assets/images/asset_9.png') }}" alt="" height="18"></span>
                            </a>
                        </div>

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">Sign In </h4>
                                <p class="text-muted mb-4">Enter your email address and password to access admin panel.
                                </p>
                            </div>

                            <form id="login">

                                <div class="mb-3">
                                    <span for="emailaddress" class="form-label">Email address<span
                                            class="red">*</span></span>
                                    <input class="form-control" type="email" name="email" id="emailaddress"
                                        required="" placeholder="Enter your email"
                                        @if (isset($_COOKIE['email'])) value="{{ $_COOKIE['email'] }}" @endif>
                                </div>

                                <div class="mb-3">
                                    <span for="" class="form-label">Password<span class="red">*</span></span>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" name="password" class="form-control"
                                            placeholder="Enter your password"
                                            @if (isset($_COOKIE['password'])) value="{{ $_COOKIE['password'] }}" @endif>
                                        <div class="input-group-text" data-password="false">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div>
                                        <label id="password-error" class="error" for="password"></label>
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
                                            <a href="{{route('admin.forgot.password')}}" class="text-muted float-end"><small>Forgot
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 mb-0 text-center">
                                    <button class="btn btn-primary btn-sm" type="submit"> Log In </button>
                                </div>

                            </form>
                        </div> <!-- end card-body -->
                    </div>
                    <!-- end card -->

                    {{-- <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-muted">Don't have an account? <a href="pages-register.html"
                                    class="text-muted ms-1"><b>Sign Up</b></a></p>
                        </div> <!-- end col -->
                    </div> --}}
                    <!-- end row -->

                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt">
        2018 - 2021 © Hyper - Coderthemes.com
    </footer>

    <!-- bundle -->
    <script src="{{ asset('public/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#login').validate({
                rules: {
                    email: {
                        required: true,
                        rangelength: [2, 256],
                        maxlength: 256,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 4,
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
        $(function() {
            $(document).on('submit', '#login', function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('admin.login_submit') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        // console.log(result);
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
                    error: function(jqXHR, exception) {}
                });
            })
        });
    </script>

@if (Session::has('message'))
<script>
    $(document).ready(function() {  
        $.NotificationApp.send("Success",'{{ Session::get('message') }}', "top-right",
                                "rgba(0,0,0,0.2)", "success")
    });
</script>
@endif


</body>

</html>
