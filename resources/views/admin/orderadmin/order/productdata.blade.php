@extends('admin.layout.app')

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

        .note-insert {
            display: none;
        }

        .note-view {
            display: none;
        }
    </style>
@endsection

@section('main_content')
<!-- Warning Alert Modal -->
<div id="warning-alert-modal-es" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    {{-- <i class="dripicons-warning h1 text-warning"></i> --}}
                    <h4 class="mt-2">Confirmation!</h4>
                    <p class="mt-3">Are you sure you want to do this?</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


    <nav aria-label="breadcrumb " style=" 
       margin-top: 1%;
              ">
        <ol class="breadcrumb mb-0 p-1">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page"> <a href="{{ route('admin.order') }}">Orders</a>
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
                            <span class="label label-outline"><b>{{  $order->status == "initiate_refund" ?   "Initiate Refund" : ucwords($order->status) }}</b></span>
                        </div>
                    </div>

                </div>
                <hr>
                <div class="col-sm-12">
                    <div class="well well-lg">
                        <span class="lead">
                            Payment: {{$order->payment->payment_method}}
                        </span>
                        <div class="container">
                            <div class="pull-right">
                                <span class="label label-outline"><b>{{$order->payment->status == 'success' ? "Paid" : "Unpaid" }}</b></span>
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
                                @foreach($order->orderItem as $item)
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
                                    <td class="text-right" width="40%"> 
                                        ${{$order->discount_amount}}
                                    </td>
                                </tr>



                                <tr>
                                    <td class="text-right">
                                        <span>Packaging</span><br>
                                        <em class="small"></em>
                                    </td>
                                    <td class="text-right" width="40%">
                                        ${{$order->shipping_amount}}
                                    </td>
                                </tr>

                                {{-- <tr>
                                    <td class="text-right">Handling</td>
                                    <td class="text-right" width="40%">
                                        $2.00
                                    </td>
                                </tr> --}}

                                <tr>
                                    <td class="text-right">Taxes <br>
                                        <em class="small">
                                            Worldwide
                                            0%
                                        </em>
                                    </td>
                                    <td class="text-right" width="40%">
                                        $ {{$order->tax_amount}}
                                    </td>
                                </tr>

                                <tr class="lead">
                                    <td class="text-right">Grand total</td>
                                    <td class="text-right" width="40%">
                                        $ {{$order->total_amount}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

                <div class="card">
                    <div class="row">
                        <div class="col-12 col-md-6 col-lg-3 mb-2 ms-3">
                            {{-- <button class="btn btn-danger btn-block mypayment"  data-bs-toggle="modal" data-id="{{ $order->payment->id }}">Mark as {{ $order->payment->status }}</button> --}}
                          
                        </div>
                        {{-- <div class="col-12 col-md-6 col-lg-3 mb-2">
                            <button class="btn btn-secondary btn-block" data-id="{{ $order->payment->id }}">Initiate a
                                Refund</button>
                        </div> --}}
                        <div class="col-12 col-md-6 col-lg-3 mb-2">
                            {{-- <button class="btn btn-secondary btn-block deleteType"
                                data-id="{{ $order->payment->id }}">Update Status</button> --}}
                        </div>
                        {{-- <div class="col-12 col-md-6 col-lg-3 mb-2">
                            <button class="btn btn-warning btn-block cancleorder" data-id="{{ $order->id }}"
                                status="{{ $order->status }}">Cancel Order</button>
                        </div> --}}
                        <div class="col-12 col-md-6 col-lg-3 mb-2">
                            {{-- <button type="button" class="btn btn-info btn-block" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop">Fulfill Order</button> --}}
                        </div>
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
                            <img src="https://www.gravatar.com/avatar/fdeef491281a73a51a0fd7dab03d9173?s=30&amp;d=mm"
                                class="img-circle img-sm" alt="Avatar">

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
                            <a href="{{ route('admin.order.customer_invoice', ['id' => $order->id]) }}"
                                class="btn btn-info">Invoice</a>
                        </div>




                        <fieldset class="mt-2">
                            <legend>SHIPPING ADDRESS</legend>
                        </fieldset>
                        <address style="font-size: large">
                            <address>
                                <strong>Address</strong>:   {{ $order->shippingaddress->floor_apartment ?? '' }}
                                <br>{{ $order->shippingaddress->address ?? ''}}<br>
                                <strong>City</strong>:     {{ $order->shippingaddress->city ?? '' }}<br>
                                <strong>State</strong>:       {{ $order->shippingaddress->state ?? ''}}<br>
                                <strong>Zip code</strong>:   {{ $order->shippingaddress->zip_code ?? '' }}<br>
                                <strong>Country</strong>:    {{ $order->shippingaddress->country ?? ''}}<br><abbr
                                    title="Phone">P:</abbr> {{ $order->shippingaddress->contact_no ?? '' }}
                            </address>
                        </address>


                        <fieldset>
                            <legend>BILLING ADDRESS</legend>
                        </fieldset>

                        <small>
                        
                            <address style="font-size: large">
                                <address>
                                    <strong>Address</strong>:      {{ $order->billingAddress->floor_apartment ?? '' }}
                                    <br>{{ $order->billingAddress->address ?? '' }}<br>
                                    <strong>City</strong>:    {{ $order->billingAddress->city ?? '' }}<br>
                                    <strong>State</strong>:     {{ $order->billingAddress->state ?? '' }}<br>
                                    <strong>Zip code</strong>:      {{ $order->billingAddress->zip_code ?? '' }}<br>
                                    <strong>Country</strong>:      {{ $order->billingAddress->country ?? '' }}<br><abbr
                                        title="Phone">P:</abbr> {{ $order->billingAddress->contact_no ?? '' }}
                                </address>
                            </address>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- payment modal --}}
    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text">
                        <h4 class="mt-2">Update</h4>
                        <hr>
                        <form id="order-status-form">
                            <p class="mt-3">
                                <input type="hidden" value="{{ $order->payment->id }}" name="id">
                                <label for="">ORDER STATUS <span class="text-danger">*</span></label>
                                    <select name="selected_status" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Confirmed" {{ucwords($order->status) == "Confirmed" ? 'selected': ''}}>Confirmed</option>
                                        <option value="Dispatched" {{ucwords($order->status) == "Dispatched" ? 'selected': ''}}>Dispatched </option>
                                        <option value="Delivered" {{ucwords($order->status) == "Delivered" ? 'selected': ''}}>Delivered</option>
                                        <option value="Returned" {{ucwords($order->status) == "Returned" ? 'selected': ''}}>Returned</option>
                                    </select>
                                    
                            </p>
                            <div class="row">
                                <div class="col-lg-12">
                                    {{-- <input type="checkbox" name="checkbox"> --}}

                                    {{-- SEND A NOTIFICATION EMAIL TO CUSTOMER --}}
                                </div>
                            </div>
                            <label class="mt-2"><span class="text-danger">*</span> Required fields.</label>
                            <div class="text-right">

                                <button type="button" class="btn btn-warning my-2"
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
                            <input type="text" class="form-control" name="subject" id="subject">
                        <div id="subjectError" class="text-danger"></div>
                        <label>Message<samp class="text-danger"> </samp></label>
                        <textarea id="content" name="message" class="form-control summernote"></textarea>
                        <div id="messageError" class="text-danger"></div>
                        {{-- <label class="mt-2">file<span class="text-danger">*</span> (JPG,PNG,JPEG,2MB) </label>
                        <input type="file" class="form-control" name="file" id="file">
                        <div id="fileError" class="text-danger"></div> --}}
    
                        <div class="mt-2" style="text-align: right;">
                            <button type="submit" class="btn btn-success mx-auto mydatadraft mytype" id="submitBtn"
                             >Submit </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- full fill --}}
    <div class="modal fade" id="message_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Full Fill Order</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="full_fill">

                    <input type="hidden" value="{{ $order->customer_detail->id }}" name="id">
                    <div class="modal-body">
                        <label>Tracking Id<samp class="text-danger"></samp></label>
                        <input type="text" class="form-control" name="tracking" id="tracking">
                        <label>Shipping Carrier<samp class="text-danger">*</samp></label>
                        <select name="carrier" class="form-control" id="customerDropdown">
                            <option value="" selected disabled>Select Carrier</option>
                            @foreach ($Carriers as $carrier)
                                <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                            @endforeach
                        </select>
                        <div class="row mt-2">
                            <div class="col-lg-12">
                                <input type="checkbox" name="checkbox">

                                SEND A NOTIFICATION EMAIL TO CUSTOMER
                            </div>
                        </div>
                        <label class="mt-2"><span class="text-danger">*</span>Required fields.</label>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary full_fill_data"
                                data-bs-dismiss="modal">Update</button>
                        </div>
                </form>
            </div>
        </div>
    </div>


   
