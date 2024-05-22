@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.carrier.list')}}">Carrier</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Carrier</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.carrier.list')}}" class="btn btn-primary">View All Carriers</a>
    </div>
    <form id="coupon_form">
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="simpleinput" class="form-label">Name<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{$carrier->name}}">
                <input type="hidden" class="form-control" name="id" value="{{$carrier->id}}">
            </div>
            <div class="mb-3 col-lg-6">
                <label for="example-select" class="form-label">Tracking URL<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="url" name="url" value="{{$carrier->tracking_url}}">
            </div>
        </div>
        <div class="row">
            <div class="mb-3 col-lg-4">
                <label for="simpleinput" class="form-label">Phone<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{$carrier->phone}}">
            </div>
            <div class="mb-3 col-lg-4">
                <label for="example-select" class="form-label">Email<span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{$carrier->email}}">
            </div>
            <div class="mb-3 col-lg-4">
                <label class="form-label">Logo :</label>
                <span><img src="{{ asset('public/admin/shipping/carriers/logo/' . $carrier->logo) }}" alt="{{ $carrier->name }}" height="30px"></span>
                <input type="file" class="form-control" id="logo" name="logo">
            </div>
        </div>
        <div class="row">
            <label for="simpleinput" class="form-label">Status<span class="text-danger">*</span></label>
            <div class="mb-3">
                <input type="hidden" name="status" value="{{$carrier->status}}">
                <input type="checkbox" id="switch2" {{ $carrier->status == "1" ? 'checked' : '' }} data-switch="primary" value={{$carrier->status}} onclick="updateCheckboxValue(this)">
                <label for="switch2" data-on-label="On" data-off-label="Off"></label>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Update</button>
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
                    minlength:6,
                    maxlength: 15,
                },
                url: {
                    linkvalid: true,
                    required: true
                },
                logo: {
                    imageFormat: true,
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

       
        $.validator.addMethod("fileExtension", function(value, element, extensions) {
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

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");
    })
</script>

<script>
    $(function() {
        $(document).on('submit', '#coupon_form', function(e) {
            e.preventDefault();
       
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.carrier.update') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result.location);
                        if (result.status) {
                            $.NotificationApp.send("Success", result.message, "top-right", "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.message, "top-right", "rgba(0,0,0,0.2)", "error")
                        }
                    },
                });
    
        })

        function validateForm() {
            var isValid = true;

            $('.error-message').remove();

            $('#name ,#url ,#phone ,#email ').each(function() {
                if ($.trim($(this).val()) === '') {
                    var errorMessage = 'This field is required';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
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


            $('#name ,#url ,#phone ,#email').on('input change', function() {
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