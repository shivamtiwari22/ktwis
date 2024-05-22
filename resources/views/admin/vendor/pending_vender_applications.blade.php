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
                    <h4 class="mt-2">Confirm</h4>
                    <p class="mt-3">Are You Sure to Delete this Vendor Application</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.vendor.applications')}}">Vendor Application</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pending Application</li>
    </ol>
</nav>
<div class="card mt-1 p-3">
<div class="d-flex justify-content-end mb-2">
<a href="{{route('admin.vendor.applications')}}" class="btn btn-primary">View All Applications</a>
</div>
    <table id="category_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>TAX Type</th>
                <th>TAX Pin</th>
                <th>Approvel</th>
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
            $('#category_table').dataTable({
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
                    url: "{{route('admin.vendor.pending.applications.list')}}",
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
                "columns": [
                    {
                        "width": "5%",
                        "targets": 0,
                        "name": "S_no",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "category_name",
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

        $('body').on("click", ".deleteUser", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');

            $("#warning-alert-modal-text").text(name);
           
            $('#warning-alert-modal').modal('show');
            // Optionally, you can listen for the modal's "Continue" button click event
            $('#warning-alert-modal').on('click', '.btn', function() {
                $.ajax({
                    url: "{{route('admin.vendor.applications.list.delete')}}",
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

        $('body').on('change', '.change_status' , function(e){
                e.preventDefault();

                var status_value =   $(this).val();
                var application_id =   $(this).attr('data-id');


                console.log(status_value ,application_id);

                let fd = new FormData();
                fd.append('_token', "{{ csrf_token() }}");
                fd.append('application_id', application_id);
                fd.append('status_value', status_value);

                $.ajax({
                    url: "{{ route('admin.vendor.applications.list.status.update') }}",
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
</script>


@endsection
