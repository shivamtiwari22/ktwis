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
        <li class="breadcrumb-item"><a aria-current="page" href="{{route('languages.list')}}">Languages</a></li>
        <li class="breadcrumb-item"><a aria-current="page">View</a></li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('languages.list')}}" class="btn btn-primary">View All Languages</a>
        </div>
        <form id="business_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Language<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="language" value="{{$language->language}}" disabled>
                    
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Order</label><span tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="The position you want to show this language on the language option. The smallest number will display first.">
                        <i class="dripicons-question"></i></span>
                    <input type="number" class="form-control" name="order" value="{{$language->order}}" disabled>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Code<span class="text-danger">*</span></label><span tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="The locale code, the code must have the same name as the language folder.">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control" name="code" value="{{$language->code}}" disabled>
                    
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Flag <span class="fw-light">(png/jpg/svg)</sapn></label>
                        @if ($language->flag)
                        <div><img src="{{ url('public/admin/setting/language/flag/' . $language->flag) }}" alt="{{ $language->language }}" height="40px"></div>
                    @endif                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">PHP LOCALE CODE<span class="text-danger">*</span></label><span tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-content="The PHP locale code for system use like translating date, time etc. Please find the full list of the PHP locale code on the documentation.">
                        <i class="dripicons-question"></i>
                    </span>
                    <input type="text" class="form-control" name="php_locale_code" value="{{$language->php_locale_code}}" disabled>
                </div>
            </div>
            <div class="row">
                <label for="simpleinput" class="form-label">Status<span class="text-danger">*</span></label>
                <div class="mb-3">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" id="switch2" {{ $language->status == "1" ? 'checked' : '' }} data-switch="primary" name="status" value={{$language->status}} onclick="updateCheckboxValue(this)" disabled>
                    <label for="switch2" data-on-label="On" data-off-label="Off"></label>          
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
@endsection
