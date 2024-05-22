@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
@endsection


@section('css')
    <style>
        .container {
            display: flex;
            align-items: center;
        }

        blockquote {
            margin: 20px 0 30px;
            padding-left: 20px;
            border-left: 5px solid #1371b8;
        }

        .card-padding {
            padding: 0.5rem 1.5rem;
        }

        .customer-card {
            padding: 0.5rem 0.5rem;
        }

        #product-card-header {
            padding: 0px;
        }

        .label-info {
            background-color: #00c0ef !important;
            color: white;
            padding: 0px 4px;
        }

        .label-outline {
            background-color: transparent;
            border: 1px solid #d2d6de;
            padding: 0px 4px;
        }

        .img-circle {
            border-radius: 50%;
            max-width: 30px;
            max-height: 30px;
        }

        .nopadding {
            padding: 0;
            margin: 0;
        }

        .input-lg {
            height: 48px !important;
            border-radius: 0px;
        }

        .select2-container .select2-selection--single {
            height: 48px;
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            line-height: 45px;
        }

        .table>tbody {
            vertical-align: unset;
        }

        .quantity-input {
            width: 46px;
            border: none;
        }

        .input_value {
            width: 46px;
            border: none;
        }

        .pull-right {
            float: right;
        }

        hr {
            margin: 0.5rem 0;
        }


 

        .iti--allow-dropdown {
            width: 100%;
        }

        .input_flag {
            padding: 0px 0 0 54px !important;
            height: 40px;
        }
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.order.index') }}">Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Order</li>


            <div class="d-flex justify-content-end mb-2" style="position: absolute;  right:3%">
                <a href="{{ route('vendor.order.index') }}" class="btn btn-secondary">Cancel</a>
            </div>


        </ol>



    </nav>


    <div class="row">
        <img src="https://www.google.com/url?sa=i&url=https%3A%2F%2Flaraget.com%2Fblog%2Fhow-to-create-an-ajax-pagination-using-laravel&psig=AOvVaw1TCuO_ZK5xFhN2YMU-Aoik&ust=1695467843776000&source=images&cd=vfe&ved=0CBAQjRxqFwoTCMjttoaMvoEDFQAAAAAdAAAAABAW"
            alt="">

        <div class="d-flex justify-content-end mb-2">
            {{-- <a href="{{route('vendor.disputes.index')}}" class="btn btn-primary">View All Disputes</a> --}}
        </div>
        <div class="col-sm-9">
            <div class="card ">
                <div class="card-header" id="product-card-header">
                    <div class="container">
                        <i class="fa fa-shopping-cart"></i>
                        <h4 class="ms-1">Cart</h4>

                    </div>
                </div>
                <br>
                <form id="order-place">
                    <div class="card-body card-padding">

                        <div class="row mb-4">
                            <div class="col-md-9 nopadding ">
                                <select name="product" id="product_id" class="form-select select2 input-lg">
                                    <option value="0" selected disabled>Choose an item from here</option>
                                    @foreach ($products as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 nopadding">
                                <button class="btn btn-primary btn-lg" id="getCart">Add To Cart</button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">

                                <span class="spacer10"></span>

                                <table class="table table-sripe" id="product-table">
                                    <tbody id="items">
                                        <tr data-id="new" id="no-data">
                                            <td colspan="4">Cart is empty</td>
                                        </tr>

                                    </tbody>
                                </table>
                            </div>
                        </div>



                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group mt-3">
                                    <label for="Admin">Admin Note</label> <br>
                                    <textarea id="" placeholder="start from here" name="admin_note" cols="40"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td class="text-right">Total</td>
                                            <td class="text-right" width="40%">
                                                $ <span id="table_total">0</span>

                                                <input type="hidden" name="total" value="0" id="total">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="text-right">
                                                <span>Discount</span>
                                            </td>
                                            <td class="text-right" width="50%"
                                                style="
                                        padding-left: 2px;"> −
                                                $ <input type="number" class="input_value" name="discount" min="0"
                                                    id="discount-amount" value="0">

                                            </td>

                                        </tr>

                                        <tr>
                                            <td class="text-right">
                                                <span>Shipping</span><br>
                                                <em class="small"></em>
                                            </td>
                                            <td class="text-right" width="40%">
                                                $ <input type="number" name="shipping" class="input_value" value="0" min="0"
                                                    id="shipping-amount">
                                            </td>

                                        </tr>

                                        <tr>
                                            <td class="text-right">Taxes <br>
                                                <em class="small">
                                                    Worldwide
                                                    0%
                                                </em>
                                            </td>
                                            <td class="text-right" width="40%">
                                                $ 0
                                            </td>
                                        </tr>

                                        <tr class="lead">
                                            <td class="text-right">Grand total</td>
                                            <td class="text-right" width="40%">
                                                $ <span id="table_grand">0</span>

                                                <input type="hidden" name="grand_total" value="0" id="grand">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>



                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="box-tools pull-right">
                                    <button type="submit" id="submit-btn" class="btn btn-secondary">Place the
                                        order</button>
                                </div>

                            </div>
                        </div>

                </form>

            </div>
        </div>
    </div>
    <div class="col-sm-3">
        <div class="card ">
            <div class="card-header">
                <strong> Customer </strong>
            </div>
            <div class="card-body customer-card">

                {{-- <h5 class="card-title"><strong>Amount:</strong> $ 33 </h5> --}}
                <p>
                    @if ($customer->profile_pic)
                        <img src="{{ asset('public/customer/profile/' . $customer->profile_pic) }}" alt="profile"
                            class="img-circle">
                    @else
                        <img src="https://www.gravatar.com/avatar/f2f7ab6bd648ee89508a29d2e94bbe11?s=30&d=mm"
                            alt="profile" class="img-circle">
                    @endif
                    <span>{{ $customer->name }}</span>
                </p>
                <span class="mb-5">Email : {{ $customer->email }} </span>

            </div>
        </div>
        <div class="card mt-2">
            <div class="card-header">
                <strong>Address</strong>
            </div>
            <div class="card-body card-padding" style="padding-left: 0px">
                <h5 class="" style="font-weight: 100; text-align:right">Shipping Address</h5>
                <hr>
                <div style="display: flex; justify-content: space-around;">
                    <address>

                        {{ $shippingAddress ? $shippingAddress->address : '' }} <br>
                        {{ $shippingAddress ? $shippingAddress->city : '' }}<br>
                        {{ $shippingAddress ? $shippingAddress->zip_code : '' }}<br>
                        {{ $shippingAddress ? $shippingAddress->obj_country : '' }} <br>
                        {{ $shippingAddress ? $shippingAddress->obj_state : '' }}<br><abbr title="Phone">P:</abbr>
                        {{ $shippingAddress ? $shippingAddress->contact_no : '' }}
                    </address>

                    <a href="javascript::void(0)" data-toggle="modal" data-target="#exampleModal"><i
                            class="fa fa-edit"></i>Edit</a>

                </div>
                <h5 class="" style="font-weight: 100; text-align:right">Billing Address</h5>
                <hr>
                <small class="ms-2"><input type="checkbox" name="sameAsShipping" id="checkbtn"> <label
                        for=""><strong>SAME AS SHIPPING ADDRESS</strong></label></small>
                <div style="display: flex; justify-content: space-around;" class="mt-2" id="billing-sec">
                    <address>

                        {{ $billingAddress ? $billingAddress->address : '' }} <br>
                        {{ $billingAddress ? $billingAddress->city : '' }}<br>
                        {{ $billingAddress ? $billingAddress->zip_code : '' }}<br>
                        {{ $billingAddress ? $billingAddress->obj_country : '' }} <br>
                        {{ $billingAddress ? $billingAddress->obj_state : '' }}<br><abbr title="Phone">P:</abbr>
                        {{ $billingAddress ? $billingAddress->contact_no : '' }}
                    </address>

                    <a href="javascript::void(0)" data-target="#exampleCenter" data-toggle="modal"><i
                            class="fa fa-edit"></i>Edit</a>

                </div>


            </div>
        </div>


        {{-- <div class="card ">
            <div class="card-header">
                <strong> Payment </strong>
            </div>
            <div class="card-body card-padding">
                <label for="">Payment Method</label>
                <select name="payment_mehtod" id="payment_method" class="form-select">
                    <option value="" selected disabled> Select</option>
                    <option value="FlutterWave">Flutter Wave</option>
                    <option value="PayPal">PayPal</option>
                </select>

                <label for="" class="mt-2">Payment Status</label>
                <select name="payment_status" id="payment_status" class="form-select mb-2">
                    <option value="" selected disabled>select</option>
                    <option value="Unpain">Unpiad</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
        </div> --}}

        {{-- <div class="card ">
            <div class="card-header">
                <strong> Invoice </strong>
            </div>
            <div class="card-body card-padding">
                <label for="">Message To Customer</label>
                <textarea name="message_customer" id="customer_note" class="mt-1" placeholder="Start from here" cols="23"></textarea>
                <br>

                <input type="checkbox" name="send_invoice" id="send_invoice">
                <label for="">Send the Invoice</label>

            </div>
        </div> --}}
    </div>








    {{-- Shipping Model  --}}
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

                        <input type="hidden" name="user_id" value="{{ $customer->id }}" id="customer_id">
                        <input type="hidden" name="contact_person" value="{{ $customer->name }}">
                        <input type="hidden" id="shipping_id"
                            value="{{ $shippingAddress ? $shippingAddress->id : '' }}">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="">Address Type</label>
                                <select name="address_type" id="" class="form-select">
                                    <option value="" required selected disabled>Select</option>
                                    <option value="billing"
                                        @if ($shippingAddress) {{ $shippingAddress->address_type == 'billing' ? 'selected' : '' }} @endif>
                                        Billing</option>
                                    <option value="shipping"
                                        @if ($shippingAddress) {{ $shippingAddress->address_type == 'shipping' ? 'selected' : '' }} @endif>
                                        Shipping</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="with-help">Address Line 1</label>
                                    <input class="form-control" placeholder="Address Line 1" required
                                        value="{{ $shippingAddress ? $shippingAddress->floor_apartment : '' }}"
                                        name="address_line1" type="text" id="name">
                                    <input name="address_id" type="hidden"
                                        value="{{ $shippingAddress ? $shippingAddress->id : '' }}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="">Address Line 2 </label>
                                <input class="form-control" placeholder="Address Line 2" name="address_line2" required
                                    value="{{ $shippingAddress ? $shippingAddress->address : '' }}" type="text"
                                    id="">
                            </div>

                        </div>
                        <div class="row mt-2">

                            <div class="col-sm-6">
                                <label for="active" class="with-help">City</label>
                                <input class="form-control" placeholder="City" name="city" type="text" required
                                    value="{{ $shippingAddress ? $shippingAddress->city : '' }}" id="">

                            </div>
                            <div class="col-sm-6">
                                <label for="">Zip/Postal Code</label>

                                <input class="form-control" placeholder="Zip/Postal Code" name="zip_code" required min="1"
                                    value="{{ $shippingAddress ? $shippingAddress->zip_code : '' }}" type="number"
                                    id="">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <label for="">Phone</label>
                                <input class="form-control w-100 input_flag number-float" placeholder="Phone" name="phone" type="text" id="inputContact" required
                                    value="{{ $shippingAddress ?    $shippingAddress->country_code . $shippingAddress->contact_no : '' }}" id="mini_order">
                            </div>
                            
                        </div>
                      
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="">County</label>
                                <select class="form-select country" id="country" data-type="shipping" name="country"
                                    required>
                                    <option value="" selected disabled>Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            {{ $shippingAddress ? ($shippingAddress->country == $country->id ? 'selected' : '') : '' }}>
                                            {{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="">State </label>
                                <select class="form-select" id="state" name="state" required>
                                    <option value="" selected disabled>Select State</option>
                                    @foreach ($state as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $shippingAddress ? ($shippingAddress->state == $item->id ? 'selected' : '') : '' }}>
                                            {{ $item->state_name }}</option>
                                    @endforeach
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


    {{-- billing Modal  --}}

    <div class="modal fade" id="exampleCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                    <form id="address">
                        <input type="hidden" name="user_id" value="{{ $customer->id }}">
                        <input type="hidden" name="contact_person" value="{{ $customer->name }}">
                        <input type="hidden" id="billing_id" value="{{ $billingAddress ? $billingAddress->id : '' }}">
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="">Address Type</label>
                                <select name="address_type" id="" class="form-select">
                                    <option value="" required selected disabled>Select</option>
                                    <option value="billing"
                                        @if ($billingAddress) {{ $billingAddress->address_type == 'billing' ? 'selected' : '' }} @endif>
                                        Billing</option>
                                    <option value="shipping"
                                        @if ($billingAddress) {{ $billingAddress->address_type == 'shipping' ? 'selected' : '' }} @endif>
                                        Shipping</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="with-help">Address Line 1</label>
                                    <input class="form-control" placeholder="Address Line 1" required
                                        value="{{ $billingAddress ? $billingAddress->floor_apartment : '' }}"
                                        name="address_line1" type="text" id="name">
                                    <input name="address_id" type="hidden"
                                        value="{{ $billingAddress ? $billingAddress->id : '' }}">
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <label for="">Address Line 2 </label>
                                <input class="form-control" placeholder="Address Line 2" name="address_line2" required
                                    value="{{ $billingAddress ? $billingAddress->address : '' }}" type="text"
                                    id="">
                            </div>

                        </div>
                        <div class="row mt-2">

                            <div class="col-sm-6">
                                <label for="active" class="with-help">City</label>
                                <input class="form-control" placeholder="City" name="city" type="text" required
                                    value="{{ $billingAddress ? $billingAddress->city : '' }}" id="">

                            </div>
                            <div class="col-sm-6">
                                <label for="">Zip/Postal Code</label>

                                <input class="form-control" placeholder="Zip/Postal Code" name="zip_code" required min="1"
                                    value="{{ $billingAddress ? $billingAddress->zip_code : '' }}" type="number"
                                    id="">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <label for="">Phone</label>
                                <input class="form-control w-100 input_flag number-float" placeholder="Phone" id="inputPhone" name="phone" type="text" required
                                    value="{{  $billingAddress ?  $billingAddress->country_code.''.$billingAddress->contact_no : '' }}" id="mini_order">
                            </div>  
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="">County</label>
                                <select class="form-select country" id="country" name="country" data-type="billing"
                                    required>
                                    <option value="" selected disabled>Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            {{ $billingAddress ? ($billingAddress->country == $country->id ? 'selected' : '') : '' }}>
                                            {{ $country->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="">State </label>
                                <select class="form-select" id="state_id" name="state" required>
                                    <option value="" selected disabled>Select State</option>
                                    @foreach ($state as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $billingAddress ? ($billingAddress->state == $item->id ? 'selected' : '') : '' }}>
                                            {{ $item->state_name }}</option>
                                    @endforeach
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
@endsection


@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({});
        });
        $(document).on('keypress', '.number-float', function() {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    })

        const phoneInputField = document.querySelector("#inputPhone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        const contactInputField = document.querySelector("#inputContact");
        const contactInput = window.intlTelInput(contactInputField, {
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });

        $(document).on('change', '#checkbtn', function() {
            var isChecked = $(this).prop('checked');
            var shipping_id = $('#shipping_id').val();
            var billing_id = "{{$billingAddress ? $billingAddress->id : ''}}"
            if (isChecked) {
                $('#billing-sec').hide();
                $('#billing_id').val(shipping_id)
                console.log(shipping_id);
            } else {
                $('#billing-sec').show();
                $('#billing_id').val(billing_id)
                console.log(billing_id);

            }

        });
    </script>

    <script>
        $(document).ready(function() {


            $('#address-form').validate({
                    rules: {
                        phone: {
                            required: true,
                            number: true,
                            maxlength: 20,
                            minlength: 6
                        },
                     
                    },
                 
                })


            $(document).on('submit', '#address-form', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                const countryData = contactInput.getSelectedCountryData();
            const countryCode = countryData.dialCode;
            formData.append('country_code', countryCode);
                $.ajax({
                    url: "{{ route('vendor.order.customerAddress') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                    }
                });
            });


            $('#address').validate({
                    rules: {
                        phone: {
                            required: true,
                            number: true,
                            maxlength: 20,
                            minlength: 6
                        },
                     
                    },
                 
                })


            $(document).on('submit', '#address', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                const countryData = phoneInput.getSelectedCountryData();
            const countryCode = countryData.dialCode;
            formData.append('country_code', countryCode);
                $.ajax({
                    url: "{{ route('vendor.order.customerAddress') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                    }
                });
            });
        });


        $(document).on('change', '.country', function(e) {
            e.preventDefault();
            let id = $(this).val();
            let type = $(this).attr('data-type');

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
                        if (type == "shipping") {
                            $('#state').html(
                                '<option value="" selected disabled>Select State</option>');
                        } else {
                            $('#state_id').html(
                                '<option value="" selected disabled>Select State</option>');
                        }
                        $.each(result.data, function(key, val) {

                            if (type == "shipping") {
                                $('#state').append(`
                    <option value="${val.id}" >${val.state_name}</option>
                    `);
                            } else {
                                $('#state_id').append(`
                    <option value="${val.id}" >${val.state_name}</option>
                    `);
                            }
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
        function toggleMessageRow() {
            const dataRows = $('#product-table tbody tr[data-id!="new"]');
            if (dataRows.length === 0) {
                $('#no-data').show();
                $('#submit-btn').prop('disabled', true);
            } else {
                $('#no-data').hide();
                $('#submit-btn').prop('disabled', false);
            }
        }
    </script>



    <script>
        $(document).on('click', '#getCart', function(e) {
            e.preventDefault();
            let product_id = $('#product_id').val();

            if ($('#offer-price_' + product_id).val()) {
                $.NotificationApp.send("Success", 'Item Already Added', "top-right",
                    "rgba(0,0,0,0.2)", "success");
            } else {
                $.ajax({
                    url: "{{ route('vendor.order.getProducts') }}",
                    method: "POST",
                    data: {
                        id: product_id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // console.log(response);
                        $('#product-table tbody').append(`
                         <tr data-id="${response.data.id}">
                        <td><img src="${response.data.feature_img_url}"
                                                width="50px;"></td>
                        <td>${response.data.name}         <input type="hidden" name="product[]" id="" value="${response.data.id}" class="product-id">  </td>
                        <td>${response.data.inventory.offer_price}
                            <input type="hidden" name="actual_price[]" id="actual-price_${response.data.id}" value="${response.data.inventory.offer_price}" class="actual_price">
                            </td>
                        <td>×</td>
                       <td><input type="number" class="quantity-input" name="quantity[]" value="1" min="1" id="${response.data.id}"></td>
                        <td> <span id="innner-total_${response.data.id}"> ${response.data.inventory.offer_price} </span>  
                                             <input type="hidden" name="offer_price[]" id="offer-price_${response.data.id}" value="${response.data.inventory.offer_price}" class="offer_price">   </td>
                 <td><i class="fa fa-trash text-muted delete-row"></i></td>
                          </tr>
    `);

                        var total = 0;

                        $('.offer_price').each(function() {
                            var offerPrice = parseFloat($(this).val());
                            if (!isNaN(offerPrice)) {
                                total += offerPrice;
                            }
                        });
                        // console.log(total);
                        $('#grand').val(total);
                        $('#table_grand').html(total);
                        $('#total').val(total);
                        $('#table_total').html(total);
                        toggleMessageRow();

                        $('#product_id').val('0');
                        $('#discount-amount').val(0);
                        $('#shipping-amount').val(0);

                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                    }
                });
            }
        })

        toggleMessageRow();

        $('#product-table').on('click', '.delete-row', function() {
            var input_id = $(this).closest('tr').data('id');
            var amount = $('#offer-price_' + input_id).val();
            var grand = $('#grand').val();
            var total = $('#total').val();
            // console.log(grand, amount);

            $('#grand').val(grand - amount);
            $('#total').val(total - amount);
            $('#table_grand').html(grand - amount);
            $('#table_total').html(total - amount);

            $(this).closest('tr').remove();
            toggleMessageRow();
        });


        $(document).on('change', '.quantity-input', function() {
            var value = $(this).val();
            let id = $(this).attr('id');
            var amount = $('#actual-price_' + id).val();
            var cal = amount * value;
            // console.log(cal);

            $('#offer-price_' + id).val(cal)
            $('#innner-total_' + id).text(cal);

            var quantity_total = 0;
            $('.offer_price').each(function() {
                quantity_total += parseFloat($(this).val());
            });

            var discount_amount = $('#discount-amount').val();
            var shipping_amount = $('#shipping-amount').val();

            var total_quantity = quantity_total - parseFloat(discount_amount) + parseFloat(shipping_amount);
            //  console.log(total_quantity);
            $('#grand').val(total_quantity);
            $('#table_grand').html(total_quantity);
            $('#total').val(quantity_total);
            $('#table_total').html(quantity_total);


        })


        $(document).on('change', '#discount-amount', function() {
            var discount = $(this).val()  ? $(this).val() : 0 ;
            var total = $('#total').val();
            var shipping = parseInt($('#shipping-amount').val()) ?   parseInt($('#shipping-amount').val())   : 0;
            var t_amount = total - discount + shipping;
            $('#grand').val(t_amount);
            $('#table_grand').html(t_amount);
        })

        $(document).on('change', '#shipping-amount', function() {


            var discount = parseInt($('#discount-amount').val()) ?  parseInt($('#discount-amount').val()) : 0;
            var total = parseInt($('#total').val());
            var shipping = parseInt($(this).val()) ? parseInt($(this).val())  : 0 ;

            var grand_total = total - discount + shipping;
            //    console.log(grand_total);
            $('#grand').val(grand_total);
            $('#table_grand').html(grand_total);
        })

        $(document).on('submit', '#order-place', function(e) {
            e.preventDefault();
            var customer_id = $('#customer_id').val();
            var billing_id = $('#billing_id').val();
            var shipping_id = $('#shipping_id').val();

            if (billing_id == '') {
                $.NotificationApp.send("Error", 'Please Update Your Billing Address', "top-right",
                    "rgba(0,0,0,0.2)", "error");
            }
            if (shipping_id == '') {
                $.NotificationApp.send("Error", 'Please Update Your Shipping Address', "top-right",
                    "rgba(0,0,0,0.2)", "error");
            }

            if (billing_id && shipping_id) {
                var form = $(this);
                var formData = new FormData(form[0]);
                formData.append('customer_id', customer_id);
                formData.append('billing_address', billing_id);
                formData.append('shipping_address', shipping_id);
                $('#submit-btn').prop('disabled', true);

                $.ajax({
                    url: "{{ route('vendor.order.place') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = "{{ route('vendor.order.index') }}";
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                        $('#submit-btn').prop('disabled', false);
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        $.NotificationApp.send("Error", xhr.responseText, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                        $('#submit-btn').prop('disabled', false);

                    }
                });
            }

        });
    </script>
@endsection
