<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Log In | Ecommerce Admin Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('public/assets/images/favicon.ico')}}">
        <!-- App css -->
        <link href="{{asset('public/assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('public/assets/css/app.min.css')}}" rel="stylesheet" type="text/css" id="light-style" />
        <link href="{{asset('public/assets/css/app-dark.min.css')}}" rel="stylesheet" type="text/css" id="dark-style" />

    </head>

    <body class="loading authentication-bg" data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
        <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xxl-4 col-lg-5">
                        <div class="card">

                            <!-- Logo -->
                            <div class="card-header text-center bg-primary">
                                <a href="index.html">
                                    <h3 class="text-white">Reset Password</h3>
                                </a>
                            </div>

                            <div class="card-body p-4">
                                
                                <form method="post" action="{{route('user.reset.password')}}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{$user->id}}">
                                    <div class="mb-3">
                                        <label for="emailaddress" class="form-label">New Password</label>
                                        <input class="form-control" type="password" name="password" id="emailaddress" required="" placeholder="Enter your new password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Confirm Password</label>
                                        <div class="input-group input-group-merge">
                                            <input type="password" id="password" name="password_confirmation" class="form-control" placeholder="Confirm your new password">
                                            <div class="input-group-text" data-password="false">
                                                <span class="password-eye"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 mb-0 text-center">
                                        <button class="btn btn-primary" type="submit"> Submit </button>
                                    </div>
                                </form>


                            </div> <!-- end card-body -->
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                {{-- <p class="text-muted">Don't have an account? <a href="pages-register.html" class="text-muted ms-1"><b>Sign Up</b></a></p> --}}
                            </div> <!-- end col -->
                        </div>
                        <!-- end row -->

                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->

        {{-- <footer class="footer footer-alt">
            2018 - 2021 © Hyper - Coderthemes.com
        </footer> --}}

        <!-- bundle -->
        <script src="{{asset('public/assets/js/vendor.min.js')}}"></script>
        <script src="{{asset('public/assets/js/app.min.js')}}"></script>



        {{-- <script>

            $(function () {
                $('#login').on('submit', function(e){
                    e.preventDefault();
                    let fd = new FormData(this);
                    fd.append('_token',"{{ csrf_token() }}");
                    $.ajax({
                        url: "{{ route('admin.login_submit') }}",
                        type:"POST",
                        data: fd,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        success:function(result){
                            console.log(result.location);


                            if(result.status)
                            {
                                $.NotificationApp.send("Success",result.msg,"top-right","rgba(0,0,0,0.2)","success")
                                setTimeout(function(){
                                    window.location.href = result.location;
                                }, 1000);
                            }
                            else
                            {
                                $.NotificationApp.send("Error",result.msg,"top-right","rgba(0,0,0,0.2)","error")
                            }
                        },
                        error: function(jqXHR, exception) {
                        }
                    });
                })
            });
        </script> --}}

        
    </body>
</html>
