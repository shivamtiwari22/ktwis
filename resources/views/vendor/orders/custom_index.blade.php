@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection


@section('css')
<style>
      .select2-container {
            z-index: 10000;
        }

        .justi-content {
            justify-content: space-between;
        }

        .search_list {
            display: grid;
    grid-template-columns: auto auto auto auto auto auto;
    column-gap: 5px;
        }
</style>
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
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item "><a href="{{ route('vendor.order.index') }}">Orders</a></li>

            <li class="breadcrumb-item active" aria-current="page">Custom Orders</li>
        </ol>
    </nav>

    <div class="add-back">
        <form class="form-inline search_list">
         
            <div class="search-element bg-light rounded ">
                <select class="form-select" id="payment_status" aria-label="Search" data-width="">
                    <option value="">Search by Payment Status</option>
                    <option value="paid">Paid</option>
                    <option value="unpaid">Unpaid</option>
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

            <button type="submit" id="searchButtones" class="form-control  ml-3 add-search btn btn-primary"
                data-width="200">Search</button>
        </form>
    </div>

    <div class="card p-4 mt-2">
        <div class="d-flex justify-content-end mb-2">
            <a href="javascript::void(0);" data-target="#exampleModalCenter" data-toggle="modal"
                class="btn btn-success">Create Order</a>
        </div>
        <table id="order-table" class="table table-striped dt-responsive nowrap w-100 ">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order id</th>
                    <th>Order Date</th>
                    <th>Customer Email</th>
                    <th>Grand Total</th>
                    <th>Payment</th>
                    <th>Payment link</th>
                 
           
                </tr>
            </thead>
            <tbody>
                {{-- @foreach ($orders as $order)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>#{{ $order->order_number }}</td>
                        <td>{{ $order->created_at->format('D, M j, Y g:i A') }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->total_amount }}</td>
                        <td>{{ $order->payment_status  }}</td>
                        <td id="url">{{ $order->payment_url }} </td>
                    </tr>
                @endforeach --}}
            </tbody>
        </table>


        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">
                            Search Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('vendor.order.add') }}" method="POST" id="guideForm">
                        <div class="modal-body">
                            @csrf


                            <div class="mt-2 mb-2">
                                <div class=" mb-2">
                                    <label for="order_type"> Order Type</label>
                              
                                    <select name="order_type" id="order" class="form-select select mt-1" >
                                        <option value="" selected disabled>Select Type</option>
                                        <option value="regular">Regular Order</option>
                                        <option value="custom">Custom Order</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-2 mb-2" id="customer">
                                <div class="mb-2">
                                    <label for="">Customer</label>
                                    <div style="display: flex">
                                    <select name="customer_id" id="" class="form-select select2 mt-1" >
                                        <option value="" selected disabled>Select Customer</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }} |
                                                {{ $customer->email }}</option>
                                        @endforeach

                                    </select>
                                    <a href="{{route('vendor.customer.index')}}" class="btn btn-success"><i class="fa fa-plus"></i></a>
                                </div>
                                <label for="customer_id" id="customer_id-error" class="error"></label>

                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-secondary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

$(document).ready(function() {
            $('.select2').select2({});
        });


        $(document).ready(function() {
            $('#order-table').DataTable({
                responsive: false,
                scrollX: true
            });
        });




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
                $('#order-table').dataTable({
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
                        url: "{{ route('vendor.custom_order_listing') }}",
                        "type": "POST",
                        "data": function(d) {
                            d._token = "{{ csrf_token() }}";
                        d.payment_status = payment_status;
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
                            "width": "60%",
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
                });
            }
            $.fn.tableload();

        });





        $(document).on('click','#url',function(){
            var copyText = $(this).text();
            navigator.clipboard.writeText(copyText);
            $.NotificationApp.send('', 'URL Copied Successfullly' , "top-right",
                                "rgba(0,0,0,0.2)", "success")
        })

        $('#guideForm').validate({
            rules: {
                order_type: 'required',
                customer_id : {
                    required: true,
                },
            },
         
        })
      
    </script>
@endsection
