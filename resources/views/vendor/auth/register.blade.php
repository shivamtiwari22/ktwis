<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Vendor Register | Ktwis</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('public/assets/images/asset_10.png') }}">
    <!-- toastr -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css"
        rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
        top: 43%;
        right: 15%;
        cursor: pointer;
        color: lightgray;
        font-size: 23px
    }

    .error {
        color: red
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


    .cv-spinner {
        height: 100% !important;
    }

    .phone-input-container {
        display: flex;
        align-items: center;
        /* Vertically center the items */
    }

    .country-code {
        /* This will make the country code take up 1/3 of the available space */
        margin-right: -2px;
        /* Add some space between country code and mobile number */
    }

    .mobile-number {
        flex: 2;
        /* This will make the mobile number take up 2/3 of the available space */
    }
</style>

@include('component.myloader')

<body class="authentication-bg pb-0" data-layout-config='{"darkMode":false}'>

    <div class="auth-fluid">
        <!--Auth fluid left content -->
        <div class="auth-fluid-form-box">
            <div class="align-items-center d-flex h-100">
                <div class="card-body">

                    <!-- Logo -->
                    <div class="auth-brand text-center text-lg-start">
                        <a href="index.html" class="logo-dark">
                            <span><img src="{{ asset('public/assets/images/asset_9.png') }}" alt="" height="18"></span>
                        </a>
                        {{-- <a href="index.html" class="logo-light">
                            <span><img src="{{ asset('public/assets/images/logo.png') }}" alt="" height="18"></span>
                        </a> --}}
                    </div>

                    <!-- title-->
                    <h4 class=""> Sign Up</h4>
                    <p class="text-muted mb-3">Don't have an account? Create your account, it takes less than a minute
                    </p>

                    <!-- form -->
                    <form action="#" id="vendor-registration-form">
                        <div class="mb-3">
                            <label for="fullname" class="form-label"> Name <span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="name" id="fullname"
                                placeholder="Enter your name">
                            {{-- <div class="error-container"></div> --}}
                        </div>
                        <div class="mb-3">
                            <label for="emailaddress" class="form-label">Email address <span
                                    class="text-danger">*</span></label>
                            <input class="form-control" type="email" name="email" id="emailaddress"
                                placeholder="Enter your email">
                            <div class="error-container"></div>
                        </div>
                        <div class="mb-1">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input class="form-control" type="password" id="password" name="password"
                                    placeholder="Enter your password">
                                <div class="input-group-text" data-password="false">
                                    <span class="password-eye"></span>
                                </div>
                            </div>

                            <div class="error-container"></div>
                            <label id="password-error" class="error" for="password"></label>
                        </div>

                        <div class="mb-1">
                            <label for="confirm_password" class="form-label">Confirm Password <span
                                    class="text-danger">*</span></label>
                            <div class="input-group input-group-merge">
                                <input class="form-control" type="password" id="confirm-password"
                                    name="confirm_password" placeholder="Confirm your password">
                                <div class="input-group-text" data-password="false">
                                    <span class="password-eye"></span>
                                </div>
                            </div>

                            <label id="confirm-password-error" class="error" for="confirm-password"></label>

                        </div>
                        <div class="mb-3">
                            <label for="tax" class="form-label">Phone <span class="text-danger">*</span></label>
                            <div class="phone-input-container">
                                <div class="country-code">
                                    <select name="country_code" id="country_code" class="form-select">
                                        @foreach ($areas as $index => $area)
                                            <option value="{{ $area->calling_code }}"
                                                {{ $index === 0 ? 'selected' : '  ' }}><img src=""
                                                    alt="flag"> {{ $area->calling_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mobile-number">
                                    <input class="form-control" type="text" id="phone" name="mobile_number"
                                        placeholder="Enter your phone number">
                                </div>
                            </div>

                            <label id="phone-error" class="error" for="phone"></label>

                        </div>
                        <div class="mb-3">
                            <label for="tax_number" class="form-label">Shop Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="shop" name="shop_name"
                                placeholder="Enter your shope name">
                            <div class="error-container"></div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="terms" class="form-check-input" id="checkbox-signup">
                                <label class="form-check-label" for="checkbox-signup">I accept <a
                                        href="{{ route('vendor.terms') }}" target="blank">Terms and
                                        Conditions</a></label>
                            </div>
                            <label id="terms-error" class="error" for="terms"></label>


                        </div>
                        <div class="mb-0 d-grid text-center">
                            <button class="btn btn-danger" type="submit"><i class="mdi mdi-account-circle"></i>
                                Sign Up</button>
                        </div>
                        <!-- social
                        <div class="text-center mt-4">
                            <p class="text-muted font-16">Sign up using</p>
                            <ul class="social-list list-inline mt-3">
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);"
                                        class="social-list-item border-primary text-primary"><i
                                            class="mdi mdi-facebook"></i></a>
                                </li>
                                <li class="list-inline-item">
                                    <a href="javascript: void(0);"
                                        class="social-list-item border-danger text-danger"><i
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
                        </div> -->
                    </form>
                    <!-- end form-->

                    <!-- Footer-->
                    <footer class="footer footer-alt">
                        <p class="text-muted">Already have an account? <a href="{{ route('vendor.login') }}"
                                class="text-muted ms-1"><b>Log In</b></a></p>
                    </footer>

                </div> <!-- end .card-body -->
            </div> <!-- end .align-items-center.d-flex.h-100-->
        </div>
        <!-- end auth-fluid-form-box-->

        <!-- Auth fluid right content -->
        <div class="auth-fluid-right text-center">
            <div class="auth-user-testimonial">
                {{-- <h2 class="mb-3">I love the color!</h2>
                <p class="lead"><i class="mdi mdi-format-quote-open"></i> It's an elegant template. I love it very
                    much! <i class="mdi mdi-format-quote-close"></i></p>
                <p>- {{ $userName }}</p> --}}
            </div> <!-- end auth-user-testimonial-->
        </div>
        <!-- end Auth fluid right content -->
    </div>

    <!-- end auth-fluid-->
    {{-- 
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        {{ $condition ? $condition->title : 'Lorem Ipsum' }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if ($condition)
                    {!! $condition->content !!}
                    @else
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been
                    the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of
                    type and scrambled it to make a type specimen book. It has survived not only five centuries, but
                    also the leap into electronic typesetting, remaining essentially unchanged. It was popularised
                    in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more
                    recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum
                    @endif

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div> --}}

    <!-- bundle -->
    <script src="{{ asset('public/assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('public/assets/js/app.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#termsLink').click(function() {
                $.ajax({
                    url: "",
                    method: 'GET',
                    success: function(response) {
                        var newWindow = window.open();
                        newWindow.document.write(response.content);
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#vendor-registration-form').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 35,
                    },
                    email: {
                        required: true,
                        rangelength: [2, 50],
                        maxlength: 50,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 6,
                        maxlength: 16,
                        uppercase: true,
                        lowercase: true,
                        numeric: true,
                        specialChars: true,
                    },
                    confirm_password: {
                        equalTo: "#password"
                    },

                    mobile_number: {
                        required: true,
                        number: true,
                        maxlength: 20,
                        minlength:6,
                    },
                    shop_name: {
                        required: true,
                        maxlength: 256,
                    },
                    terms: {
                        required: true,
                    }      
                },
                messages: {
                        confirm_password: {
                            equalTo: "Confirm Password does not match." 
                        }
                    }
            })

            $.validator.addMethod("specialChars", function(value, element) {
                return this.optional(element) || /[!@#$%^&*(),.?":{}|<>]/.test(value);
            }, "Please include at least one special character.");

            $.validator.addMethod("uppercase", function(value, element) {
                return this.optional(element) || /[A-Z]/.test(value);
            }, "Password must contain at least one uppercase letter")

            $.validator.addMethod("lowercase", function(value, element) {
                return this.optional(element) || /[a-z]/.test(value);
            }, "Password must contain at least one lowercase letter")

            $.validator.addMethod("numeric", function(value, element) {
                return this.optional(element) || /[0-9]/.test(value);
            }, "Password must contain at least one numeric digit")

        })
    </script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).on('submit', '#vendor-registration-form', function(event) {
            event.preventDefault();
            $("#overlay").fadeIn();

            var formData = {
                name: $('#fullname').val(),
                email: $('#emailaddress').val(),
                password: $('#password').val(),
                password_confirmation: $('#confirm-password').val(),
                shop_name: $('#shop').val(),
                mobile_number: $('#phone').val(),
                country_code: $('#country_code').val()
            };

            console.log($('#country_code').val());
            $.ajax({
                url: "{{ route('vendor.register.store') }}",
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    $("#overlay").fadeOut();

                    if (response.success) {
                        toastr.success('Registration successful!', 'Success', {
                            timeOut: 3000
                        });

                        window.location.href = response.location;
                        // toastr.clear();
                        // $('#vendor-registration-form')[0].reset();
                    } else {
                        toastr.error(response.message, 'error', 'error', {
                            timeOut: 5000
                        });
                    }

                },
                error: function(xhr, status, error) {
                    $("#overlay").fadeOut();
                    toastr.clear();
                    var errorMessage = xhr.responseJSON.message;
                    toastr.error(errorMessage, 'Error', {
                        timeOut: 5000
                    });
                }
            });
        });

        function validateEmail(email) {
            var re = /\S+@\S+\.\S+/;
            return re.test(email);
        }

        function validateForm() {
            var isValid = true;

            $('.error-container').empty();

            var fullname = $('#fullname').val();
            if ($.trim(fullname) === '') {
                var errorMessage = 'Fullname is required';
                $('#fullname').addClass('is-invalid');
                $('#fullname').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else {
                $('#fullname').removeClass('is-invalid');
            }

            var email = $('#emailaddress').val();
            if ($.trim(email) === '') {
                var errorMessage = 'Email is required';
                $('#emailaddress').addClass('is-invalid');
                $('#emailaddress').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else if (!validateEmail(email)) {
                var errorMessage = 'Invalid email format';
                $('#emailaddress').addClass('is-invalid');
                $('#emailaddress').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else {
                $('#emailaddress').removeClass('is-invalid');
            }

            var password = $('#password').val();
            var regExp = /[_\-!\"@;,.:]/;
            if ($.trim(password) === '') {
                var errorMessage = 'Password is required';
                $('#password').addClass('is-invalid');
                $('#password').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else if (password.length < 8 || password.length > 15) {
                var errorMessage = 'Password must be between 8 and 15 characters';
                $('#password').addClass('is-invalid');
                $('#password').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else if (!regExp.test(password)) {
                var errorMessage = 'Password should contain at least one special character';
                $('#password').addClass('is-invalid');
                $('#password').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else {
                $('#password').removeClass('is-invalid');
            }

            var taxType = $('#tax_type').val();
            if ($.trim(taxType) === '') {
                var errorMessage = 'Tax type is required';
                $('#tax_type').addClass('is-invalid');
                $('#tax_type').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else {
                $('#tax_type').removeClass('is-invalid');
            }

            var taxPin = $('#tax_pin').val();
            if ($.trim(taxPin) === '') {
                var errorMessage = 'Tax PIN is required';
                $('#tax_pin').addClass('is-invalid');
                $('#tax_pin').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            } else {
                $('#tax_pin').removeClass('is-invalid');
            }

            if (!$('#checkbox-signup').prop('checked')) {
                var errorMessage = 'Please agree to the terms and conditions.';
                $('#checkbox-signup').siblings('.error-container').html(
                    '<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                isValid = false;
            }

            $('#fullname, #emailaddress, #password, #tax_type, #tax_pin, #checkbox-signup').on('input change',
                function() {
                    $(this).removeClass('is-invalid');
                    $(this).siblings('.error-container').empty();
                });


            return isValid;
        }
    </script>

</body>

</html>
