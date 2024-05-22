@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disputes</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <label for="">DISPUTES</label>
        <hr>
        <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Order Id</th>
                    <th>Type</th>
                    <th>Refunded Requested</th>
                    <th>Refund Amount</th>
                    <th>Response</th>
                    <th>Guarantee Charge</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="card p-4 mt-4">
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
    </div>
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
                        url: "{{ route('admin.disputes.list') }}",
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
                            "targets": 1,
                            "name": "customer_name",
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
