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
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Payouts</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <div class="row mb-3">
        <div class="col-md-4">
            <label for="status_filter">Status:</label>
            <select id="status_filter" class="form-control">
                <option value="">All</option>
                <option value="Pending">Pending</option>
                <option value="Escrowed">Escrowed</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>
        <div class="col-md-4">
            <label for="type_filter">Transaction Type:</label>
            <select id="type_filter" class="form-control">
                <option value="">All</option>
                <option value="deposit">Deposit</option>
                <option value="withdraw">Withdrawal</option>
            </select>
        </div>
        <div class="col-md-4"><br>
            <button id="apply_filters" class="btn btn-primary">Apply Filters</button>
        </div>
    </div>
    <br>
    <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>Date</th>
                <th>Shop</th>
                <th>Type</th>
                <th>Status</th>
                <th>Remaining Balance</th>
                <th>Amount</th>
            </tr>
        </thead>
    </table>
</div>
@endsection


@section('script')
<script>
    $(function() {
        var dataTable;

        function loadDataTable() {
            dataTable = $('#rate_table').DataTable({
                "scrollX": true,
                "processing": true,
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
                        d.status_filter = $('#status_filter').val();
                        d.type_filter = $('#type_filter').val();
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
                    }
                ]
            });
        }

        loadDataTable();

        $('#apply_filters').on('click', function() {
            dataTable.ajax.reload();
        });

    });
</script>
@endsection