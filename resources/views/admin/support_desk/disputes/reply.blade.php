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
            <li class="breadcrumb-item"><a href="{{ route('admin.disputes.index') }}">Disputes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disputes Reply</li>
        </ol>
    </nav>
    <form id="reply_form" enctype="multipart/form-data">
        <input type="hidden" name="id" id="id" value="{{ $dispute->id }}">
        <div class="card p-4 mt-4">
            <div class="d-flex justify-content-end mb-2">
                <a href="{{ route('admin.disputes.index') }}" class="btn btn-primary">View All Disputes</a>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <label for="">Status: </label><br>
                    <select name="reply_status" id="reply_status" class="form-control">
                        <option value="" selected disabled>Select Status</option>
                        <option value="new" {{ $dispute->status == 'new' ? 'selected' : '' }}>NEW</option>
                        <option value="open" {{ $dispute->status == 'open' ? 'selected' : '' }}>OPEN</option>
                        <option value="waiting" {{ $dispute->status == 'waiting' ? 'selected' : '' }}>WAITING</option>
                        <option value="solved" {{ $dispute->status == 'solved' ? 'selected' : '' }}>SOLVED</option>
                        <option value="closed" {{ $dispute->status == 'closed' ? 'selected' : '' }}>CLOSED</option>
                    </select>
                </div>
                <div class="col-sm-6">
                    <label for="">Upload Attachment: (JPG,PNG,JPEG,2MB) </label><br>
                    <input type="file" name="attachment" id="attachment" class="form-control" />
                </div>
            </div><br><br>

            <div>
                <label for="content">Content:<span class="red">*</span></label>
                <textarea id="content" name="content" class="form-control summernote" required ></textarea>
            </div>
            <div class="d-flex">
                <button type="submit" class="btn btn-success mx-auto"> Submit</button>
            </div>
        </div>
    </form>
@endsection


@section('script')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#reply_form').validate({
                rules: {
                    reply_status: {
                        required: true,
                    },
                    // content: {
                    //     required: true,
                    // },
                    attachment : {
                    imageFormat: true,
                        filesize: 1024
                    },
                },
                
            messages: {
                attachment: {
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
        $(document).ready(function() {
            $(document).on('submit', '#reply_form', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                $.ajax({
                    url: "{{ route('admin.disputes.reply.store') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.message, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = response.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.message, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.log(xhr.responseText);
                        var errorMessage = JSON.parse(xhr.responseText).message;
                        $.NotificationApp.send("Error", errorMessage, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                    }
                });
            });
        });
    </script>
@endsection
