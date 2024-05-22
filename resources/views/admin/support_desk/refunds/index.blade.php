@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')

@endsection


@section('css')
    <style>
        .modal-header {
            background: black;
            color: #ffc233;
            display: flex;
            /* align-items: center; */
            /* justify-content: center; */
            flex-flow: row-reverse;
            text-align: center;
            padding: 0 20px;
            padding-bottom: 5px;

            .close {
                color: white;
                opacity: 1;
                display: flex;
                // align-self: flex-start;
                padding-top: 5px;
            }
        }

        // modal title text logo
        h3 {
            &#myModalLabel {
                flex: 1;
                text-transform: uppercase;

                span {
                    color: white;
                }
            }
        }

        a.back,
        a.next,
        a.back:focus,
        a.next:focus,
        a.back:active,
        a.next:active {
            color: white;
            background: #ffc233;
            padding: 10px 60px;
            margin: 0;
            font-weight: bold;
        }

        a.back:hover,
        a.next:hover {
            color: #333;
            background-color: #e6e6e6;
            border-color: #adadad;
        }

        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .left-footer {
            display: flex;
        }

        .right-footer {
            flex: 1;
        }

        // prevent nav tabs from collapsing on mobile
        // using flex to fill the modal width
        // otherwise use table-cell and float
        .nav-justified {
            display: flex;

            li {
                flex: 1;
                // display: table-cell;
                // float: left;
            }
        }
    </style>
@endsection

@section('main_content')
 
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Refund</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <label for="">REFUND</label>
        <hr>
        <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order Id</th>
                    <th>Vendor Name</th>
                    <th>Order Amount</th>
                    <th>Refund Amount</th>
                    <th>Status</th>
                    <th>Guarantee Charge</th>
                    <th>Created at </th>
                    <th>Last update </th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    {{-- <div class="card p-4 mt-4">
        <label for="">CLOSED DISPUTES</label>
        <table id="rate_tables" class="table table-striped dt-responsive nowrap w-100">
            <hr>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>Refunded Requested</th>
                    <th>Refund Amount</th>
                    <th>Response</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div> --}}
@endsection


@section('script')


    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#rate_table').dataTable({
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
                        url: "{{ route('admin.refund.order_list') }}",
                        "type": "POST",
                        "data": function(d) {
                            d._token = "{{ csrf_token() }}";
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
                            "width": "2%",
                            "targets": 0,
                            "name": "S_no",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "customer_name",
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
                            "name": "refund_requested",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "refund_amount",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "response",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "response",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "last_updated",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
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
                        }
                    ]
                });
            };

            $.fn.tableload();
        });
    </script>
    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#rate_tables').dataTable({
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
                        url: "{{ route('admin.disputes.close') }}",
                        "type": "POST",
                        "data": function(d) {
                            d._token = "{{ csrf_token() }}";
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
                            "width": "2%",
                            "targets": 0,
                            "name": "S_no",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "customer_name",
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
                            "name": "refund_requested",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "refund_amount",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "response",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
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
                        }
                    ]
                });
            };

            $.fn.tableload();
        });
    </script>
@endsection
