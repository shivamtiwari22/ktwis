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
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.zones.index') }}">Shipping Zones </a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">

        <form id="coupon_form">
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Zone Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" placeholder="Zone Name" id="name" name="name">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Tax<span class="text-danger">*</span></label>
                    <select class="form-select" id="tax" name="tax">
                        <option value="" selected disabled>Select Tax</option>
                        @foreach ($tax as $item)
                            <option value="{{ $item->id }}">{{ $item->tax_name }}</option>
                        @endforeach
                    </select>
                    <label for="tax" class="error" id="tax-error"></label>

                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="example-select" class="form-label">Country<span class="text-danger">*</span></label>
                    <select class="form-select" id="country" name="country">
                        <option value="" selected disabled>Select Country</option>
                        @foreach ($country as $country)
                            <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                        @endforeach
                    </select>
                    <label for="country" class="error" id="country-error"></label>
                </div>


                <div class="mb-3 col-lg-6">
                    <label for="example-select" class="form-label">State<span class="text-danger">*</span></label>
                    <select class="form-select" id="state" name="state">
                        <option value="" selected disabled>Select State</option>
                    </select>
                    <label for="state" class="error" id="state-error"></label>
                </div>

            </div>


            <div class="row">
                <label for="simpleinput" class="form-label">Status</label>
                <div class="mb-3">
                    <input type="hidden" name="status" value="1">
                    <input type="checkbox" id="switch2" checked data-switch="primary" value="1"
                        onclick="updateCheckboxValue(this)">
                    <label for="switch2" data-on-label="On" data-off-label="Off"></label>
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            $('#coupon_form').validate({
                rules: {
                    "country": {
                        required: true,
                    },
                    "tax": {
                        required: true,
                    },
                    "state": {
                        required: true,
                    },

                    name: {
                        required: true,
                        maxlength: 256,
                    },

                },
            })
        })
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
        $(function() {
            $(document).on('submit', '#coupon_form', function(e) {
                e.preventDefault();

                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.zones.store') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result);
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                });

            })


        });
    </script>
    <script>
        function updateCheckboxValue(checkbox) {
            var hiddenInput = document.querySelector('input[name="status"][type="hidden"]');
            hiddenInput.value = checkbox.checked ? 1 : 0;
        }
    </script>
@endsection
