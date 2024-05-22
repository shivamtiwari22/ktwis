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
        <li class="breadcrumb-item"><a aria-current="page" href="{{route('business.list')}}">Business Area</a></li>
        <li class="breadcrumb-item"><a aria-current="page">View</a></li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('business.list')}}" class="btn btn-primary">View All Business Area</a>
        </div>
        <form id="business_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{$business->name}}" disabled>
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Full Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="full_name" value="{{$business->full_name}}" disabled>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">ISO Code<span class="text-danger">*</span></label><span tabindex="0" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top" data-bs-content="ISO 3166_2 code. For example, Example: For United States of America the code is US">
                        <i class="dripicons-question"></i></span>
                    <input type="text" class="form-control mb-1" name="iso_code" value="{{$business->iso_code}}" disabled>
                    <a href="https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes" target="_blank" class="text-secondary">https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes</a>
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Flag</label>
                    <div>
                        @if($business->flag)
                            <img src="{{ url('public/admin/setting/business/flag/' . $business->flag) }}" alt="{{ $business->name }}" height="40px">
                        @endif
                    </div>
                    
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Calling Code</label>
                    <input type="text" class="form-control" name="calling_code" value="{{$business->calling_code}}" disabled>
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Currency<span class="text-danger">*</span></label>
                    <select class="form-select" id="example-select" name="currency" disabled>
                    @foreach($currencies as $currency)
                        <option value="{{ $currency->id }}" {{ $currency->id == $business->Currency_fk_id ? 'selected' : '' }}>
                            {{ $currency->currency_name }}
                        </option>
                    @endforeach        
                    </select>
                </div>
            </div>
            <div class="row">
                <label for="simpleinput" class="form-label">Status<span class="text-danger">*</span></label>
                <div class="mb-3">
                    <input type="hidden" name="status" value="0">
                    <input type="checkbox" id="switch2" {{ $business->status == "1" ? 'checked' : '' }} data-switch="primary" name="status" value={{$business->status}} onclick="updateCheckboxValue(this)" disabled>
                    <label for="switch2" data-on-label="On" data-off-label="Off"></label>          
                </div>
            </div>
            {{-- <div>
                <button type="submit" class="btn btn-success">Submit</button>
            </div> --}}
        </form>
    </div>
@endsection

@section('script')
@endsection
