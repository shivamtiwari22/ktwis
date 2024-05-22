@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.vendor.applications') }}">Merchants</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Shop</li>
        </ol>
    </nav>
    <div class="card p-2 mt-1">
        <div class=" mb-2">
            <h4>Form</h4>
        </div>
        <form id="rate_form">
        <div class="row">
            <div class="mb-3 col-lg-8">
                <label for="simpleinput" class="form-label">Shop Name<span class="text-danger">*</span></label>
                <input type="hidden" name="shop_id" id="shop_id" value="{{ $shop->id ?? '' }}">
                <input type="text" class="form-control" name="shop_name" id="shop_name"
                    value="{{ $shop->shop_name ?? '' }}">
            </div>
            <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Status </label>
                    <select name="status" id="" class="form-select">
                        <option value="active"   {{$shop ? $shop->status == "active" ? 'selected' : '' : '' }} >Active</option>
                        <option value="inactive"   {{$shop ? $shop->status == "inactive" ? 'selected' : '' : '' }} >Inactive</option>
                    </select>
            </div>
          
        </div>
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="simpleinput" class="form-label"> Legal Name<span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="legal_name" name="legal_name"
                    value="{{ $shop->legal_name ?? '' }}">
            </div>
            
         
            <div class="mb-3 col-lg-6">
                <label class="form-label">TimeZone<span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="timezone" id="timezone"
                    value="{{ $shop->timezone ?? '' }}">
            </div>
           
        </div>
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="example-select" class="form-label">Email<span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $shop->email ?? '' }}" readonly>
            </div>

            <div class="mb-3 col-lg-6">
            
                <label for="simpleinput" class="form-label">Shop URL </label>
                <input type="text" class="form-control" name="shop_url" id="shop_url"
                    value="{{ $shop->shop_url ?? '' }}">
            </div>

        </div>
        <div class="row">
            <div class="mb-3 col-lg-12">
                <label class="form-label">Description</label>
                <textarea type="text" class="form-control" name="description" id="description">{{ $shop->description ?? '' }}</textarea>
            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-lg-6">
                <label class="form-label">Brand Logo </label>

                 <img src="{{ asset('public/vendor/shop/brand/'.$shop->brand_logo) }}" alt="Brand Logo"  width="40%" height="40%">
                <input type="file" name="brand_logo" id="brand_logo" class="form-control" accept="image/*"
                   >
            </div>
            <div class="mb-3 col-lg-6">
                <label class="form-label">Cover Logo </label>
                <img src="{{ asset('public/vendor/shop/cover/'.$shop->cover_image) }}" alt="Cover Logo"  width="40%" height="40%">
                <input type="file" name="cover_logo" id="cover_logo" class="form-control" accept="image/*"
                    >
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



$('#rate_form').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 36,
                    },
                    email: {
                        required: true,
                        maxlength: 40,
                        email: true,
                        emailWithDomain: true

                    },
                    password: {
                        required: true,
                    },
                    shop_name: {
                        required: true,
                        maxlength: 26,


                    },
                    legal_name: {
                        required: true,
                        maxlength: 26,

                    },
                    shop_url: {
                        linkvalid: true,

                    },
                    brand_logo: {
                        filesize:1024,
                        imageFormat:true

                    },
                    cover_logo : {
                        filesize:1024,
                        imageFormat:true 
                    }


                },
                messages: {
                    brand_logo: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                    cover_logo :{
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    }

                },
            });

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
            $.validator.addMethod("linkvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please enter valid link.");


        $('body').on('submit', '#rate_form', function(e) {
            e.preventDefault();

            let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");
        
            $.ajax({
                    url: "{{ route('admin.vendor.applications.update.application') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                })
                .done(function(result) {
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)",
                        "success");
                        setTimeout(function() {
                            window.location.href = result.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                    }
                })
                .fail(function(jqXHR, exception) {
                    console.log(jqXHR.responseText);
                });
        });
    </script>
@endsection
