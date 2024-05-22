@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css"
        rel="stylesheet">
    <style>
        .switch.ios,
        .switch-on.ios,
        .switch-off.ios {
            border-radius: 20rem;
        }

        .switch.ios .switch-handle {
            border-radius: 20rem;
        }
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Setting</a></li>
            <li class="breadcrumb-item active" aria-current="page">General Setting</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <form id="rate_form">
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Shop Name<span class="text-danger">*</span></label>
                    <input type="hidden" name="shop_id" id="shop_id" value="{{ $shop->id ?? '' }}">
                    <input type="text" class="form-control" name="shop_name" id="shop_name"
                        value="{{ $shop->shop_name ?? '' }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label">Shop URL </label>
                    <input type="text" class="form-control" name="shop_url" id="shop_url"
                        value="{{ $shop->shop_url ?? '' }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Legal Name<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="legal_name" name="legal_name"
                        value="{{ $shop->legal_name ?? '' }}">
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label for="example-select" class="form-label">Email<span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="{{ $shop->email ?? '' }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label class="form-label">TimeZone<span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="timezone" id="timezone"
                        value="{{ $shop->timezone ?? '' }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label class="form-label">Description</label>
                    <textarea type="text" class="form-control" name="description" id="description">{{ $shop->description ?? '' }}</textarea>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label class="form-label">Brand Logo (jpg, jpeg, png, 2MB max)</label>
                    <input type="file" name="brand_logo" id="brand_logo" class="form-control" accept="image/*"
                        value="{{ $shop->brand_logo ?? '' }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label class="form-label">Cover Logo (jpg, jpeg, png, 2MB max)</label>
                    <input type="file" name="cover_logo" id="cover_logo" class="form-control" accept="image/*"
                        value="{{ $shop->cover_image ?? '' }}">
                </div>
                <div class="mb-3 col-lg-4">
                    <label for="simpleinput" class="form-label"> Maintenance Mode </label><span tabindex="0"
                        data-bs-toggle="popover" data-bs-trigger="hover" data-bs-placement="top"
                        data-bs-content="If maintenance mode is on, the shop will be offline and all listings will be down from the marketplace until maintenance off.">
                        <i class="dripicons-question"></i></span>
                    <br>
                    <input type="checkbox" data-toggle="switchbutton" id="switch"
                        {{ $shop->maintenance_mode == 1 ? 'checked' : '' }} class="mode" data-style="ios">


                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-4">
                    <label class="form-label">Banner Img (jpg, jpeg, png, 2MB max)</label>
                    <input type="file" name="banner_img" id="banner_img" class="form-control" accept="image/*"
                        value="{{ $shop->banner_img ?? '' }}">
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </form>

        <div class="row mt-3">
            <div class="mb-3 col-lg-6">
                <p> <strong>SHOP ADDRESS</strong> </p>
                <label class="form-label">Adress : {{ $shop_address ? $shop_address->address_line1 : '' }} </label> <br>

                <label class="form-label">City: {{ $shop_address ? $shop_address->city : '' }}</label><br>
                <label class="form-label">Phone: {{ $shop_address ? $shop_address->phone : '' }}</label><br>
                <label class="form-label">Country:
                    {{ $shop_address ? $countryPluck[$shop_address->country] : '' }}</label><br>
                <label class="form-label">State: {{ $shop_address ? $statePluck[$shop_address->state] : '' }}</label><br>
                <button class="btn btn-secondary" data-toggle="modal" data-target="#exampleModal">Update Address</button>
            </div>

            <div class="mb-3 col-lg-6">
                <p> <strong>BANK INFORMATION </strong> </p>
                <label class="form-label">Account holder name :
                    {{ $bank_detail ? $bank_detail->account_holder_name : '' }}</label> <br>
                <label class="form-label">Account number:
                    {{ $bank_detail ? $bank_detail->account_number : '' }}</label><br>
                <label class="form-label">SWIFT/BIC code: {{ $bank_detail ? $bank_detail->bic_code : '' }} </label><br>
                <label class="form-label">Account Type: {{ $bank_detail ? $bank_detail->account_type : '' }}</label><br>
                <label class="form-label">Bank Address: {{ $bank_detail ? $bank_detail->bank_address : '' }}</label><br>

                <button class="btn btn-secondary" data-toggle="modal" data-target="#bank_details">Update Bank
                    Detail</button>

            </div>
        </div>


    </div>

    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm my-data">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Confirmation!</h4>
                        <p class="mt-3">Are you sure you want to do this ?</p>
                        <button type="button" class="btn btn-warning my-2" id="confirmation">Proceed</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal"
                            id="cancel">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>


    <!-- Address Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="address-form">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="with-help">Address Line 1 <span>*</span></label>
                                    <input class="form-control" placeholder="Address Line 1" required
                                        value="{{ $shop_address ? $shop_address->address_line1 : '' }}"
                                        name="address_line1" type="text" id="name">
                                    <input name="shop_id" type="hidden" value="{{ $shop->id }}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="">Address Line 2 <span>*</span></label>
                                <input class="form-control" placeholder="Address Line 2" name="address_line2" required
                                    value="{{ $shop_address ? $shop_address->address_line2 : '' }}" type="text"
                                    id="">
                            </div>

                        </div>
                        <div class="row mt-2">

                            <div class="col-sm-6">
                                <label for="active" class="with-help">City <span>*</span></label>
                                <input class="form-control" placeholder="City" name="city" type="text" required
                                    value="{{ $shop_address ? $shop_address->city : '' }}" id="">

                            </div>
                            <div class="col-sm-6">
                                <label for="">Zip/Postal Code <span>*</span></label>

                                <input class="form-control" placeholder="Zip/Postal Code" name="postal_code" required
                                    value="{{ $shop_address ? $shop_address->postal_code : '' }}" type="text"
                                    id="">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="">Phone <span>*</span></label>
                                <input class="form-control" placeholder="Phone" name="phone" type="number" required min="0"
                                    value="{{ $shop_address ? $shop_address->phone : '' }}" id="mini_order">
                            </div>
                            <div class="col-sm-6">
                                <label for="">County <span>*</span></label>
                                <select class="form-select" id="country" name="country" required>
                                    <option value="" selected disabled>Select Country</option>
                                    @foreach ($country as $country)
                                        <option value="{{ $country->id }}"
                                            {{ $shop_address ? ($shop_address->country == $country->id ? 'selected' : '') : '' }}>
                                            {{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="">State <span>*</span></label>
                                <select class="form-select" id="state" name="state" required>
                                    <option value="" selected disabled>Select State</option>
                                    @if ($shop_address)
                                        @foreach ($state as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $shop_address ? ($shop_address->state == $item->id ? 'selected' : '') : '' }}>
                                                {{ $item->state_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary mt-n1">Save</button>
                </div>
                </form>
            </div>

        </div>
    </div>
    </div>




    <!-- Bank Modal -->
    <div class="modal fade" id="bank_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="bank-form">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name" class="with-help">Account Holder Name <span>*</span></label>
                                    <input class="form-control" placeholder="Account Holder Name" required
                                        name="account_holder_name" type="text" id="name"
                                        value="{{ $bank_detail ? $bank_detail->account_holder_name : '' }}">

                                    <input name="shop_id" type="hidden" value="{{ $shop->id }}">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <label for="">Account Number <span>*</span></label>
                                <input class="form-control" placeholder="Account Number" required name="account_number"
                                    value="{{ $bank_detail ? $bank_detail->account_number : '' }}" type="text"
                                    id="">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="">Account Type <span>*</span></label>
                                <input class="form-control" placeholder="Account Type" name="account_type"
                                    value="{{ $bank_detail ? $bank_detail->account_type : '' }}" type="text" required
                                    id="mini_order">
                            </div>
                            <div class="col-sm-6">
                                <label for="">Routing Number <span>*</span></label>
                                <input class="form-control" placeholder="Routing Number" name="routing_number"
                                    value="{{ $bank_detail ? $bank_detail->routing_number : '' }}" type="text"
                                    required id="mini_order">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="">Swift/Bic Code <span>*</span></label>
                                <input class="form-control" placeholder="Swift/Bic Code" required name="bic_code"
                                    value="{{ $bank_detail ? $bank_detail->bic_code : '' }}" type="text" 
                                    id="">
                            </div>

                            <div class="col-sm-6">
                                <label for="">Iban Number <span>*</span></label>
                                <input class="form-control" placeholder="Iban Number" required name="iban_number"
                                    value="{{ $bank_detail ? $bank_detail->iban_number : '' }}" type="text"
                                    id="iban_number">
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <label for="">Bank Address </label>
                                <input class="form-control" placeholder="Bank Address" required name="bank_address"
                                    value="{{ $bank_detail ? $bank_detail->bank_address : '' }}" type="text"
                                    id="">
                            </div>

                        </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary mt-n1">Save</button>
                </div>
                </form>
            </div>

        </div>
    </div>
    </div>
@endsection


@section('script')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js">
    </script>
    <script>
        $(document).ready(function() {
            $('#rate_form').validate({
                rules: {
                    shop_name: {
                        required: true,
                    },
                    legal_name: {
                        required: true,
                    },
                    email: {
                        rangelength: [2, 256],
                        maxlength: 256,
                        email: true,
                        emailWithDomain: true

                    },
                    timezone: {
                        required: true,
                    },
                    brand_logo: {
                        imageFormat: true,
                        filesize: 2024
                    },
                    cover_logo: {
                        imageFormat: true,
                        filesize: 2024
                    },
                    banner_img: {
                        imageFormat: true,
                        filesize: 2024
                    },
                    shop_url: {
                        linkvalid: true,
                    }

                },
                messages: {
                    brand_logo: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                    cover_logo: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                    banner_img: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    }


                },
            });

            $.validator.addMethod("emailWithDomain", function(value, element) {
                return this.optional(element) || /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(value);
            }, "Please enter a valid email address.");
            $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");
            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");

            $.validator.addMethod("linkvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please enter valid url.");
        })
    </script>



    <script>
        $(function() {

            $(document).on('submit', '#rate_form', function(e) {
                e.preventDefault();

                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.setting.shops.store') }}",
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
            });

        });
    </script>

    <script>
        $(document).on('change', '#switch', function() {
            var check = $(this).prop('checked');
            var value = null;
            if (check) {
                value = 1;
            } else {
                value = 0;
            }

            $('#cancel').on('click', function() {
                if (value == 1) {
                    console.log(value);
                    $('#switch').prop("checked", false);
                }

                if (value == 0) {
                    $('#switch').prop("checked", true);
                }

                window.location.reload();

            });


            $('#warning-alert-modal').modal('show');

            $('#confirmation').on('click', function() {
                $.ajax({
                    url: "{{ route('vendor.update_maintenance_mode') }}",
                    type: 'POST',
                    data: {
                        value: value
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {

                        console.log(result);
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            $('#warning-alert-modal').modal('hide');

                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error");
                        }
                    },

                    error: function(jqXHR, exception) {
                        console.log(jqXHR.responseText); // Log the error for debugging purposes
                    }

                })
            });
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
        $(document).on('submit', '#address-form', function(e) {
            e.preventDefault();
          

            let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('vendor.setting.shops.address_update') }}",
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
                        window.location.reload();

                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "error")
                    }
                },
            });
        });
    </script>


    <script>

