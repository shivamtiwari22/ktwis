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
        <li class="breadcrumb-item"><a href="{{route('admin.appereance.blogs')}}">Blogs</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Blog :</li>
    </ol>
</nav>
<hr>
<h2>View <span class="badge badge-success-lighten">Blog</span></h2>
<div id="update_pages_form" class="form-control">
    <div class="row">
        <div class="col-sm-1">
            <label for="banner_image">Banner Image:</label>
        </div>
        <div class="col-sm-3">
            <a href="{{asset('public/admin/appereance/blogs/banner_image/'.$blog->banner_image)}}" target="_blank">
                <img src="{{asset('public/admin/appereance/blogs/banner_image/'.$blog->banner_image)}}" alt="Banner Image" style="border: 1px solid black;" width="80px">
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-3"> <label for="title">Title:</label>
            <span class="form-control">{{$blog->title}}</span>
        </div>
        <div class="col-sm-3"> <label for="slug">Slug:</label>
            <span class="form-control">{{$blog->slug}}</span>
        </div>
        <div class="col-sm-3"><label for="status">Status:</label>
            <span class="form-control">{{$blog->status}}</span>
        </div>
        <div class="col-sm-3"><label for="meta_title">Meta Title:</label>
            <span class="form-control">{{$blog->meta_title}}</span>
        </div>
    </div><br>
    <div class="row">

        <div class="col-sm-4"> <label for="type">Excerpt:</label>
            <span class="form-control">{{$blog->excerpt}}</span>
        </div>
        <div class="col-sm-4"><label for="content">Meta Description:</label>
            <span class="form-control">{!!$blog->meta_description !!}</span>
        </div>
        <div class="col-sm-4"><label for="content">Content:</label>
            <span class="form-control">{!!$blog->content !!}</span>
        </div>
    </div>
</div>

@endsection

@section('script')

@endsection