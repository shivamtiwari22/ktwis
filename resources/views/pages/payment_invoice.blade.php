<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Invoive</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        .field-icon {
            float: right;
            margin-left: -25px;
            margin-top: -25px;
            position: relative;
            z-index: 2;
            right: 8px;
        }

        .center {
            text-align: center
        }

        body {
            background-color: #faf8f5 !important;
        }

        .css-ttr2i5-text_heading_sm {
            color: rgb(0, 20, 53);
            font-family: PayPalOpen-Regular, "Helvetica Neue", Arial, sans-serif;
            font-size: 1.50rem;
            line-height: 2.25rem;
            font-weight: 400;
        }

        .status-container {
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .payment-status {
            border: 0px solid;
            border-radius: 7px;
            background-color: rgb(255, 190, 74);
            padding: 3px;
        }

        .payment-status-success {
            border: 0px solid;
            border-radius: 7px;
            background-color: green;
            padding: 3px;
            color: white;
        }

        .css-1gyuotj-grid_container {
            background-color: #f1ede8;
            padding-top: 1px;
            padding-bottom: 1px;

        }

        .font-size {
            font-size: 14px;
        }

        .anchor-deco {
            text-decoration: none;
            color: black;
        }

        a:hover {
            text-decoration: none;
            color: black;
        }

        .login-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 10px 20px;
            font-weight: bold;
            cursor: pointer;

            position: relative;
            /* left: 40%; */
            margin-top: 20px;
            display: flex;
            margin: auto;
            /* width: 20%; */
        }

        .login-button:hover {
            background-color: #0056b3;
        }

        .margin-desktop {
            margin: 10px 0;
        }

        @media (min-width: 574px) {
            .desktop-text-end {
                text-align: end;
            }

            .margin-desktop {
                margin: 0 0;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="row mt-3">

            <div class="col-md-12">
                <div class=""
                    style="display: flex; flex-wrap: nowrap; justify-content: space-between; align-items: center; margin: 1rem 0px 1.5rem;">
                    {{-- <a href="{{route('vendor.disputes.index')}}" class="btn btn-primary">View All Disputes</a> --}}
                    <div class="css-ttr2i5-text_heading_sm">Invoice From Ktwis</div>
                    <div>
                        <input type="hidden" id="order-id" value="{{ $order->id }}">
                        <a href="{{ route('download-invoice', $order->id) }}" class="anchor-deco"> <i
                                class="fa fa-download"></i>
                            Download PDF</a>

                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="card">
                    <form id="order-place">
                        <div class="card-body card-padding">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group mb-0">
                                        <label for=""><Strong> {{ $user->name }}</Strong></label> <br>
                                        <label for="">
                                            @if ($address->floor_apartment)
                                                {{ $address->floor_apartment }} ,
                                            @endif {{ $address->address }}
                                        </label> <br>
                                        <label for="">{{ $address->city }}</label> <br>
                                        <label for="">{{ $address->obj_state }}</label> <br>
                                        <label for="">{{ $address->zip_code }}</label> <br>
                                        <label for="">{{ $address->obj_country }}</label> <br>

                                    </div>
                                </div>
                                <div class="col-sm-6 desktop-text-end">
                                    <div>
                                        <small>Invoice no {{ sprintf('%05d', $order->invoice_number) }}</small> <br>
                                        <small>Issued : {{ date('d M Y', strtotime($order->created_at)) }}</small> <br>
                                        <small>Due : {{ date('d M Y', strtotime($order->created_at)) }}</small> <br>
                                    </div>


                                </div>
                            </div>
                            <div class="desktop-text-end margin-desktop">
                                <div>
                                    <h1>$ {{ $order->total_amount }}</h1>
                                    @if ($payment->status != 'success')
                                        <span class="payment-status"> Due</span>
                                    @else
                                        <span class="payment-status-success"> Paid</span>
                                    @endif
                                </div>
                            </div>


                            <div class="css-1gyuotj-grid_container mt-2"
                                style="
                            display: flex; column-gap: 20px;   flex-wrap: wrap;     padding: 5px 10px;
                            ">
                                <span class=""><i class='fas fa-phone-alt'></i>
                                    {{ $address->contact_no }}</span>
                                <span style="word-break: break-all;"> <i class="fa fa-envelope "></i> &nbsp
                                    {{ $user->email }}</span>
                            </div>


                            <div class="mt-4">
                                <h6>Items</h6>
                                <div class="card">

                                    <div class="" style="padding: 5px 5px;overflow: auto;">
                                        <table style="
    width: 100%;
    text-align: center;
">
                                            <thead>
                                                <tr class="lead">
                                                    <td>
                                                        <h6>Product</h6>
                                                    </td>
                                                    <td>
                                                        <h6>Quantity</h6>
                                                    </td>
                                                    <td>
                                                        <h6>Price</h6>
                                                    </td>
                                                    <td>
                                                        <h6>Amount</h6>
                                                    </td>
                                                </tr>
                                            </thead>


                                            @foreach ($order_items as $item)
                                                <tr class="font-size">
                                                    <td> {{ $item->name }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ $item->price }}</td>
                                                    <td> ${{ $item->total_amount }}</td>
                                                </tr>
                                            @endforeach
                                            <!-- Add more rows for additional products if needed -->
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6"></div>
                                <div class="col-md-6">
                                    <table class="table">
                                        <tbody class="font-size">
                                            <tr>
                                                <td class="text-right">Sub Total</td>
                                                <td class="text-right" width="40%">
                                                    $ {{ $order->sub_total }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td class="text-right">
                                                    <span>Discount</span>
                                                </td>
                                                <td class="text-right" width="50%"
                                                    style="
                                            padding-left: 2px;">
                                                    −
                                                    $ {{ $order->discount_amount }}

                                                </td>

                                            </tr>

                                            <tr>
                                                <td class="text-right">
                                                    <span>Shipping</span><br>
                                                    <em class="small"></em>
                                                </td>
                                                <td class="text-right" width="40%">
                                                    $ {{ $order->shipping_amount }}
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
                                                <td class="text-right">
                                                    <h6>Total</h6>
                                                </td>
                                                <td class="text-right" width="40%">
                                                    <h6> $ {{ $order->total_amount }}
                                                        <input type="hidden" id="amount"
                                                            value="{{ $order->total_amount }}">
                                                    </h6>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>

                    </form>

                </div>
            </div>
        </div>


        <div class="col-md-3" style="margin: 3px 0px 4px 0px">
            @if ($payment->status != 'success')
                <div class="card">
                    <div class="card-header" style="background-color: white">
                        <strong> Amount Due: <span class="ml-3">$ {{ $order->total_amount }}</span> </strong>
                    </div>
                    <div class="card-body card-padding  center">
                        <label for=""> <strong>Select payment method:</strong> </label> <br>
                        <div style="display: grid; gap: 10px">
                            <a
                                href="{{ route('invoice-payment.verify', ['pay' => $payment->id, 'user' => $user->id]) }}">
                                <button class="btn btn-warning w-100" type="submit">
                                    Flutter&nbsp;Wave</button></a>
                            <button class="btn btn-secondary"data-toggle="modal" data-target="#modalLoginForm">Riva
                                Pass</button>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body center">
                        <h6>Last updated on <span>{{ date('d M Y', strtotime($payment->updated_at)) }} at
                                {{ date('H:i:s', strtotime($payment->updated_at)) }}</span> </h6>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header" style="background-color: white">
                        <strong> Amount Due: <span class="ml-5">$ 0.00</span> </strong>
                    </div>
                    <div class="card-body ">

                        <div class="row ">
                            <div class="col-md-7">
                                <h6>Original invoice total </h6>

                            </div>
                            <div class="col-md-5">
                                <h6>$ {{ $order->total_amount }}</h6>
                            </div>
                        </div>
                        <hr>


                        <div class="row">
                            <div class="col-md-7">
                                <h6>Total amount paid </h6>

                            </div>
                            <div class="col-md-5">
                                <h6>${{ $order->total_amount }}</h6>
                            </div>
                        </div>



                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header" style="background-color: white">
                        <strong> Payment activity </strong>
                    </div>
                    <div class="card-body font-size">

                        <div class="row">
                            <div class="col-md-7">
                                Payment –  {{date('d/m/Y', strtotime($payment->updated_at))}}

                            </div>
                            <div class="col-md-5">
                                ${{ $order->total_amount }}
                            </div>
                        </div>
                        <hr>

                        <div class="row ">
                            <div class="col-md-7">
                                Transaction ID

                            </div>
                            <div class="col-md-5">
                                {{ $payment->transaction_id }}
                            </div>
                        </div>


                    </div>
                </div>
            @endif


        </div>
    </div>

    {{-- Credit Pass Model  --}}
    <div class="modal fade" id="modalLoginForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        @include('component.myloader')
        <div class="modal-dialog" role="document" id="model-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Login To Riva Pass</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body" id="model-contant">

                    <form id="loginForm">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Email</label>
                            <input type="email" class="form-control" id="username" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password-field" name="password"
                                required>
                            <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>

                        <button type="submit" class="btn btn-primary login-button" id="login">Submit</button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.2.1/dist/js/bootstrap.min.js"
    integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous">
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

{{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>     --}}

<script>
    $(".toggle-password").click(function() {
        $(this).toggleClass("fa-eye fa-eye-slash");
        var input = $($(this).attr("toggle"));
        if (input.attr("type") == "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });

    $(document).on('submit', '#loginForm', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]);

        $("#overlay").fadeIn();
        $.ajax({
            url: "{{ route('creditPassLogin') }}",
            type: 'POST',
            data: formData,
            dataType: "JSON",
            contentType: false,
            processData: false,
            success: function(result) {
                $("#overlay").fadeOut();
                console.log(result);
                if (result.status) {
                    toastr.success(result.message);

                    $('#model-contant').html(
                        ` 
                        <form class="form-horizontal" id="verifyCustomer" method="POST">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <div class="ml-3">
                            <p>Please enter the <strong>OTP</strong> generated on your Authenticator App. <br> Ensure
                                you submit the current one because it refreshes every 30 seconds.</p> </div>
                            <label for="one_time_password" class="col-md-6 control-label">One Time Password</label>

                            <div class="col-md-12">
                                <input id="one_time_password" type="number" class="form-control"
                                    name="one_time_password" required autofocus>
                                    <input id="token" type="hidden" class="form-control"
                                    name="token" value="${result.context.data.token}">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Login
                                </button>
                            </div>
                        </div>
                    </form>
                    `
                    )

                } else {
                    toastr.error(result.message);
                    $("#overlay").fadeOut();

                }
            },
            error: function(result) {
                toastr.error(result.result);

            }
        })

    })


    $(document).on('submit', '#verifyCustomer', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]);
        $("#overlay").fadeIn();
        $.ajax({
            url: "{{ route('creditVerifyCustomer') }}",
            type: 'POST',
            data: formData,
            dataType: "JSON",
            contentType: false,
            processData: false,
            success: function(result) {
                // console.log(result);
                if (result.status) {
                    toastr.success(result.message);
                    walletBallance()
                } else {
                    toastr.error(result.message);
                }
                $("#overlay").fadeOut();

            },
            error: function(result) {
                toastr.error(result.result);
                $("#overlay").fadeOut();

            }
        })

    })

    function walletBallance() {
        var token = $('#token').val();
        $.ajax({
            url: "{{ route('customerWallet') }}",
            type: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                "token": token
            },
            success: function(result) {
                var amount = $('#amount').val();
                var order_id = $('#order-id').val();
                $("#overlay").fadeOut();

                console.log(result);
                if (result.status) {
                    $('#model-dialog').html(
                        `
                    <div class="modal-dialog" role="document" id="model-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Balance : <strong>$ ${result.data.context.data.total_usd_amount}</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
              
                <div class="modal-body" id="model-contant">
                        <div class="text-center"  style="font-size: 20px">
                            <i class="fa fa-wallet"></i>
                            Riva Pass
                        </div>
                        <div class="text-center mt-2">
                            <form class="form-horizontal" id="payment" method="POST">
                        {{ csrf_field() }}
                            <input id="bearer" type="hidden" class="form-control"
                                    name="token" value="${result.token}">
                                    <input id="total-amount" type="hidden" class="form-control"
                                    name="amount" value="${amount}">
                                    <input id="" type="hidden" class="form-control"
                                    name="order_id" value="${order_id}">
                            <button class="btn btn-primary  mt-2" style="width:51%" type="submit" >Pay Now $ ${amount}</button>
                            </form>
                        </div>
                </div>
              
            </div>
        </div>  `)
                } else {

                    toastr.error(result.message);
                }
            },
            error: function(result) {
                toastr.error(result.result);
                $("#overlay").fadeOut();
            }
        });
    }
</script>



<script>
    $(document).on('submit', '#payment', function(e) {
        e.preventDefault();
        var form = $(this);
        var formData = new FormData(form[0]);
        $("#overlay").fadeIn();
        $.ajax({
            url: "{{ route('customerPayment') }}",
            type: 'POST',
            data: formData,
            dataType: "JSON",
            contentType: false,
            processData: false,
            success: function(result) {
                console.log(result);
                if (result.status) {
                    toastr.success(result.message);
                    window.location.href = result.location;

                } else {
                    toastr.error(result.message);
                }
                $("#overlay").fadeOut();

            },
            error: function(result) {
                toastr.error(result.result);
                $("#overlay").fadeOut();

            }
        })

    })
</script>

</html>
