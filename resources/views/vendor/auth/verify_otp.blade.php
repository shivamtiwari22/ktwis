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

    .otp-input {
        margin-right: 10px;
        width: 40px;
    }

    .authentication-bg {
        background-image: url('{{ asset('public/vendor/side_img.jpg') }}') !important;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
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
                        <div class="card-header text-center bg-danger">
                            <a href="javascript::void(0)">
                                <h3 class="text-white">Vendor</h3>
                                {{-- <span><img src="{{asset('public/assets/images/logo.png')}}" alt="" height="18"></span> --}}
                            </a>
                        </div>

                        <div class="card-body p-4">
                            <div class="text-center w-75 m-auto">
                                <h4 class="text-dark-50 text-center pb-0 fw-bold">Verify One Time Password</h4>
                                <p class="text-muted mb-4">OTP will expire in: <span id="countdownTimer">02:00</span>
                                </p>
                                <p id="otpExpiredMessage" class="red error" style="display: none;">One Time Password has
                                    expired.</p>
                            </div>
                            <form id="login">
                                <div class="mb-3">
                                    <input type="hidden" name="email" id="email" value="{{ $email }}">
                                    <input type="hidden" name="otp_created_at" value="{{ $otp_created_at }}">
                                    <div class="d-flex justify-content-center">
                                        <input type="text" name="otp1" id="otp1"
                                            class="form-control otp-input" maxlength="1" placeholder="0" />
                                        <input type="text" name="otp2" id="otp2"
                                            class="form-control otp-input" maxlength="1" placeholder="0" />
                                        <input type="text" name="otp3" id="otp3"
                                            class="form-control otp-input" maxlength="1" placeholder="0" />
                                        <input type="text" name="otp4" id="otp4"
                                            class="form-control otp-input" maxlength="1" placeholder="0" />
                                    </div>
                                </div>
                                <div class=" mb-3 mb-0 text-center">
                                    <button class="btn btn-danger" type="submit">Verify</button>
                                </div>
                                <div class="validation-error text-center"></div>
                                <div class=" text-center">
                                    <a href="javascript::void(0)" id="resend-otp" style="display: none">Resend One Time
                                        Password</a>
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
        const inputs = document.querySelectorAll('input');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (event) => {
                // If the input value is not empty
                if (input.value.trim() !== '') {
                    if (index + 1 < inputs.length) {
                        inputs[index + 1].focus();
                    }
                }
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Backspace' && input.value.trim() === '') {
                    if (index - 1 >= 0) {
                        inputs[index - 1].focus();
                    }
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#login').validate({
                rules: {
                    otp1: {
                        required: true,
                        digits: true,
                        maxlength: 1
                    },
                    otp2: {
                        required: true,
                        digits: true,
                        maxlength: 1
                    },
                    otp3: {
                        required: true,
                        digits: true,
                        maxlength: 1
                    },
                    otp4: {
                        required: true,
                        digits: true,
                        maxlength: 1
                    }
                },
                messages: {
                    otp1: {
                        required: "Please enter the valid OTP.",
                        digits: "Please enter a valid digit (0-9)",
                    },
                    otp2: {
                        required: "Please enter the valid OTP.",
                        digits: "Please enter a valid digit (0-9)",
                    },
                    otp3: {
                        required: "Please enter the valid OTP.",
                        digits: "Please enter a valid digit (0-9)",
                    },
                    otp4: {
                        required: "Please enter the valid OTP.",
                        digits: "Please enter a valid digit (0-9)",
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "otp1") {
                        // error.appendTo(element.closest("form").find(".validation-error"));
                        $(".validation-error").html(error);
                    } else if (element.attr("name") === "otp2") {
                        error.appendTo(element.closest("form").find(".validation-error"));
                        $(".validation-error").html(error);
                    } else if (element.attr("name") === "otp3") {
                        error.appendTo(element.closest("form").find(".validation-error"));
                        $(".validation-error").html(error);
                    } else if (element.attr("name") === "otp4") {
                        error.appendTo(element.closest("form").find(".validation-error"));
                        $(".validation-error").html(error);
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // var countdown;
            // var countdownStartTime = localStorage.getItem('countdownStartTime');

            // if (countdownStartTime) {
            //     var currentTime = Math.floor(Date.now() / 1000);
            //     var elapsedTime = currentTime - countdownStartTime;

            //     countdown = 130 - elapsedTime;

            //     console.log(countdown);
            //     if (countdown <= 0) {
            //         $('#otpExpiredMessage').show();
            //         $('#resend-otp').show();
            //     } else {
            //         startCountdown();
            //     }
            // } else {    
            //     countdown = 130;
            //     startCountdown();
            // }

            // function startCountdown() {
            //     var countdownTimer = setInterval(function() {

            //         countdown--;
            //         if (countdown <= 0) {
            //             clearInterval(countdownTimer);
            //             $('#otpExpiredMessage').show();
            //             $('#resend-otp').show();

            //         }
            //         var minutes = Math.floor(countdown / 60);
            //         var seconds = countdown % 60;
            //         $('#countdownTimer').text(minutes.toString().padStart(2, '0') + ':' + seconds.toString()
            //             .padStart(2, '0'));
            //     }, 1000);
            // }

            // localStorage.setItem('countdownStartTime', Math.floor(Date.now() / 1000));
        });



    
        $(document).ready(function () {
    let remainingTime = localStorage.getItem('remainingTime');
    if (remainingTime !== null && !isNaN(remainingTime) && remainingTime > 0) {
        startTimer(parseInt(remainingTime));
    } else {
        startTimer(120); // Start with 2 minutes if no remaining time is stored
    }
});

function startTimer(remaining) {
    let timerInterval = setInterval(function () {
        let minutes = Math.floor(remaining / 60);
        let seconds = remaining % 60;

        let formattedMinutes = minutes < 10 ? "0" + minutes : minutes;
        let formattedSeconds = seconds < 10 ? "0" + seconds : seconds;

        $("#countdownTimer").text(formattedMinutes + ":" + formattedSeconds);

        if (remaining <= 0) {
            clearInterval(timerInterval);
            $("#otpExpiredMessage").show();
            $("#resend-otp").show();
        } else {
            remaining--;
            localStorage.setItem('remainingTime', remaining); // Store remaining time in localStorage
        }
    }, 1000);
}
    </script>
    <script>
        $(function() {
            $(document).on('submit', '#login', function(e) {
                e.preventDefault();

                $('#loader').show();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.otp_verify') }}",
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
                    error: function(xhr, status, error) {
                        var errorMessage = JSON.parse(xhr.responseText).msg;
                        console.log(errorMessage);
                        $.NotificationApp.send("Error", errorMessage, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                        // setTimeout(function() {
                        //     window.location.href = JSON.parse(xhr.responseText).location;
                        // }, 1000);
                    },
                    complete: function() {
                        $('#loader').hide();
                    }
                });
            })
        });





        $(document).on('click', '#resend-otp', function(e) {
            e.preventDefault();
            var email = $('#email').val();
            var token = '{{ csrf_token() }}'
            $.ajax({
                url: "{{ route('vendor.resend-otp') }}",
                type: "POST",
                data: {
                    email: email,
                    _token: token
                },
                success: function(result) {
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "success")
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "error")
                    }
                },
                error: function(xhr, status, error) {
                    var errorMessage = JSON.parse(xhr.responseText).msg;
                    console.log(errorMessage);
                    $.NotificationApp.send("Error", errorMessage, "top-right", "rgba(0,0,0,0.2)",
                        "error");

                },

            });
        })
    </script>
</body>

</html>
