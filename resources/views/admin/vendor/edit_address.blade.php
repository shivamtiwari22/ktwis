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
            <li class="breadcrumb-item"><a href="{{ route('admin.vendor.applications') }}">Merchants</a></li>
            <li class="breadcrumb-item active" aria-current="page">Address</li>
        </ol>
    </nav>
    <div class="card p-2 mt-1">
        <div class=" mb-2">
            <h4>Form</h4>
        </div>
        <form id="address-form">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="name" class="with-help">Address Line 1 <span class="error">*</span></label>
                        <input class="form-control" placeholder="Address Line 1" required
                            value="{{ $shop_address ? $shop_address->address_line1 : '' }}" name="address_line1"
                            type="text" id="name">
                        <input name="shop_id" type="hidden" value="{{ $shop->id }}">
                        <input name="address_id" type="hidden" value="{{ $shop_address ? $shop_address->id : '' }}">
                    </div>
                </div>

                <div class="col-sm-6">
                    <label for="">Address Line 2  <span class="error">*</span></label>
                    <input class="form-control" placeholder="Address Line 2" name="address_line2" required
                        value="{{ $shop_address ? $shop_address->address_line2 : '' }}" type="text" id="">
                </div>

            </div>
            <div class="row mt-2">

                <div class="col-sm-4">
                    <label for="active" class="with-help">City <span class="error">*</span></label>
                    <input class="form-control" placeholder="City" name="city" type="text" required
                        value="{{ $shop_address ? $shop_address->city : '' }}" id="">

                </div>
                <div class="col-sm-4">
                    <label for="">Zip/Postal Code <span class="error">*</span></label>

                    <input class="form-control" placeholder="Zip/Postal Code" name="postal_code" required
                        value="{{ $shop_address ? $shop_address->postal_code : '' }}" type="text" id="">
                </div>

                <div class="col-sm-4">
                    <label for="">Phone <span class="error">*</span></label>
                    <input class="form-control" placeholder="Phone" name="phone" type="number" required min="1"
                        value="{{ $shop_address ? $shop_address->phone : '' }}" id="mini_order">
                </div>
            </div>
            <div class="row mt-2">
               
                <div class="col-sm-6">
                    <label for="">County <span class="error">*</span></label>
                    <select class="form-select" id="country" name="country" required>
                        <option value="" selected disabled>Select Country</option>
                        @foreach ($country as $country)
                            <option value="{{ $country->id }}"
                                {{ $shop_address ? ($shop_address->country == $country->id ? 'selected' : '') : '' }}>
                                {{ $country->country_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-6">
                    <label for="">State <span class="error">*</span></label>
                    <select class="form-select" id="state" name="state" required>
                        <option value="" selected disabled>Select State</option>
                        @foreach ($state as $item)
                            <option value="{{ $item->id }}"
                                {{ $shop_address ? ($shop_address->state == $item->id ? 'selected' : '') : '' }}>
                                {{ $item->state_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-2">
                <button type="submit" class="btn btn-success">Submit</button>
            </div>

        </form>

    </div>
@endsection

@section('script')
    <script>



$('#address-form').validate({
                rules: {
                    naddress_line1: {
                        required: true,
                        maxlength: 46,
                    },
                   
                    address_line2: {
                        required: true,
                        maxlength:46,

                    },
                    city: {
                        required: true,
                    },
                    postal_code: {
                        required: true,
                        maxlength:26,
                        minlength:6,
                        number:true

                    },
                    phone: {
                        required:true,
                        maxlength:12,
                        minlength:8

                    },
                    country : {
                        required:true
                    },
                    state : {
                        required:true
                    }

                },
                messages: {
                    profile_pic: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                    postal_code : {
                        number: "Please enter a valid zip code"
                    }
                },
            });



        $('body').on('submit','#address-form', function(e) {
            e.preventDefault();

            let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");

            $.ajax({
                    url: "{{ route('admin.vendor.applications.update.address') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                })
                .done(function(result) {
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)",
                            "success");
                        setTimeout(function() {
                            window.location.href = result.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                    }
                })
                .fail(function(jqXHR, exception) {
                    console.log(jqXHR.responseText);
                });
        });
    </script>


<script>
    $(document).on('change', '#country', function(e) {
        e.preventDefault();
        let id = $(this).val();
        console.log(id);

        $.ajax({
            url: "{{ route('admin.getStates') }}",
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
@endsection