@endsection

@section('script')
<script>
        $('body').on("click", ".mypayment", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');

            
            $("#warning-alert-modal-text").text(name);
            $('#warning-alert-modal-es').modal('show');
            $('#warning-alert-modal-es').on('click', '.btn', function() {


                $.ajax({
                        url: "{{route('admin.carts.payment_status')}}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                            location.reload();                            setTimeout(function() {
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            });
        });
</script>




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

                // if (subjectInput.value.trim() === "") {
                //     subjectError.innerHTML = "Subject is required";
                //     event.preventDefault();
                // }

                // if (contentInput.value.trim() === "") {
                //     messageError.innerHTML = "Message is required";
                //     event.preventDefault();
                // }

                // if (fileInput.value.trim() === "") {
                //     fileError.innerHTML = "File is required";
                //     event.preventDefault();
                // }
            });
        });
    </script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $('.summernote').summernote({
            height: 100
        });
    </script>

    <script>
        $(document).ready(function() {
            $(document).on('click', '#full_fill .full_fill_data', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var form = $(this).closest('form');
                var formData = new FormData(form[0]);

                $.ajax({
                    url: "{{ route('vendor.order.full_fill') }}",
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


<script>

$('#reply_formes').validate({
                rules: {
                    subject: {
                        required: true,
                        maxlength:26,
                    },
                    message: {
                        required: true
                    },
                    file : {
                        filesize: 1024,
                        imageFormat: true

                    }
                },
                messages: {
                    file : {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                 

                },
            });


            $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");
</script>
    <script>
      
            $(document).on('submit', '#reply_formes', function(e) {
                e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                console.log('helo');

                $.ajax({
                    url: "{{ route('admin.order.send_message') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.status) {

                            $.NotificationApp.send("Success", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            // setTimeout(function() {
                            //     window.location.href = response.location;
                            // }, 1000);

                            setTimeout(function(){
                                window.location.reload();
                            } , 1000) 

                           
                            $('#message_modal').hide();
                        } else {
                            $.NotificationApp.send("Error", response.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        var message = response.message;
                        console.log(message);
                        $.NotificationApp.send("Error", message, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                    }
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






    <script>
        $('body').on("click", ".deleteType", function(e) {
            e.preventDefault(); // Prevent the default click behavior

            var id = $(this).data('id');
            var name = $(this).data('name');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');

            $("#warning-alert-modal-text").text(name);
            $('#warning-alert-modal').modal('show');

            $('#warning-alert-modal').on('click', '.btn', function() {
                var formData = new FormData($('#order-status-form')[0]);
                formData.append('_token', '{{ csrf_token() }}');


                $.ajax({
                    url: "{{ route('admin.carts.update_status') }}",
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
