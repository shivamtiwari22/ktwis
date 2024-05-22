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
            <li class="breadcrumb-item"><a aria-current="page" href="{{ route('currencies.list') }}">Currencies</a></li>
            <li class="breadcrumb-item"><a aria-current="page">Add Currencies</a></li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('currencies.list') }}" class="btn btn-primary">View All Currencies</a>
        </div>
        <form id="currency_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="currency_name">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency Code<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="currency_code">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency Flag (PNG,JPG,JPENG,2MB)</label>
                    <input type="file" class="form-control" name="currency_flag" accept=".png, .jpg, .svg">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency Symbol <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="symbol">
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
            $('#currency_form').validate({
                rules: {
                    currency_name: {
                        required: true,
                        maxlength: 14,
                    },
                    currency_code: {
                        required: true,
                        maxlength: 20,
                    },
                    symbol: {
                        required: true,
                        maxlength: 20,
                    },
                    currency_flag : {
                        filesize:1024,
                        imageFormat:true 
                    }
                },

                messages: {
                    currency_flag: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },

                },


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
            $(document).on('submit','#currency_form', function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('currencies.save') }}",
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
                });
            })
        });
    </script>
@endsection
