@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style>
     .loader {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .loader-wheel {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .loader-wheel .spinner {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border-top: 4px solid #f3f3f3;
            border-right: 4px solid #f3f3f3;
            border-bottom: 4px solid #f3f3f3;
            border-left: 4px solid #337ab7;
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
</style>
@endsection

@section('main_content')

<div class="loader" id="loader">
    <div class="loader-wheel">
        <div class="spinner"></div>
    </div>
</div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.carrier.list') }}">Carrier</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('vendor.carrier.list') }}" class="btn btn-primary">View All Carriers</a>
        </div>
        <form id="coupon_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="example-select" class="form-label">Tracking URL<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="url" name="url">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Phone<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="phone" name="phone">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="example-select" class="form-label">Email<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
                <div class="mb-3 col-lg-4">
                    <label class="form-label">Logo only (jpg, jpeg, png , 2mb size)</label>
                    <input type="file" id="logo" accept="image/*" class="form-control " name="logo">
                </div>
            </div>
            <div class="row">
                <label for="simpleinput" class="form-label">Status </label>
                <div class="mb-3">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" id="switch2" checked data-switch="primary" name="status" value="1"
                        onclick="updateCheckboxValue(this)">
                    <label for="switch2" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('#coupon_form').validate({
                rules: {
                    name: {
                        required: true,
                        rangelength: [2, 256],
                        maxlength: 46,
                    },
                    email: {
                        required: true,
                        rangelength: [2, 256],
                        maxlength: 256,
                        email: true,
                        emailWithDomain: true
                    },
                    phone: {
                        required: true,
                        number: true,
                        minlength: 6,
                        maxlength: 15,
                    },
                    url: {
                        linkvalid: true,
                        required: true
                    },
                    logo: {
                        format: "jpg,jpeg,png",
                        filesize: 2024,
                    },

                },

                messages: {
                    logo: {
                        format : "Only PNG, JPEG, and JPG files are allowed",
                        filesize: "Maximum file size is 2MB",
                    }

                },
            })

            $.validator.addMethod("format", function(value, element, extensions) {
                // Split the filename to get the extension
                var extension = value.split('.').pop().toLowerCase();

                // Check if the extension is in the allowed extensions list
                return this.optional(element) || extensions.split(',').indexOf(extension) !== -1;
            }, "Please select a valid file format.");

            $.validator.addMethod("linkvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please enter valid link.");

            $.validator.addMethod("emailWithDomain", function(value, element) {
                return this.optional(element) || /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(value);
            }, "Please enter a valid email address.");

            $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");
        })
    </script>

    <script>
        $(function() {
            $(document).on('submit', '#coupon_form', function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $('#loader').show();
                $.ajax({
                    url: "{{ route('vendor.carrier.save') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result.location);
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error");

                                $('#loader').hide();

                        }
                    },
                });

            })

            function validateForm() {
                var isValid = true;

                $('.error-message').remove();

                $('#name,#url ,#phone ,#email ,#logo ,#switch2').each(function() {
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                // if (!$('#requires_shipping').is(':checked')) {
                //     var errorMessage = 'Requires Shipping is required';
                //     $('#requires_shipping').addClass('is-invalid');
                //     $('#requires_shipping').after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                //     isValid = false;
                // } else {
                //     $('#requires_shipping').removeClass('is-invalid');
                // }


                $('#name ,#url ,#phone ,#email , #logo ,#switch2').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }
        });
    </script>
    <script>
        function updateCheckboxValue(checkbox) {
            var hiddenInput = document.querySelector('input[name="status"][type="hidden"]');
            hiddenInput.value = checkbox.checked ? 1 : 0;
        }
    </script>
@endsection
