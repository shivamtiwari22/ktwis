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
                    <h4 class="mt-2">Confirm</h4>
                    <p class="mt-3">Are You Sure to Delete this Tax ?</p>
                    <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div style="display: flex; justify-content: space-between; align-items: flex-end;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tax</li>
        </ol>
    </nav>
    <a href="{{route('vendor.settings.tax.create')}}"><button class="btn btn-success">Add Tax</button></a>
</div>
<hr>
<div class="card mt-1 p-3">
    <table id="product_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Tax Name</th>
                <th>Tax Rate</th>
                <th>Region</th>
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
            $('#product_table').dataTable({
                "scrollX": true,
                "processing": true,
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('vendor.settings.tax.list') }}",
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
                        "width": "5%",
                        "targets": 1,
                        "name": "tax_name",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "5%",
                        "targets": 2,
                        "name": "tax_rate",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "region",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "5%",
                        "targets": 2,
                        "name": "status",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 3,
                        "name": "action",
                        'searchable': true,
                        'orderable': true
                    }
                ]
            });
        };

        $.fn.tableload();

        $('body').on('change', '.change_status', function(e) {
            e.preventDefault();

            var status_value = $(this).val();
            var p_id = $(this).attr('data-id');


            console.log(status_value, p_id);

            let fd = new FormData();
            fd.append('_token', "{{ csrf_token() }}");
            fd.append('application_id', p_id);
            fd.append('status_value', status_value);

            $.ajax({
                    url: "{{ route('vendor.products.list.status.update') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
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

    // delete products
    $('body').on("click", "#delete_tax", function(e) {
        var id = $(this).data('id');
        var name = $(this).data('name');
        let fd = new FormData();
        fd.append('id', id);
        fd.append('_token', '{{ csrf_token() }}');

        $("#warning-alert-modal-text").text(name);
        $('#warning-alert-modal').modal('show');
        $('#warning-alert-modal').on('click', '.confirm', function() {
            $.ajax({
                    url: "{{route('vendor.settings.tax.delete')}}",
                    type: 'POST',
                    data: fd,
                    dataType: "JSON",
                    contentType: false,
                    processData: false,
                })
                .done(function(result) {
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                        location.reload();
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                    }
                })
                .fail(function(jqXHR, exception) {
                    console.log(jqXHR.responseText);
                });
        });
    });
</script>

@endsection


@section('script')

@endsection