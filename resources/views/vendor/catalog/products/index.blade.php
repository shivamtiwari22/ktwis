@extends('vendor.layout.app')

@section('meta_tags')

@endsection


@section('title')

@endsection


@section('css')
<style>
    .wrap-table {
        table-layout: fixed;
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
                    <h4 class="mt-2">Confirm</h4>
                    <p class="mt-3">Are You Sure to Delete this Vendor Application</p>
                    <input type="hidden" name="product_id" id="p_id">
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
            <li class="breadcrumb-item active" aria-current="page">Products Lists</li>
        </ol>
    </nav>
    <a href="{{route('vendor.products.create')}}"><button class="btn btn-success">Add Products</button></a>
</div>
<hr>
<div class="card mt-1 p-3">
    <table id="product_table" class="table table-striped dt-responsive wrap-table w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Image</th>
                <th>Brand</th>
                <th>Description</th>
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
                    url: "{{ route('vendor.products.list') }}",
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
                        "width": "5%",
                        "targets": 0,
                        "name": "S_no",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "product_name",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "featured_image",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "brand",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "description",
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
                        "targets": 3,
                        "name": "action",
                        'searchable': true,
                        'orderable': true
                    }
                ],
                "drawCallback": function(settings) {
                // Initialize tooltips after each table redraw
                $('[data-toggle="tooltip"]').tooltip();
            }
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
    $('body').on("click", "#delete_product", function(e) {
        var id = $(this).data('id');
        var name = $(this).data('name');
      
               $('#p_id').val(id);
        $("#warning-alert-modal-text").text(name);
        // Replace $.confirm with the success alert modal code
        $('#warning-alert-modal').modal('show');

    });
        // Optionally, you can listen for the modal's "Continue" button click event
        $('#warning-alert-modal').on('click', '.confirm', function() {
               var p_id = $('#p_id').val();
            let fd = new FormData();
              fd.append('id', p_id);
             fd.append('_token', '{{ csrf_token() }}');
            $.ajax({
                    url: "{{route('vendor.products.list.delete')}}",
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
 
</script>

@endsection


@section('script')

@endsection