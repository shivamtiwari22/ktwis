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
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cancellation List</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    
    <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Order</th>
                <th>Customer</th>
                <th>Grand Total</th>
                <th>Payment</th>
                <th>Requested items</th>
                <th>Requested at</th>
                <th>Status</th>
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
                "responsive":false,
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('vendor.cancellation.list') }}",
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
                        "name": "order_number",
                        'searchable': false,
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
                        "name": "total_amount",
                        'searchable': false,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "payment_status",
                        'searchable': false,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "request_item",
                        'searchable': false,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "requested_at",
                        'searchable': false,
                        'orderable': true
                    },
                    {
                    "width": "10%",
                        "targets": 2,
                        "name": "status",
                        'searchable': false,
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

    $(document).on('click','.action',function(){
          var order_id = $(this).attr('data-id');
          var status = $(this).attr('data-name');

          if (confirm("Are you sure you want to do this?")) {
                console.log(order_id,status);
          $.ajax({
             url:"{{route('vendor.cancellation.approved')}}",
             type:"POST",
             data:{
                order_id: order_id,
                status:status
             },
             headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
             success:function(result){
                console.log(result);
                if (result.status) {
                    $.NotificationApp.send("Success", result.msg, "top-right",
                        "rgba(0,0,0,0.2)", "success")
                         window.location.reload();

                } else {
                    $.NotificationApp.send("Error", result.msg, "top-right",
                        "rgba(0,0,0,0.2)", "error")
                }
             },
             error:function(result){
                $.NotificationApp.send("Error", "something went wrong", "top-right",
                        "rgba(0,0,0,0.2)", "error")
             }
          })
        }
    })
</script>
@endsection