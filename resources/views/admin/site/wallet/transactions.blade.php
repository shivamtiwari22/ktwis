@extends('admin.layout.app')

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
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a aria-current="page" href="{{ route('admin.wallet.index') }}">Wallet</a></li>
            <li class="breadcrumb-item active"><a aria-current="page">Payout Requests</a></li>
        </ol>
    </nav>

    <div class="mt-2">
        <h3>Wallet Amount</h3>
        <strong style="font-size: 50px ; " class="ms-2">
            $ {{ $totalAmount }}
        </strong>
    </div>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            {{-- <a href="{{ route('vendor.coupon.addnew') }}" class="btn btn-primary">Transactions</a> --}}
        </div>
        <table id="wallet_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Vendor Name</th>
                    <th>Transaction Type</th>
                    <th>Currency</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->receiver->name }}</td>
                        <td>{{ $item->transaction_type }}</td>
                        <td>{{ $item->currency->symbol }}</td>
                        <td>{{ $item->amount }}</td>
                        <td>{{ $item->status }}</td>
                        <td>
                            @if ($item->status == 'Pending')
                                <a href="javascript:void(0)" data-name="Cancelled" data-id="{{ $item->id }}"
                                    class="btn btn-danger status">Approve</a>
                            @endif
                            @if ($item->status != 'Complete' && $item->status == 'Pending')
                                <a href="javascript:void(0)" data-name="Completed" data-id="{{ $item->id }}"
                                    class="btn btn-success status">Decline</a>
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $(document).on('click', '.status', function(e) {

            var result = confirm("Are you sure you want to decline this request?");

            if (result) {
                e.preventDefault();
                var status = $(this).attr('data-name');
                var id = $(this).attr('data-id');
                console.log(status);
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.wallet.status') }}",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id,
                        status: status,
                    },
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
            }
        });
    </script>
@endsection