$('#bank-form').validate({
                rules: {
                    account_holder_name: {
                        required: true,
                    },
                    account_number: {
                        required: true,
                        pattern :true
                    },
                    account_type: {
                        required: true,
                    },
                    routing_number : {
                        required: true,
                        pattern :true

                    },
                    bic_code: {
                        required: true,
                        
                    },
                    iban_number : {
                        required: true,

                    },
                    bank_address :{
                        required: true,
                        
                    }
                }
            });

            $.validator.addMethod("pattern", function(value, element, param) {
    // Customize the regular expression pattern according to your requirements
    var pattern = /^[a-zA-Z0-9\s]+$/;
    return this.optional(element) || pattern.test(value);
}, "Special Charater not allowed.");


        $(document).on('submit', '#bank-form', function(e) {
            e.preventDefault();
            var ibanInput = $('#iban_number').val();
            var regex = /^[a-zA-Z0-9]+$/; // Only allow alphanumeric characters

            if (!regex.test(ibanInput)) {
                $.NotificationApp.send("Error",'Special characters are not allowed in the IBAN number.', "top-right",
                            "rgba(0,0,0,0.2)", "error")
            }
            else {
                let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('vendor.setting.shops.bank_update') }}",
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
                        window.location.reload();

                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "error")
                    }
                },
            });
            }
            
           
        });
    </script>
@endsection
