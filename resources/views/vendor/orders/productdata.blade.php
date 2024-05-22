@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')

    <head>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    </head>
@endsection


@section('css')
    <style>
        .container {
            position: relative;
        }

        .pull-right {
            position: absolute;
            top: -30px;
            border: 2px solid #ccc;
            right: 16px;
        }



        .label-outline {
            background-color: transparent;
        }

        .well-lg {
            padding: 24px;
            border-radius: 6px;
        }

        .well {
            min-height: 20px;
            padding: 19px;
            margin-bottom: 20px;
            background-color: #f5f5f5;
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, .05);

        }

        .modal-sm {
            max-width: 580px;
        }
    </style>
@endsection

@section('main_content')
    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Warning</h4>
                        <p class="mt-3">Are you sure u want to cancel this order ? </p>
                        <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>

    <nav aria-label="breadcrumb " style="
margin-top: 1%;
">
        <ol class="breadcrumb mb-0 p-1" style="background-color:ghostwhite">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('vendor.order.index') }}">Orders</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">All Order data</li>
        </ol>
    </nav>

    <div class="card p-2 mt-1 " style="background-color: whitesmoke;">
        <div class="row">

            <div class="col-sm-8 card mr-5">
                <div class="box-header with-border row">

                    <h3 class="box-title mt-3">
                        <i class="fa fa-shopping-cart"></i> Order: #{{ $order->order_number }}
                    </h3>
                    <div class="container">
                        <div class="pull-right">
                            <span class="label label-outline"><b>{{   $order->status == "initiate_refund" ?   "Initiate Refund" : ucwords($order->status) }}</b></span>
                        </div>
                    </div>

                </div>
                <hr>
                <div class="col-sm-12">
                    <div class="well well-lg">
                        <span class="lead">
                            Payment: {{ $order->payment->payment_method }}
                        </span>
                        <div class="container">
                            <div class="pull-right">
                                <span class="label label-outline"><b>{{ $order->payment->status == 'success' ? "Paid" : "Unpaid" }}</b></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div>
                    <div class="col-md-12">
                        <h4>Order details</h4>
                        <span class="spacer10"></span>

                        <table class="table table-sripe">
                            <tbody id="items">
                                @foreach ($order->orderItem as $item)
                                    <tr>
                                        <td>
                                            <img src="{{ asset('public/vendor/featured_image/' . $item->product->featured_image) }}"
                                                width="50px;">
                                        </td>
                                        <td class="nopadding-right" width="55%">
                                            {{ $item->product->name }} </td>
                                        <td class="nopadding-right text-right " width="15%">
                                            {{ $item->price }}

                                        </td>
                                        <td>×</td>
                                        <td class="nopadding text-left" width="10%">
                                            {{ $item->quantity }}

                                        </td>
                                        <td class="nopadding-right text-center" width="10%">
                                            {{ $item->sub_total }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="text-right">Total</td>
                                    <td class="text-right" width="40%">
                                        ${{ $order->sub_total }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-right">
                                        <span>Discount</span>
                                    </td>
                                    <td class="text-right" width="50%"> 
                                        ${{ $order->discount_amount }}
                                    </td>
                                </tr>



                                <tr>
                                    <td class="text-right">
                                        <span>Shipping </span><br>
                                        <em class="small"></em>
                                    </td>
                                    <td class="text-right" width="40%">
                                        ${{ $order->shipping_amount }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="text-right">Coupon </td>
                                    <td class="text-right" width="40%">
                                        ${{ $order->coupon_discount }}
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
                                        $ {{ $order->tax_amount }}
                                    </td>
                                </tr>

                                <tr class="lead">
                                    <td class="text-right">Grand total</td>
                                    <td class="text-right" width="40%">
                                        <span class="small"><b>
                                            $ {{ $order->total_amount }}
                                        </b>
                                           
                                        </span>
                                     
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="text-end">
                        <button class="btn btn-secondary deleteType" data-id="{{ $order->payment->id }}">UPDATE
                            STATUS</button>
                        @if ($order->status != 'Canceled' && $order->status != 'Fulfilled')
                            <button class="btn btn-warning cancleorder" data-id="{{ $order->id }}"
                                status="{{ $order->status }}">CANCEL ORDER</button>
                        @endif

                        @if ($order->status != 'Fulfilled')
                            <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop">
                                FULLFILL ORDER </button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-sm-3 card">
                <div class="box-header with-border mt-1">
                    <h3 class="box-title"><i class="fa fa-user-secret"></i> Customer</h3>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                <hr>
                <div>
                    <div class="box-body">
                        <p>
                            @if($order->customer_detail->profile_pic)
                            <img src="{{asset('public/customer/profile/' . $order->customer_detail->profile_pic)}}"
                            class="img-circle img-sm" alt="Avatar" height="50px" width="60px">
                            @else
                            <img src="https://www.gravatar.com/avatar/fdeef491281a73a51a0fd7dab03d9173?s=30&amp;d=mm"
                            class="img-circle img-sm" alt="Avatar">
                            @endif
                          
                            <span class="admin-user-widget-title indent5">
                                <a data-link="https://www.zcart.incevio.cloud/admin/admin/customer/1"
                                    class="ajax-modal-btn" style="cursor: pointer;">
                                </a>
                                {{ $order->customer_detail->name }}
                            </span>
                        </p>
                        <span class="admin-user-widget-text text-muted">
                            Email: {{ $order->customer_detail->email }}
                        </span>

                        <div class="mt-2">

                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                Send Message </button>
                            <a href="{{ route('vendor.order.customer_invoice', ['id' => $order->id]) }}"
                                class="btn btn-info">Invoice</a>
                        </div>

                        <fieldset class="mt-2">
                            <legend>SHIPPING ADDRESS</legend>
                        </fieldset>
                        <address style="font-size: large">
                            <address>
                                <strong>Address</strong>:      {{ $order->shippingaddress->floor_apartment ?? '' }}
                                <br>{{ $order->shippingaddress->address ?? ''}}<br> 
                                <strong>City</strong>:     {{ $order->shippingaddress->city ?? '' }}<br>
                                <strong>State</strong>:     {{ $order->shippingaddress->state ?? ''}}<br>
                                <strong>Zip code</strong>:      {{ $order->shippingaddress->zip_code ?? ''}}<br> <strong>Country</strong>: {{ $order->shippingaddress->country ?? ''}}<br><abbr
                                    title="Phone">P:</abbr> {{ $order->shippingaddress->contact_no ?? '' }}
                            </address>
                        </address>


                        <fieldset>
                            <legend>BILLING ADDRESS</legend>
                        </fieldset>

                            {{-- <i class="fa fa-check-square-o"></i> --}}
                            <address style="font-size: large">
                                <address>
                               <strong>Address</strong>:     {{ $order->billingAddress->floor_apartment ?? '' }}
                                    <br>{{ $order->billingAddress->address ?? '' }}<br>
                                    <strong>City</strong>:     {{ $order->billingAddress->city ?? '' }}<br>
                                    <strong>State</strong>:     {{ $order->billingAddress->state ?? '' }}<br>
                                    <strong>Zip code</strong>:     {{ $order->billingAddress->zip_code ?? '' }}<br>   <strong>Country</strong>: {{ $order->billingAddress->country ?? '' }}<br><abbr
                                        title="Phone">P:</abbr> {{ $order->billingAddress->contact_no ?? '' }}
                                </address>
                            </address>
                   
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- payment modal --}}
    <div id="warning-alert" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="order-status">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="text">
                        <form id="order-status-form">
                            <p class="mt-3">
                                <input type="hidden" value="{{ $order->id }}" name="id">
                                <label for="">ORDER STATUS <span class="text-danger">*</span></label>
                                <select class="form-control" name="status" required>
                                    <option value="">Select</option>
                                    <option value="Confirmed"
                                        {{ ucwords($order->status) == 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="Dispatched"
                                        {{ ucwords($order->status) == 'Dispatched' ? 'selected' : '' }}>Dispatched </option>
                                    <option value="Delivered"
                                        {{ ucwords($order->status) == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="initiate_refund" {{ ucwords($order->status) == 'Initiate_refund' ? 'selected' : '' }}>
                                        Initiate Refund</option>
                                </select>
                            </p>
                            <div class="row">
                                <div class="col-lg-12">
                                    {{-- <input type="checkbox" name="checkbox"> --}}

                                    {{-- SEND A NOTIFICATION EMAIL TO CUSTOMER --}}
                                </div>
                            </div>
                            {{-- <label class="mt-2"><span class="text-danger">*</span> Required fields.</label> --}}
                            <div class="text-right">

                                <button type="button" class="btn btn-warning my-2  changeStatus"
                                    data-bs-dismiss="modal">Confirm</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


    {{-- message modal  --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Form</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reply_formes">
                        <p>To:{{ $order->customer_detail->email }}
                        <p>
                            <hr>
                            <input type="hidden" value="{{ $order->customer_detail->email }}" name="email">

                            <label>Subject<samp class="text-danger">*</samp></label>
                            <input type="text" class="form-control" name="subject" id="subject" required>
                        <div id="subjectError" class="text-danger"></div>
                        <br>
                        <label>Message<samp class="text-danger">*</samp></label>
                        <textarea id="content" name="message" class="form-control summernote" ></textarea>
                        <div id="messageError" class="text-danger"></div>
                        {{-- <label class="mt-2">File<span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file" id="file">
                        <div id="fileError" class="text-danger"></div> --}}
                        {{-- <label class="mt-2"><span class="text-danger">*</span>Required fields.</label> --}}
                        <div class="mt-2" style="text-align: right;">
                            <input type="button" class="btn btn-success mx-auto mydatadraft mytype" id="submitBtn"
                                value="Submit">
                        </div>
                    </form>
                </div>


            </div>
        </div>
    </div>


    {{-- full fill --}}
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Full Fill Order</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="full_fill">

                    <input type="hidden" value="{{ $order->customer_detail->id }}" name="id">
                    <input type="hidden" value="{{ $order->id }}" name="order_id">
                    <div class="modal-body">
                        <label>Tracking Id<samp class="text-danger"></samp></label>
                        <input type="text" class="form-control" name="tracking" id="tracking">
                        <label>Shipping Carrier<samp class="text-danger">*</samp></label>
                        <select name="carrier" class="form-control" id="customerDropdown" required>
                            <option value="" selected disabled>Select Carrier</option>
                            @foreach ($Carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                            @endforeach
                        </select>
                        <div class="row mt-2">
                            <div class="col-lg-12">
                                <input type="checkbox" name="email_send">

                                SEND A NOTIFICATION EMAIL TO CUSTOMER
                            </div>
                        </div>
                        {{-- <label class="mt-2"><span class="text-danger">*</span>Required fields.</label> --}}

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-secondary">Update</button>
                        </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const subjectInput = document.getElementById("subject");
            const contentInput = document.getElementById("content");
            const fileInput = document.getElementById("file");
            const subjectError = document.getElementById("subjectError");
            const messageError = document.getElementById("messageError");
            const fileError = document.getElementById("fileError");
            const submitBtn = document.getElementById("submitBtn");

            submitBtn.addEventListener("click", function(event) {
                subjectError.innerHTML = "";
                messageError.innerHTML = "";
                fileError.innerHTML = "";

                if (subjectInput.value.trim() === "") {
                    subjectError.innerHTML = "Subject is required";
                    event.preventDefault();
                }

                if (contentInput.value.trim() === "") {
                    messageError.innerHTML = "Message is required";
                    event.preventDefault();
                }

            //    if (fileInput.value.trim() === "") {
            //        fileError.innerHTML = "File is required";
            //        event.preventDefault();
            //    }   
            });
        });
    </script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $('.summernote').summernote({
            height: 100
        });

        $(document).on('click', '#order-status', function() {
            $('#warning-alert').modal('hide');
        })
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('submit', '#full_fill', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);

                $.ajax({
                    url: "{{ route('vendor.order.full_fill') }}",
                    method: "POST",
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            if (response && response.status) {
                                if ($.NotificationApp && $.NotificationApp.send) {
                                    $.NotificationApp.send(
                                        "Success",
                                        response.msg,
                                        "top-right",
                                        "rgba(0,0,0,0.2)",
                                        "success"
                                    );
                                } else {
                                    console.error("$.NotificationApp.send is not defined.");
                                }
                                window.location.reload();
                            } else {
                                console.error(
                                    "Invalid response data or response status is not true.");
                            }
                        } catch (error) {
                            console.error("An error occurred:", error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX request error:", error);
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '#reply_formes .mydatadraft', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var form = $(this).closest('form');
                var formData = new FormData(form[0]);

                $.ajax({
                    url: "{{ route('vendor.order.send_message') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        // window.location.load();
                        if (response.status) {
                            $.NotificationApp.send("Success", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            setTimeout(function() {
                                // window.location.href = response.location;
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        var message = response.msg;
                        console.log(message);
                        $.NotificationApp.send("Error", message, "top-right",
                            "rgba(0,0,0,0.2)", "error");

                            setTimeout(function() {
                                // window.location.href = response.location;
                                window.location.reload();
                            }, 1000);
                            
                    }
                });
            });
        });
    </script>





    <!-- Make sure you have included necessary libraries -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        $(document).ready(function() {
            // Set up AJAX headers
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Attach click event to the 'cancleorder' element
            $('.cancleorder').on('click', function() {
                var orderId = $(this).data('id');

                $('#warning-alert-modal').modal('show');
                $('#warning-alert-modal').on('click', '.confirm', function() {
                    $.ajax({
                        url: "{{ route('vendor.order.order_status') }}",
                        type: 'POST',
                        data: {
                            id: orderId
                        },
                        dataType: "json",
                        success: function(response) {
                            try {
                                if (response && response.status) {
                                    // Check if NotificationApp is defined and send notification
                                    if ($.NotificationApp && $.NotificationApp.send) {
                                        $.NotificationApp.send(
                                            "Success",
                                            response.msg,
                                            "top-right",
                                            "rgba(0,0,0,0.2)",
                                            "success"
                                        );
                                    } else {
                                        console.error(
                                            "$.NotificationApp.send is not defined."
                                            );
                                    }
                                    // Reload the page after success
                                    window.location.reload();
                                } else {
                                    console.error(
                                        "Invalid response data or response status is not true."
                                        );
                                }
                            } catch (error) {
                                console.error("An error occurred:", error);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("AJAX request error:", error);
                        }
                    });
                });

            });
        });
    </script>






    <script>
        $('body').on("click", ".deleteType", function(e) {
            e.preventDefault(); // Prevent the default click behavior

            var id = $(this).data('id');
            var name = $(this).data('name');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');

            $("#warning-alert").text(name);
            $('#warning-alert').modal('show');

            $('#warning-alert').on('click', '.changeStatus', function() {
                var formData = new FormData($('#order-status-form')[0]);
                formData.append('_token', '{{ csrf_token() }}');


                $.ajax({
                    url: "{{ route('vendor.order.payment_status') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            if (response && response.status) {
                                if ($.NotificationApp && $.NotificationApp.send) {
                                    $.NotificationApp.send(
                                        "Success",
                                        response.msg,
                                        "top-right",
                                        "rgba(0,0,0,0.2)",
                                        "success"
                                    );
                                } else {
                                    console.error("$.NotificationApp.send is not defined.");
                                }
                                // Reload the page after success
                                window.location.reload();
                            } else {
                                console.error(
                                    "Invalid response data or response status is not true.");
                            }
                        } catch (error) {
                            console.error("An error occurred:", error);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX request error:", error);
                    }

                });
            });
        });
    </script>
@endsection
