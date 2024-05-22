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
                    <p class="mt-3">Are you sure you want to delete ?</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div>
    </div>
</div>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Shops</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.settings.shops.create')}}" class="btn btn-success">Add Shops</a>
    </div>
    <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Shipping Carrier</th>
                <th>Delivery Days</th>
                <th>Order Weight</th>
                <th>Rate</th>
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
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('vendor.shipping.list_rate') }}",
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
                        "name": "name",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "carrier",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "time",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "order_value",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "rate",
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

        $('body').on("click", ".delete_rate", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');

            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');
            $("#warning-alert-modal-text").text(name);
            $('#warning-alert-modal').modal('show');
            $('#warning-alert-modal').on('click', '.btn', function() {
                $.ajax({
                        url: "{{route('vendor.shipping.rates.delete')}}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            });
        });
    });
</script>
@endsection