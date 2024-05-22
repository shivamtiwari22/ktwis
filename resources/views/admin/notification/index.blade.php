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
            <li class="breadcrumb-item active" aria-current="page">Send Notification</li>
        </ol>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card mt-3">
                    <div class="card-body">
                        @if (session('message'))
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                        @endif
                        <form action="{{ route('admin.send-notification') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="">TO <span class="text-danger">*</span></label>
                                <select name="send_to" id="" class="form-select" required>
                                    <option value="" selected disabled>Select</option>
                                    <option value="customer">All Customer</option>
                                    <option value="vendor">All Vendor</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>
                         
                            <div class="form-group mt-2">
                                <label>Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="message" required ></textarea>
                            </div>
                            <button type="submit" class="btn btn-success btn-block mt-2">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
@endsection
