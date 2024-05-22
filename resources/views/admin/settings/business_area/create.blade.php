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
            <li class="breadcrumb-item"><a aria-current="page" href="{{ route('business.list') }}">Business Area</a></li>
            <li class="breadcrumb-item"><a aria-current="page">Add Business Area</a></li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('business.list') }}" class="btn btn-primary">View All Business Area</a>
        </div>
        <form id="business_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Business Area Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name">

                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Full Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="full_name">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">ISO Code<span class="text-danger">*</span></label><span
                        tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="ISO 3166_2 code. For example, Example: For United States of America the code is US">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control mb-1" name="iso_code">
                    <a href="https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes" target="_blank"
                        class="text-secondary">https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes</a>
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Flag (PNG,JPG,JPENG,2MB)</label>
                    <input type="file" class="form-control" name="flag" accept=".png, .jpg, .svg">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Calling Code <span class="error">*</span></label>
                    <input type="text" class="form-control" name="calling_code">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency<span class="text-danger">*</span></label>
                    <select class="form-select" id="example-select" name="currency">
                    <option value="" selected disabled>Select Currency </option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->id }}">
                                {{ $currency->currency_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                <label for="simpleinput" class="form-label">Country<span class="text-danger">*</span></label>
                <select name="country_id" id="country" class="form-control">
                    <option value="" selected disabled>Select Country </option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" data-id="{{ $country->id }}">
                            {{ $country->country_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3 col-lg-6">

                <label for="simpleinput" class="form-label">Status<span class="text-danger">*</span></label>
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
          $('#business_form').validate({
              rules: {
                  name: {
                      required: true,
                      maxlength: 26,
                      minlength:4
                  },
                  full_name: {
                      required: true,
                      maxlength: 26,
                      minlength:4
                  },
                  iso_code: {
                      required: true,
                  },
                  currency: {
                      required: true,
                  },
                  calling_code: {
                      required: true,
                      maxlength: 6,
                      minlength:2
                  },
                  country_id: {
                      required: true,
                  },
                  flag :{
                    filesize:1024,
                        imageFormat:true 
                  }

              },

              messages: {
                flag: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                }


          });


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
            $(document).on('submit', '#business_form', function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('business_area.save') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                         console.log(result);
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
