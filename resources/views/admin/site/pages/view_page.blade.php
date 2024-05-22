@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style>
    label {
        font-weight: bold;
    }
</style>
@endsection


@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.appereance.pages')}}">Page</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Page : {{$pages->type}}</li>
    </ol>
</nav>
<hr>
<h2>View <span class="badge badge-success-lighten">Page</span></h2>
<div id="update_pages_form" class="form-control">
    <div class="row">
        <div class="col-sm-4"> <label for="title">Title:</label>
            <span class="form-control">{{$pages->title}}</span>
        </div>
        <div class="col-sm-4"> <label for="type">Type:</label>
            <span class="form-control">{{$pages->type}}</span>
        </div>
        <div class="col-sm-4">
            <label for="banner_image">Banner Image:</label><br>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <a href="{{asset('public/admin/appereance/pages/banner_image/'.$pages->banner_image)}}" target="_blank">
                        <img src="{{asset('public/admin/appereance/pages/banner_image/'.$pages->banner_image)}}" alt="Banner Image" style="border: 1px solid black;" width="55px">
                    </a>
                </div>
            </div>
        </div>
    </div><br>
    <div class="row">
        <div class="col-sm-4">
            <label for="status">Status:</label>
            <span class="form-control">{{$pages->status}}</span>
        </div>
        <div class="col-sm-4"><label for="meta_title">Meta Title:</label>
            <span class="form-control">{{$pages->meta_title}}</span>
        </div>
        <div class="col-sm-4">
            <label for="slug">Meta Description:</label>
            <span class="form-control">{{$pages->meta_description}}</span>
        </div>
    </div><br>
    <div>
        <label for="content">Content:</label>
        <span class="form-control">{!! $pages->content !!}</span>
    </div><br>

</div>

@endsection

@section('script')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#title').on('input', function() {
            var name = $(this).val();
            var slug = name.toLowerCase().replace(/\s+/g, '-');
            $('#slug').val(slug);
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#update_pages_form').submit(function(e) {
            e.preventDefault();

            var form = $(this);
            var formData = new FormData(form[0]);

            $.ajax({
                url: "{{route('admin.appereance.update_pages')}}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    if (response.status) {
                        $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success")
                        setTimeout(function() {
                            window.location.href = response.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", response.message, "top-right", "rgba(0,0,0,0.2)", "error")
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    $.NotificationApp.send("Error", xhr.responseText, "top-right", "rgba(0,0,0,0.2)", "error");

                }
            });
        });
    });
</script>

@endsection