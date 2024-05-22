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
            <li class="breadcrumb-item"><a aria-current="page">Settings</a></li>
            <li class="breadcrumb-item"><a aria-current="page">System Settings</a></li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">

        <form id="system_form" enctype="multipart/form-data">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Marketplace Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="system_name" value="{{$settings ? $settings->system_name : ''}}">

                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Legal Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="legal_name" value="{{$settings ? $settings->legal_name : ''}}">
                </div>
            </div>


            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Email Address<span class="text-danger">*</span></label>
                    <input type="" class="form-control" name="email_address" value="{{$settings ? $settings->email_address : ''}}">

                </div>

                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Business Area <span class="text-danger">*</span></label>
                    <select  id="" class="form-select" name="business_id">
                        <option value="" selected disabled>Select Business Area</option>
                        @foreach ($business as $item)
                        <option value="{{$item->id}}"  {{$settings ? $settings->business_id == $item->id ? 'selected' : '':''}}>{{$item->name}}</option>
                        @endforeach
                    </select>

                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Country<span class="text-danger">*</span></label>
                    <select name="country_id" id="" class="form-select" >
                        <option value="" selected disabled>Select Country</option>
                        @foreach ($countries as $item)
                        <option value="{{$item->id}}"  {{$settings ? $settings->country_id == $item->id ? 'selected' : '':''}}>{{$item->country_name}}</option>
                        @endforeach
                    </select>
                </div>

             
            </div>

            <div class="row">

                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Brand Logo </label>
                    <input type="file" class="form-control" name="brand_logo" accept=".png, .jpg, .svg">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Icon </label>
                    <input type="file" class="form-control" name="icon" accept=".png, .jpg, .svg">
                </div>
            </div>

            

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Brand logo: </label>

                          
                    <img src="{{  $settings ? asset('public/admin/system/'. $settings->brand_logo) : ''}}" height="30px" width="30px" alt="Brand">
                   
                </div>

                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label"> Icon: </label>
                    <img src="{{  $settings ? asset('public/admin/system/'. $settings->icon) : ''}}" height="30px" width="30px" alt="Brand">

                   
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
        $('#system_form').validate({
            rules: {
                system_name: {
                    required: true,
                    maxlength:36
                },
                legal_name: {
                    required: true,
                    maxlength:36
                },
                email_address: {
                    required: true,
                    email: true,
                    emailWithDomain: true,
                    maxlength:40

                },
                 business_id : {
                    required: true
                },
                country_id : {
                    required:true
                },
                lang_id : {
                    required:true
                },
                currency_id : {
                    required:true
                },
                brand_logo : {
                    imageFormat: true,
                        filesize: 1024
                },
                icon : {
                    imageFormat: true,
                        filesize: 1024
                }


            },
            messages: {
                brand_logo: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                icon: {
                    imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                }
            }
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
    })
</script>



    <script>
        $(function() {
            $(document).on('submit','#system_form', function(e){
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('admin.update_system_setting') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.message, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.message, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                });
            })
        });
    </script>
    <script>
        function updateCheckboxValue(checkbox) {
            var hiddenInput = document.querySelector('input[name="status"][type="hidden"]');
            hiddenInput.value = checkbox.checked ? 1 : 0;
        }
    </script>
@endsection
