@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')

 <style>
    .due {
        background-color: red !important;
        color: white !important;
    }

    .paid {
        background-color: green!important;
        color: white!important;
    }

    .table-striped>tbody>tr:nth-of-type(odd){
        color:unset;
        --bs-table-accent-bg:unset;
    }

    .search_list {
            display: grid;
    grid-template-columns: auto auto auto auto auto ;
    column-gap: 6px;
        }
 </style>
@endsection

@section('main_content')
   
    <div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Payout Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                {{-- <div class="modal-body"> --}}
                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">


                </div>
            </div>
        </div>
    </div>

    <!--  -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Payout</li>
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
                <input type="date" name="from_date" class="form-control lg-light from_date"
                    value="{{ date('Y-01-01') }}" aria-label="Search">
            </div>
            <div class="search-element bg-light rounded">
                <input type="date" name="to_date" class="form-control lg-light to_date"
                    value="{{ date('Y-m-d') }}" aria-label="Search">
            </div>

            <button type="submit" id="searchButtones" class="form-control  ml-3 add-search  btn btn-primary"
                data-width="200">Search</button>
        </form>
    </div>

      
    <div class="card p-4 mt-2">
        {{-- <div class=" ">
              <h4>Payout Detail</h4>
        </div>
        <hr> --}}
        <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order Id</th>
                    <th>Order Date & Time</th>
                    <th>Guarantee Charge </th>
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
                @foreach($orders as $order)
                @php
                     $pending = '';
                if($order->payment_release_status == "pending" &&  date('Y-m-d', strtotime($order->created_at . '+5 days')) < date('Y-m-d')){
                     $pending = 'due';
                }
                @endphp
                <tr  class="{{$order->payment_release_status == "released" ? "paid" : $pending }}">
                    <td>{{$loop->iteration}}</td>
                    <td><a href="{{route('vendor.order.show_product_detail', ['id' => $order->id])}}" target="_blank"> {{$order->order_number}}</a></td>
                    <td>{{date('M d,Y  H:i:s', strtotime($order->created_at)) }}  </td>
                    <td>{{$order->guarantee_charge}}</td>
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
                }

            @endphp
                @endforeach

                @php
                  $balanceAmount = $totalAmount - $paidAmount;
                @endphp --}}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"></td>
                    <td><strong>Total Amount:</strong></td>
                    <td><strong class="total_amount"></strong> </td>
                </tr>
            </tfoot>
        </table>


     
    </div>
    <div class="col-md-6">
         <h3><u>Summary</u></h3>
        <div class="card p-3">
            
         <p><strong>Total Amount</strong>: {{$totalAmount}} </p>
         <p><strong>Paid amount</strong>: {{$paidAmount}}</p>
         <p><strong>Balance Amount</strong>: {{$balanceAmount}}</p>
         <p><strong>Return to Customer Amount</strong>: {{$returnAmount}}</p>
         <p><strong>Charged Amount </strong>: {{$totalCommission}}</p>
        </div>


    </div>


@endsection


@section('script')
  <script>
    //    $(document).ready(function() {
    //         $('#rate_table').DataTable({
    //             // dom: 'Bfrtip',
    //             buttons: [{
    //                 extend: 'csv',
    //                 split: ['pdf', 'excel', 'csv'],
    //             }]
    //         });
    //     });


    
    $(document).ready(function() {
            $('#searchButtones').click(function(event) {
                event.preventDefault();
                var from_date = $('.from_date').val();
                var to_date = $('.to_date').val();
                var payment_status = $('#payment_status').val();
                var order_status = $('#order_status').val();
                $.fn.tableload(from_date, to_date, payment_status);
            });
        });


        $(function() {
            $.fn.tableload = function(from_date, to_date, payment_status) {
                $('#rate_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    pageLength: 10,
                    "serverSide": true,
                    "responsive": false,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.postPayoutDetail') }}",
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
                            json.data = json.data;
                            return JSON.stringify(json);
                        }
                    },
                    "order": [
                        [0, 'DESC']
                    ],
                    "columns": [{
                            "width": "5%",
                            "targets": 0,
                            "name": "S_no",
                            'searchable': true,
                            'orderable': false
                        },
                        {
                            "width": "5%",
                            "targets": 1,
                            "name": "details",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "topic",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "last_updated",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "action",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "topic",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "topic",
                            'searchable': true,
                            'orderable': true
                        }
                    ]
                    ,
                    "drawCallback": function(settings) {
        var api = this.api();
        var totalAmount = 0;

        // Calculate total amount from data
        api.rows().data().each(function(row) {
            totalAmount += parseFloat(row[6]); // Assuming 'amount' is the key for total amount in your data
        });

        // Update the total amount display
        $('.total_amount').text(totalAmount.toFixed(2)); 
        
        console.log(totalAmount);
        // Assuming you want to display total amount with 2 decimal places
    },
                });
            }
            $.fn.tableload();

        });



  </script>
 
@endsection
