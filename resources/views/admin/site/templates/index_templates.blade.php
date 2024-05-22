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
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal" id="confirm">Confirm</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div style="display: flex; justify-content: space-between; align-items: flex-end;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Email Templates</li>
        </ol>
    </nav>
    <a href="{{route('admin.appereance.create_templates')}}"><button type="button" class="btn btn-primary">Add Templates</button></a>
</div>
<hr>
<div class="card mt-1 p-3">
    <table id="page_appereance" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Template for</th>
                <th>Sender Name</th>
                <th>Sender Email</th>
                <th>Subject</th>
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
            $('#page_appereance').dataTable({
                "scrollX": true,
                "processing": true,
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('admin.appereance.list_templates') }}",
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
                        "name": "name",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "template_for",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "sender_name",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "sender_email",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "subject",
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
        }
        $.fn.tableload();

        $('body').on("click", ".delete_templates", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('name', name);
            fd.append('_token', '{{ csrf_token() }}');


            $("#warning-alert-modal-text").text(name);
            $('#warning-alert-modal').modal('show');
            $('body').on('click', '#confirm', function() {
                $.ajax({
                        url: "{{route('admin.appereance.delete_templates')}}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.message, "top-right", "rgba(0,0,0,0.2)", "success");
                            $.fn.tableload();
                        } else {
                            $.NotificationApp.send("Error", result.message, "top-right", "rgba(0,0,0,0.2)", "error");
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