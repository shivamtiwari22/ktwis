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
        <li class="breadcrumb-item"><a aria-current="page" href="{{route('commision.list')}}">Commisions</a></li>
        <li class="breadcrumb-item"><a aria-current="page">View</a></li>
    </ol>
</nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('commision.list')}}" class="btn btn-primary">View All Commisions</a>
        </div>
        <form id="business_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Business Area<span class="text-danger">*</span></label>
                    <select class="form-select" id="example-select" name="business_area" disabled>
                        @foreach($business as $business)
                            <option value="{{ $business->id }}" {{ $business->id == $commision->business_area_fk_id ? 'selected' : '' }}>
                                {{ $business->name}}
                            </option>
                        @endforeach  
                    </select>
                    <input type="hidden" value="{{$commision->id}}" name="id">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Countries<span class="text-danger" >*</span></label>
                      <select name="countries[]" multiple id="country" class="select2 form-control select2-multiple" data-toggle="select2" disabled>
                        @php  $countries_array =  explode(',',$commision->countries);   @endphp
                        @foreach($countries as $item)
                        <option value="{{ $item->id }}"   {{ in_array( $item->id, $countries_array) ? 'selected': ''}}>
                            {{ $item->country_name}}
                        </option>
                    @endforeach
                      </select>
                      <label for="country" id="country-error" class="error"></label>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Platform Charge (in %)<span class="text-danger">*</span></label>
                    <input type="text" value="{{$commision->platform_charges}}" class="form-control" oninput="this.value = !!this.value && Math.abs(this.value) >=0 ? Math.abs(this.value) : null" name="platform_charges" id="platform" readonly>

                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Transaction Charge  (in %)<span
                            class="text-danger">*</span></label>
                    <input type="text" value="{{$commision->transaction_charges}}" required oninput="this.value = !!this.value && Math.abs(this.value) >=0 ? Math.abs(this.value) : null" class="form-control" name="transaction_charges" id="transaction" readonly>
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Total charge  (in %)</label>
                    <input type="text" required value="{{$commision->total_charges}}" class="form-control" id="amount" readonly name="total_charges" readonly>
                </div>
            </div>

                <div class="row">
                    <label for="simpleinput" class="form-label">Status<span class="text-danger">*</span></label>
                    <div class="mb-3">
                        <input type="hidden" name="status" value="{{$commision->status}}">
                        <input type="checkbox" id="switch2" {{ $commision->status == "1" ? 'checked' : '' }} data-switch="primary" value={{$commision->status}} onclick="updateCheckboxValue(this)" disabled>
                        <label for="switch2" data-on-label="On" data-off-label="Off"></label>          
                    </div>
                </div>
          
        </form>
    </div>
@endsection

@section('script')
@endsection
