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
            <li class="breadcrumb-item"><a href="{{ route('vendor.coupon.list') }}">Currency Conversion</a></li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        {{-- <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('vendor.coupon.list') }}" class="btn btn-primary">View All Coupons</a>
        </div> --}}
        <form id="conversion_form">
            <div class="row">
                <div class="mb-3 col-lg-4">
                    <select name="currency_from" id="" required class="form-control">
                        <option value="" selected disabled>Select Currency</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->currency_code }}">{{ $currency->currency_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 col-lg-4 text-center">
                    To
                </div>

                <div class="mb-3 col-lg-4">
                    <select name="currency_to" id="" required class="form-control">
                        <option value="" selected disabled>Select Currency</option>
                        @foreach ($currencies as $currency)
                            <option value="{{ $currency->currency_code }}">{{ $currency->currency_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="simpleinput" class="form-label">Amount </label>
                    <input type="text" required class="form-control" name="amount">
                </div>
                <div class="mb-3 col-lg-6">
                    <label for="example-select" class="form-label">Date</label>
                    <input type="date" class="form-control" name="date">
                </div>
            </div>

            <div class="row">
                <div class="mb-3 col-lg-6">
                    <label for="example-select" class="form-label">Current Amount </label>
                    <input type="text" readonly class="form-control" id="current_amount" name="current_amount">
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
        $(function() {
            $('#conversion_form').on('submit', function(e) {
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");
                $.ajax({
                    url: "{{ route('vendor.currency.post') }}",
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
                            // setTimeout(function() {
                            //     window.location.href = result.location;
                            // }, 1000);
                            $('#current_amount').val(result.data);
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
        $(document).ready(function() {
            $('#coupon_type').change(function() {
                var selectedValue = $(this).val();
                if (selectedValue == "Percentage") {
                    $("#coupon_type_sapce").text(" (in %)")
                } else {
                    $("#coupon_type_sapce").text(" (in USD)")
                }
            });
        });
    </script>
@endsection
