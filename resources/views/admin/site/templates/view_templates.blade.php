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
        <li class="breadcrumb-item"><a href="{{route('admin.appereance.templates')}}">Templates</a></li>
        <li class="breadcrumb-item active" aria-current="page">View Template</li>
    </ol>
</nav>
<hr>
<h2>View <span class="badge badge-success-lighten">Template</span></h2>
<div id="update_pages_form" class="form-control">
    <div>
        <label for="title">Name:</label>
        <span class="form-control">{{$template->name}}</span>
    </div><br>
    <div class="row">
        <div class="col-sm-4"> <label for="slug">Status:</label>
            <span class="form-control">{{$template->status}}</span>
        </div>
        <div class="col-sm-4"> <label for="type">Template Type:</label>
            <?php
            if ($template->template_type == "0") {
                $template_type = "HTML";
            } else if ($template->template_type == "1") {
                $template_type = "Plain Text";
            }
            ?>
            <span class="form-control">{{$template_type}}</span>
        </div>
        <div class="col-sm-4"><label for="status">Template for:</label>
            <?php
            if ($template->template_for == "0") {
                $template_for = "Website";
            } else if ($template->template_for == "1") {
                $template_for = "Merchant";
            }
            ?>
            <span class="form-control">{{$template_for}}</span>
        </div>
    </div><br>
    <div class="row">
        <div class="col-sm-4"><label for="meta_title">Sender Name:</label>
            <span class="form-control">{{$template->sender_name}}</span>
        </div>
        <div class="col-sm-4"><label for="content">Sender Email:</label>
            <span class="form-control">{{$template->sender_email}}</span>
        </div>
        <div class="col-sm-4">
            <label for="title">Subject:</label>
            <span class="form-control">{{$template->subject}}</span>
        </div>
    </div><br>
    <div>
        <label for="title">Short Code:</label>
        <span class="form-control">{{$template->short_codes}}</span>
    </div>
    <div>
        <label for="title">Body:</label>
        <span class="form-control">{{$template->body}}</span>
    </div>

</div>

@endsection

@section('script')
@endsection