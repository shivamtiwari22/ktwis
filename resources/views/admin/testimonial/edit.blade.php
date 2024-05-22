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
            <li class="breadcrumb-item"><a href="{{ route('admin.test.test_monial') }}">Testimonial</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Testimonial</li>
        </ol>
    </nav>
    <form id="reply_form">
        <input type="hidden" name="id" id="id" value="{{ $dispute->id }}">
        <div class="card p-4 mt-4">
           
            <div class="row">
                <div class="col-sm-6">
                    <label for="">Name  <span class="text-danger">*</span></label><br>
                      <input type="text" name="name" value="{{$dispute->name}}" class="form-control" >
                </div>
                <div class="col-sm-6">
                    <label for="">Rating  <span class="text-danger">*</span></label><br>
                    <input type="text" name="rating"  class="form-control" value="{{$dispute->rating}}">
               
                    </select>
                </div>
            </div><br>
            <div class="row">
                <div class="col-sm-6">
                    <label for="">File (jpg, jpeg, png only ,2mb)</label><br>
                      <input type="file" name="name_file" class="form-control" >
                </div>
                <div class="col-sm-6">
                    <label>Change Status</label>
            <select name="status" class="form-control change_status" id="tax_type" data-id="">
                <option value="1" @if ($dispute->status == "1") selected @endif>Active</option>
                <option value="0" @if ($dispute->status == "0") selected @endif>InActive</option>
            </select>
                    </select>
                </div>
            </div><br>

            <div>
                <label for="content">Content <span class="error">*</span></label>
                <textarea id="contents" name="content" class="form-control summernote">{{ $dispute->testimonial }}</textarea>
                <div id="messageError" class="text-danger"></div>

            </div>
                 
            <div class="d-flex mt-2">
                <button type="submit" class="btn btn-success mx-auto"> Update</button>
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


        document.addEventListener("DOMContentLoaded", function() {
   
   const contentInput = document.getElementById("contents");
   const messageError = document.getElementById("messageError");
   const submitBtn = document.getElementById("reply_form");


   submitBtn.addEventListener("submit", function(event) {
       messageError.innerHTML = "";


       if (contentInput.value.trim() === "") {
           messageError.innerHTML = "Please enter some text in the editor.";
           event.preventDefault();
       }
   });
});
    </script>
    <script>
        $(document).ready(function() {
                $('#reply_form').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength:26,
                    },
                    rating: {
                        required: true,
                        maxlength:26,

                    },
                    file : {
                        filesize: 1024,
                        imageFormat: true

                    },
                    content : {
                        required: true,
                    }

                },
                messages: {
                    file : {
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
                    url: "{{ route('admin.test.update_testimonial') }}",
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
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                    }
                });
            });
        });
    </script>
@endsection
