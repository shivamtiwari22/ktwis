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
        <li class="breadcrumb-item"><a href="{{route('vendor.zones.index')}}">Shipping Zones </a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Zone</li>
    </ol>
</nav>
{{-- <div class="card p-4 mt-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.zones.index')}}" class="btn btn-primary">View Zones</a>
    </div>
    <form id="coupon_form">
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="simpleinput" class="form-label">Name<span class="text-danger">*</span></label>
                <input type="hidden" class="form-control" name="id" id="id" value="{{ $zones->id }}">
                <input type="text" class="form-control" name="name" id="name" value="{{ $zones->zone_name }}">
            </div>
            <div class="mb-3 col-lg-6">
                <label for="example-select" class="form-label">Country<span class="text-danger">*</span></label>
                <select class="select2 form-control select2-multiple" data-toggle="select2" id="country" multiple="multiple" data-placeholder="Choose ..." name="country[]">
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
                <input type="checkbox" id="switch2" checked data-switch="primary" value="1" onclick="updateCheckboxValue(this)">
                <label for="switch2" data-on-label="On" data-off-label="Off"></label>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-success">Update</button>
        </div>
    </form>
</div> --}}
<div class="card p-4 mt-4">

    <form id="coupon_form">
        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="simpleinput" class="form-label">Zone Name<span class="text-danger"></span></label>
                <input type="hidden" class="form-control" name="id" value="{{$zones->id}}" id="id">

                <input type="text" class="form-control"  id="name" name="name" value="{{$zones->name}}">
            </div>
            <div class="mb-3 col-lg-6">
                <label for="simpleinput" class="form-label">Tax<span class="text-danger"></span></label>
                <select class="form-select" id="tax" name="tax">
                 
                    @foreach ($taxes as $item)
                    <option value="{{ $item->id }}" {{ $selectedTaxId == $item->id ? 'selected' : '' }}>
                        {{ $item->tax_name }}
                    </option>
                @endforeach
                </select>
                <label for="tax" class="error" id="tax-error"></label>

            </div>
        </div>

        <div class="row">
            <div class="mb-3 col-lg-6">
                <label for="example-select" class="form-label">Country<span class="text-danger"></span></label>
                <select class="form-select" id="country" name="country">
                    @foreach ($country as $item)
                    <option value="{{ $item->id }}" {{ $selectcountry == $item->id ? 'selected' : '' }}>
                        {{ $item->country_name }}
                    </option>
                @endforeach
                  
                </select>
                <label for="country" class="error" id="country-error"></label>
            </div>


            <div class="mb-3 col-lg-6">
                <label for="example-select" class="form-label">State<span class="text-danger"></span></label>
                <select class="form-select" id="state" name="state">
                    @foreach ($state as $item)
                    <option value="{{ $item->id }}" {{ $selectstate == $item->id ? 'selected' : '' }}>
                        {{ $item->state_name }}
                    </option>
                @endforeach
                </select>
                <label for="state" class="error" id="state-error"></label>
            </div>

        </div>


        <div class="row">
            
            <label for="simpleinput" class="form-label">Status</label>
            <div class="mb-3">
         
              
                <select class="form-select" id="state" name="status">
                    <option value="1" {{ $zones->status == 0 ? 'selected' : '' }}>Inactive</option>
                    <option value="0" {{ $zones->status == 1 ? 'selected' : '' }}>Active</option>
                </select>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-warning text-white">Update
        </div>
    </form>
</div>


<div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-warning"></i>
                    <h4 class="mt-2">Warning</h4>
                    <p class="mt-3">Are you sure you want to delete</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection


@section('script')

<script>
    $(function() {
        $('#coupon_form').on('submit', function(e) {
            e.preventDefault();
            if (validateForm()) {
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.zones.update_zone') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result.location);
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error")
                        }
                    },
                });
            }
        })

        function validateForm() {
            var isValid = true;

            $('.error-message').remove();

            $('#name ,#country').each(function() {
                if ($.trim($(this).val()) === '') {
                    var errorMessage = 'This field is required';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            $('#name ,#country').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            });

            return isValid;
        }
    });
</script>
<script>
    $(document).on('change', '#country', function(e) {
        e.preventDefault();
        let id = $(this).val();
        console.log(id);

        $.ajax({
            url: "{{ route('vendor.getStates') }}",
            type: "POST",
            data: {
                id: id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(result) {
                console.log(result);
                if (result.status) {
                        $('#state').html('<option value="" selected disabled>Select State</option>');
                    $.each(result.data, function(key, val) {
                       
                        $('#state').append(`
                        <option value="${val.id}" >${val.state_name}</option>
                        `);
                    });
                } else {
                    $.NotificationApp.send("Error", result.msg, "top-right",
                        "rgba(0,0,0,0.2)", "error")
                }
            },
        });
    })
</script>
<script>
    function updateCheckboxValue(checkbox) {
        var hiddenInput = document.querySelector('input[name="status"][type="hidden"]');
        hiddenInput.value = checkbox.checked ? 1 : 0;
    }
</script>
@endsection