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
            <li class="breadcrumb-item active" aria-current="page">Wallet</li>
        </ol>


    </nav>

    <div class="mt-2">
        <h3>Wallet Amount</h3>
        <strong style="font-size: 50px ; " class="ms-2">
            $ {{ $totalAmount }}
        </strong>
    </div>
    <div class="card p-4 mt-4">
        <div class="row">
            <div class="col-6">
                <a href="{{ route('vendor.wallet.transaction') }}"> <u>Transactions</u></a>
            </div>
            <div class="col-6">
                <div class="d-flex justify-content-end mb-2">
                    <a href="javascript: void(0);" data-target="#exampleModal"data-toggle="modal"
                        class="btn btn-secondary ms-2">Payout Request</a>
                    <a href="{{ route('vendor.wallet.fund') }}" class="btn btn-primary  ms-2">$ Deposit Fund</a>
                    <a href="javascript: void(0);" data-target="#exampleModalCenter"data-toggle="modal"
                        class="btn btn-warning ms-2">Transfer</a>

                </div>
            </div>
           
        </div>

        <div class="d-flex justify-content-end mb-2">
        </div>
        <table id="table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Currency</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($balance as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        {{-- <td>{{ date('d/m/Y', strtotime($item->created_at)) }}</td> --}}
                        <td>{{ $item->currency->symbol }}</td>
                        <td>{{ $item->balance_amount }}</td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>



    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Balance Transfer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="transfer_form">
                    <div class="modal-body">
                        @csrf
                        <div class="mt-1">
                            <label for="">Transfer To</label>
                            <input type="email" name="email" required id="" class="form-control">
                        </div>
                        <div class="mt-2 mb-2">

                            <label for="">Amount</label>
                            <div class="input-group mb-2">
                                <select name="currency_id" id="currency">
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                            {{ $currency->symbol == '$' ? 'selected' : '' }}>
                                            {{ $currency->symbol }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="amount" class="form-control" required>
                                <br>

                            </div>
                            <span class="help-block with-error"> You can tarnfer maximum of $ {{ $totalAmount }}</span>

                        </div>

                    </div>
                    <div class="modal-footer">
                        {{-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> --}}
                        <button type="submit" class="btn btn-secondary">Transfer</button>

                    </div>
                </form>
            </div>
        </div>
    </div>



    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">
                        Payout Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="withdrawal_form">
                    <div class="modal-body">
                        @csrf
                        <div class="mt-1">
                            <p>Available balance : <strong> $ {{ $totalAmount }}</strong></p>

                        </div>
                        <div class="mt-2 mb-2">

                            <div class="input-group mb-2">
                                <select name="currency_id" id="currency">
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency->id }}"
                                            {{ $currency->symbol == '$' ? 'selected' : '' }}>
                                            {{ $currency->symbol }}</option>
                                    @endforeach
                                </select>
                                <input type="number" name="amount" placeholder="Amount" class="form-control" required>
                                <br>

                            </div>
                            <span class="help-block with-error"> Minimum withdrawal limit is $ 100</span>
                        </div>

                        <p class="text-info">
                            <i class="uil-comment-info">
                                <i class=""></i>
                                <span> A payout fee may apply from Ktwis</span>
                            </i>
                        </p>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-secondary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(document).on('submit', '#transfer_form', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            console.log(formData);
            $.ajax({
                type: "POST",
                url: "{{ route('vendor.wallet.transfer') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == false) {
                        toastr.error(data.msg);
                        console.log(data.msg);
                    } else {
                        toastr.success(data.msg);
                        window.location.reload();
                    }
                },
                error: function(data) {
                    console.log(data);
                    toastr.error("Something Went Wrong");
                }
            })
        });


        $(document).on('submit', '#withdrawal_form', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            console.log(formData);
            $.ajax({
                type: "POST",
                url: "{{ route('vendor.wallet.withdrawal') }}",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == false) {
                        toastr.error(data.msg);
                        console.log(data.msg);
                    } else {
                        toastr.success(data.msg);
                        window.location.reload();
                    }
                },
                error: function(data) {
                    console.log(data);
                    toastr.error("Something Went Wrong");
                }
            })
        });
    </script>
@endsection
