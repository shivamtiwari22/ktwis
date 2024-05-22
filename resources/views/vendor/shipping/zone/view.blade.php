@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('vendor.zones.index')}}">Zones </a></li>
        <li class="breadcrumb-item active" aria-current="page">View Zone</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.zones.index')}}" class="btn btn-primary">View All Zones</a>
    </div>
    <div class="row">
        <div class="mb-3 col-lg-6">
            <label for="simpleinput" class="form-label">Name</label>
            <input type="hidden" class="form-control" name="id" id="id" value="{{ $zones->id }}">
            <input type="text" class="form-control" name="name" id="name" value="{{ $zones->zone_name }}" disabled>
        </div>
        <div class="mb-3 col-lg-6">
            <label for="example-select" class="form-label">Country</label>
            <select class="select2 form-control select2-multiple" data-toggle="select2" disabled multiple="multiple" data-placeholder="Choose ..." name="country[]">
                @foreach ($country as $countryItem)
                @php
                $selected = in_array($countryItem->country_name, $storedCountryNames) ? 'selected' : '';
                @endphp
                <option value="{{ $countryItem->id }}" {{ $selected }}>{{ $countryItem->country_name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="row">
        <label for="simpleinput" class="form-label">Status</label>
        <div class="mb-3">
            <input type="hidden" name="status" value="1">
            <input type="checkbox" id="switch2" checked data-switch="primary" disabled value="1" onclick="updateCheckboxValue(this)">
            <label for="switch2" data-on-label="On" data-off-label="Off"></label>
        </div>
    </div>
</div>
@endsection


@section('script')

@endsection