@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
    <!-- Warning Alert Modal -->
    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Warning</h4>
                        <p class="mt-3">Are you sure you want to delete<br> <b>[<span
                                    id="warning-alert-modal-text"></span>]</b>.</p>
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item " aria-current="page"> <a href="{{ route('vendor.wallet.index') }}">Wallet </a></li>
            <li class="breadcrumb-item active" aria-current="page">Deposite Fund</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            {{-- <a href="{{route('vendor.carrier.list')}}" class="btn btn-primary">View All Carriers</a> --}}
        </div>
        <form action="" id="payment">

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="input-group">
                                <select name="currency" id="currency">
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->currency_code }}"
                                            {{ $currency->symbol == '$' ? 'selected' : '' }}>
                                            {{ $currency->symbol }}</option>
                                    @endforeach
                                </select>
                                <input type="number" class="form-control" id="amount" placeholder="Amount" required
                                    name="amount">
                                <input type="text" name="name" id="name" class="form-control"
                                    value="{{ Auth::user()->name }}" hidden>
                                <input type="email" name="email" id="email" class="form-control"
                                    value="{{ Auth::user()->email }}" hidden>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5><input type="radio" required name="payment_gateway" class="payment_gateway"
                                    value="paypal"> <label for="" class="ms-2">
                                    <strong>PAYPAL</strong>
                                </label></h5>
                            <h5><input type="radio" required name="payment_gateway" class="payment_gateway"
                                    value="flutterwave"><label for="" class="ms-2">
                                    <strong>FLUTTER WAVE</strong> </label></h5>

                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="text-info small space30">
                                <i class="uil-comment-info">
                                    <span>Select payment option</span>
                                </i>
                            </p>
                            <button id="pay-now-btn" class="btn btn-primary btn-lg btn-block" type="submit">
                                <small><i class="uil-shield-exclamation"></i>
                                    <span id="pay-now-btn-txt">Pay now</span>
                                </small>
                            </button>
                        </div>

                    </div>
                </div>

            </div>
        </form>

    </div>
@endsection


@section('script')
    <script src="https://checkout.flutterwave.com/v3.js"></script>
    <script>
        $(document).on('submit', '#payment', function(e) {
            e.preventDefault();
            var amount = $('#amount').val();
            var type = $('input[name="payment_gateway"]:checked').val();
            var email = $('#email').val();
            var name = $('#name').val();
            var currency = $('#currency').val();
            // console.log(amount, type, name, email, currency);
            if (type == "flutterwave") {
                makePayment(amount, name, email, currency);
            }
            else{
                alert("PAYPAL is not implemented");
            }
        });

        function makePayment(amount, name, email, currency) {
            FlutterwaveCheckout({
                public_key: "FLWPUBK_TEST-ac71156fd1e94e4760e772ee8f00bfaa-X",
                tx_ref: "titanic-48981487343MDI0NzMx",
                amount: amount,
                currency: "NGN",
                payment_options: "card, mobilemoneyghana, ussd",
                customer: {
                    email: email,
                    phone_number: "08102909304",
                    name: name,
                },

                callback: function(data) {
                    console.log(data)
                    $.ajax({
                        type: "POST",
                        url: "{{ route('vendor.payment.verify') }}",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            data: data,
                        },
                        success: function(data) {
                            console.log(data);
                            if (data.success == false) {
                                toastr.error(data.message);
                                console.log(data.message);
                            } else {
                                window.location.href = "{{ route('vendor.wallet.index') }}";
                            }
                        },
                        error: function(data) {
                            console.log(data);
                        }
                    })
                },
                customizations: {
                    title: "My Store",
                    description: "Payment for an awesome cruise",
                    logo: "https://disruptingafrica.com/images/9/9e/Flutterwave_Logo.png",
                },
            });
        }
    </script>
@endsection
