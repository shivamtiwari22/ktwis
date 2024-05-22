@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .red {
            color: red;
        }
        .card {
            margin-bottom : unset;
        }
    </style>
@endsection


@section('main_content')
    <!-- Warning Alert Modal -->
    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Confirm</h4>
                        <p class="mt-3">Are You Sure to Delete this Vendor Application</p>
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.appereance.seo.pages') }}">Seo Page</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Page</li>
        </ol>
    </nav>
    <h2>Edit <span class="badge badge-success-lighten">Page</span></h2>
    <hr>
    <form id="update_pages_form" class="">
        @csrf
        <div class="row">
            <div class="col-sm-4">
                <input type="hidden" name="id" value="{{$pages->id}}">
                <label for="type">Type:<span class="red">*</span> </label>
                <select id="type" name="type" class="form-select">
                    <option value="" selected disabled>Select Type</option>
                    @foreach($optionsArray as $key => $item)
                    <option value="{{$key}}"  {{ $key == $pages->type ? "selected" : ""}}>{{$item}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-4">
                <label for="meta_title">Meta Title: </label>
                <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{$pages->meta_title}}">
            </div>
            <div class="col-sm-4">
                <label for="meta_description">Meta Description:</label>
                <input type="text" id="meta_description" name="meta_description" class="form-control" value="{{$pages->meta_description}}">
            </div>
         
        </div><br>

    

        <div class="row">
        
                <div class="col-sm-4">
                    <label for="key_features">Og Tag :</label>
                    <textarea name="ogtag" id="" class="form-control"> {{$pages->og_tag}} </textarea>
                </div>
                <div class="col-sm-4">
                    <label for="description">Schema Markup :</label>
                    <textarea name="schema_markup" id="" class="form-control"> {{$pages->schema_markup}}</textarea>
                </div>
        </div><br>

        <div>
            <label for="content">Keywords:</label>
            <textarea  id="" cols="30" rows="10" name="keywords" class="form-control"> {{$pages->keywords}}</textarea>
        </div>
        <br>

      

        <div class="d-flex mt-2">
            <button type="submit" class="btn btn-success mx-auto"> Update</button>
        </div><br>
    </form>
@endsection

@section('script')

    <script>
        $(document).ready(function() {
            $('#update_pages_form').submit(function(e) {
                e.preventDefault();
                if (validateForm_edit()) {
                    var form = $(this);
                    var formData = new FormData(form[0]);

                    $.ajax({
                        url: "{{ route('admin.appereance.seo.update_pages') }}",
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
                            console.log(xhr.responseText);
                            $.NotificationApp.send("Error", xhr.responseText, "top-right",
                                "rgba(0,0,0,0.2)", "error");

                        }
                    });
                }
            });

            function validateForm_edit() {
                var isValid = true;
                $('.error-message').remove();

                $('#title, #type, #page_status, #slug').each(function() {
                    var input = $(this).attr('id');
                    var value = $.trim($(this).val());
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else if (input === "title" || input === "meta_title") {
                        if (value.length < 4 || value.length > 26) {
                            var errorMessage = 'input must be between 4 and 26 characters.';
                            $(this).addClass('is-invalid');
                            $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                                '</span>');
                            isValid = false;
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

            

                
                $('#meta_title,#meta_description').each(function() {
                    var value = $.trim($(this).val());
                    // console.log(value);
                    if (value.length < 4 || value.length > 26 ) {
                            var errorMessage = 'This field must be between 4 and 26 characters.';
                            $(this).addClass('is-invalid');
                            $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                                '</span>');
                            isValid = false;
                        
                    } else {

                        $(this).removeClass('is-invalid');
                    }
                
                })

                $('#title, #type, #page_status, #slug, #content', '#banner_images','#meta_title','#meta_description')
                    .on('input change',
                        function() {
                            $(this).removeClass('is-invalid');
                            $(this).next('.error-message').remove();
                        });

                return isValid;
            }

        });
    </script>
@endsection
