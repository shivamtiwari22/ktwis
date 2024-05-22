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
                    <p class="mt-3">Are you sure you want to delete ?</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!--  -->
<div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Wishlist Details</h4>
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
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cancellation List</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <!-- <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.shipping.rates.create')}}" class="btn btn-success">Add Rate</a>
    </div> -->
    <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Order</th>
                <th>Shop</th>
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
                    url: "{{ route('admin.cancellation.list') }}",
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
                        'orderable': false
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
                        "name": "shop_name",
                        'searchable': false,
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

    $(document).on('click','.approve',function(){
          var order_id = $(this).attr('data-id');
          console.log(order_id);

          if(confirm('Are you sure you want to approve this?')){
            $.ajax({
             url:"{{route('admin.cancellation.approved')}}",
             type:"POST",
             data:{
                order_id: order_id
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