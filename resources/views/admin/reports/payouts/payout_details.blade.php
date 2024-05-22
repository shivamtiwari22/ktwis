@extends('admin.layout.app')


@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .search_list {
            display: grid;
            grid-template-columns: auto auto auto auto auto;
            column-gap: 6px;
        }
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payouts</li>
        </ol>
    </nav>


    <div class="add-back">
        <form class="form-inline search_list">

            <div class="search-element bg-light rounded ">
                <select class="form-control" id="payment_status" aria-label="Search" data-width="">
                    <option value="">Search by guarantee charge</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>
            <div class="search-element bg-light rounded">
                <input type="date" name="from_date" class="form-control lg-light from_date" value="{{ date('Y-01-01') }}"
                    aria-label="Search">
            </div>
            <div class="search-element bg-light rounded">
                <input type="date" name="to_date" class="form-control lg-light to_date" value="{{ date('Y-m-d') }}"
                    aria-label="Search">
            </div>

            <button type="submit" id="searchButtones" class="form-control  ml-3 add-search btn btn-primary"
                data-width="200">Search</button>
        </form>
    </div>

    <div class="card p-4 mt-2">
        {{-- <div class=" ">
          <h4>Payout Detail</h4>
    </div>
    <hr> --}}
        <table id="rate" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order Id</th>
                    <th>Vendor Name</th>
                    <th>Vendor Email</th>
                    <th>Order Date & Time</th>
                    <th>Guarantee Charge </th>
                    <th>Commission Charge</th>
                    <th>Payout Status</th>
                    <th>Expected Payout Date</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                {{-- @php
            $totalAmount = 0;   
            $paidAmount = 0; 
            $returnAmount = 0;          
        @endphp
            @foreach ($orders as $order)
            @php
                 $pending = '';
            if($order->payment_release_status == "pending" &&  date('Y-m-d', strtotime($order->created_at . '+5 days')) < date('Y-m-d')){
                 $pending = 'due';
            }
            @endphp
            <tr  class="{{$order->payment_release_status == "released" ? "paid" : $pending }}">
                <td>{{$loop->iteration}}</td>
                <td><a href="{{route('admin.order.show_product_detail_admin', ['id' => $order->id])}}" target="_blank"> {{$order->order_number}}</a></td>
                <td>{{$order->vendor->name}}</td>
                <td>{{$order->vendor->email}}</td>
                <td>{{date('M d,Y  H:i:s', strtotime($order->created_at)) }}  </td>
                <td>{{$order->guarantee_charge}}</td>
                <td>{{$order->commission}}</td>
                <td>{{ucwords($order->payment_release_status)}}</td>
                <td>{{ date('M d, Y', strtotime($order->created_at . '+5 days'))}}</td>
                <td>{{$order->amount}}</td>
            </tr>
            @php
            $totalAmount += $order->amount;

            if($order->payment_release_status == "released"){
                $paidAmount +=$order->amount; 
            }

            if($order->payment_release_status == "return_to_customer"){
                $returnAmount +=$order->amount; 
            } --}}

                {{-- @endphp
            @endforeach

            @php
              $balanceAmount = $totalAmount - $paidAmount;
            @endphp --}}
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Total Amount</strong></td>
                    <td colspan="4"></td>
                    <td><strong class="total_guarantee"></strong></td>
                    <td><strong class="total_commission"></strong> </td>
                    <td colspan="2"></td>

                    <td><strong class="total_amount"></strong> </td>
                </tr>
            </tfoot>
        </table>



    </div>
    <div class="col-md-6">
        <h3><u>Summary</u></h3>
        <div class="card p-3">

            <p><strong>Total Amount</strong>: {{ $totalAmount }} </p>
            <p><strong>Paid amount</strong>: {{ $paidAmount }}</p>
            <p><strong>Balance Amount</strong>: {{ $balanceAmount }}</p>
            <p><strong>Return to Customer Amount</strong>: {{ $returnAmount }}</p>
            <p><strong>Total Commission</strong>: {{ $totalCommission }}</p>

        </div>


    </div>
@endsection


@section('script')
    <script>
        $(document).ready(function() {
            $('#searchButtones').click(function(event) {
                event.preventDefault();
                var from_date = $('.from_date').val();
                var to_date = $('.to_date').val();
                var payment_status = $('#payment_status').val();
                var order_status = $('#order_status').val();
                $.fn.loadDataTable(from_date, to_date, payment_status);
            });
        });


        $(function() {
            var dataTable;

            $.fn.loadDataTable = function(from_date, to_date, payment_status) {

                dataTable = $('#rate').DataTable({
                    "scrollX": true,
                    "processing": true,
                    "responsive": false,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('admin.payouts.list') }}",
                        "type": "POST",
                        "data": function(d) {
                            d._token = "{{ csrf_token() }}";
                            d.guarantee_charge = payment_status;
                            d.from_date = from_date;
                            d.to_date = to_date;
                        },
                        dataFilter: function(data) {
                            var json = jQuery.parseJSON(data);
                            json.recordsTotal = json.recordsTotal;
                            json.recordsFiltered = json.recordsFiltered;
                            json.summary_total = json.summary_total;
                            json.data = json.data;
                            $('.total_guarantee').text(json.summary_total);
                            console.log(json.summary_total);
                            return JSON.stringify(json);
                        }
                    },
                    "order": [
                        [0, 'DESC']
                    ],
                    "columns": [{
                            "width": "2%",
                            "targets": 0,
                            "name": "date",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "shop_name",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "type",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "status",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "balance_amount",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "amount",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "amount",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "amount",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "amount",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "amount",
                            'searchable': true,
                            'orderable': true
                        }
                    ],
                    "drawCallback": function(settings) {
                        var api = this.api();
                        var totalAmount = 0;
                        var totalCommission = 0;

                        // Calculate total amount from data
                        api.rows().data().each(function(row) {
                            totalAmount += parseFloat(row[9]);
                            totalCommission += parseFloat(row[
                            6]); // Assuming 'amount' is the key for total amount in your data
                        });

                        // Update the total amount display
                        $('.total_amount').text(totalAmount.toFixed(2));
                        $('.total_commission').text(totalCommission.toFixed(2));

                        // console.log(totalAmount);
                        // Assuming you want to display total amount with 2 decimal places
                    },

                });


            }

            $.fn.loadDataTable();



            $('#apply_filters').on('click', function() {
                dataTable.ajax.reload();
            });

        });
    </script>
@endsection
