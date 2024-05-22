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

        .wrap-column {
            word-wrap: break-word;
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
                        <p class="mt-3">Are you sure you want to delete this carrier</p>
                        <button type="button" class="btn btn-warning my-2 delete" data-bs-dismiss="modal">Confirm</button>
                        <button type="button" class="btn btn-danger my-2 " data-bs-dismiss="modal">Cancel</button>

                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.carrier.list') }}">Carrier</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add New</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{ route('vendor.carrier.add_new') }}" class="btn btn-success">Add New</a>
        </div>
        <div class="table-responsive">
            <table id="carrier_table" class="table table-striped dt-responsive w-100 wrap-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Tracking URL</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Logo</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection


@section('script')
    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#carrier_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.carrier.listrender') }}",
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
                            'orderable': true,
                            "className": "text-lg",
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "category_name",
                            'searchable': true,
                            'orderable': true,
                            "className": "text-lg",
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "parent_category",
                            'searchable': true,
                            'orderable': true,
                            "className": "text-lg",

                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "parent_category",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "parent_category",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "parent_category",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "parent_category",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "parent_category",
                            'searchable': true,
                            'orderable': true
                        }
                    ]
                    ,
                    "drawCallback": function(settings) {
                // Initialize tooltips after each table redraw
                $('[data-toggle="tooltip"]').tooltip();
            }
                });
            };

            $.fn.tableload();

            $('body').on("click", ".deleteType", function(e) {
                var id = $(this).data('id');
                var name = $(this).data('name');
                let fd = new FormData();
                fd.append('id', id);
                fd.append('_token', '{{ csrf_token() }}');
                $("#warning-alert-modal-text").text(name);
                // Replace $.confirm with the success alert modal code
                $('#warning-alert-modal').modal('show');
                // Optionally, you can listen for the modal's "Continue" button click event
                $('#warning-alert-modal').on('click', '.delete', function() {
                    $.ajax({
                            url: "{{ route('vendor.carrier.delete') }}",
                            type: 'POST',
                            data: fd,
                            dataType: "JSON",
                            contentType: false,
                            processData: false,
                        })
                        .done(function(result) {
                            if (result.status) {
                                $.NotificationApp.send("Success", result.msg, "top-right",
                                    "rgba(0,0,0,0.2)", "success");
                                setTimeout(function() {
                                    window.location.href = result.location;
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", result.msg, "top-right",
                                    "rgba(0,0,0,0.2)", "error");
                            }
                        })
                        .fail(function(jqXHR, exception) {
                            console.log(jqXHR.responseText);
                        });
                });
            });

       

        });
    </script>

    <script>
        
 $('body').on('click', '.ChangeStatus', function(e) {
    e.preventDefault();

    var id = $(this).attr('data-id');
    var currentValue = $(this).attr('my-value'); // Get the current value of my-value attribute

    if (currentValue === '1') {
        $(this).attr('my-value', '0'); 
        $('#switch2_' + id).prop('checked', true); // Check the checkbox
    } else {
        $(this).attr('my-value', '1'); 
        $('#switch2_' + id).prop('checked', false); 
    }
    var id = $(this).attr('data-id');
    let fd = new FormData();
    fd.append('_token', "{{ csrf_token() }}");
    fd.append('id', id);

    $.ajax({
        url: "{{ route('vendor.carrier.status_update') }}",
        type: "POST",
        data: fd,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: function() {
            $("#load").show();
        },
        success: function(result) {
            if (result.status) {
                $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                
            } else {
                $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
            }
        },
        complete: function() {
            $("#load").hide();
        },
        error: function(jqXHR, exception) {
            console.log(jqXHR.responseText);
        }
    });
});

    </script>
@endsection
