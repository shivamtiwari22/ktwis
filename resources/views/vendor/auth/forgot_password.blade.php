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
                            <a href="javascript::void(0)">
                                <h3 class="text-white">Vendor</h3>
                                {{-- <span><img src="{{asset('public/assets/images/logo.png')}}" alt="" height="18"></span> --}}
                            </a>
                        </div>

                        <div class="card-body p-4">

                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">Forgot Password </h4>
                                {{-- <h4 class="text-dark-50 text-center pb-0 fw-bold">Verify Your Email </h4> --}}
                                <p class="text-muted mb-4">Enter your email address
                                </p>
                            </div>

                            <form id="login">
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <label for="floatingInput">Email address</label>
                                        <input type="email" name="email" id="email" class="form-control" id="floatingInput" placeholder="name@example.com" />
                                    </div>
                                </div>
                                <div class="d-grid mb-3 mb-0 text-center">
                                    <button class="btn btn-danger" type="submit"> Send </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            {{-- <p class="text-muted">Don't have an account? <a href="{{url('vendor/register')}}" class="text-muted ms-1"><b>Sign Up</b></a></p> --}}
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
                },
            })
        })
    </script>

    <script>
        $(function() {
            $(document).on('submit', '#login', function(e) {
                e.preventDefault();

                $('#loader').show();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url:"{{ route('vendor.forgot.email') }}",
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