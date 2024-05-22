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
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a aria-current="page">Settings</a></li>
        <li class="breadcrumb-item"><a aria-current="page" href="{{route('currencies.list')}}">Currencies</a></li>
        <li class="breadcrumb-item"><a aria-current="page">View</a></li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('currencies.list')}}" class="btn btn-primary">View All Currencies</a>
        </div>
        <form id="currency_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="currency_name" value="{{$currency->currency_name}}" disabled>
                    <input type="hidden" value="{{$currency->id}}" name="id">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency Code<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="currency_code" value="{{$currency->currency_code}}" disabled>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency Flag</label><div><img src="{{ url('public/admin/setting/currency/currency_flag/' . $currency->currency_flag) }}" alt="{{ $currency->currency_flag }}" width="100px"></div>
                </div>
                <div class="mb-3 col-lg-6">
                    <div class="mb-3 col-lg-6">
                        <label for="simpleinput" class="form-label">Currency Symbol <span class="text-danger">*</span></label>
                         <input type="text" class="form-control" name="symbol"  value="{{$currency->symbol}}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
              
            </div>
        </form>
    </div>
@endsection

@section('script')
@endsection
