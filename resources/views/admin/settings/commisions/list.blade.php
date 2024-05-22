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
                    <p class="mt-3">Are you Sure you Want to Delete<b> <span id="warning-alert-modal-text"></span></b>.</p>
                    <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a aria-current="page">Settings</a></li>
        <li class="breadcrumb-item"><a aria-current="page">Commisions</a></li>
    </ol>
</nav>
<div class="card mt-1 p-3">
<div class="d-flex justify-content-end mb-2">
<a href="{{route('commision.create')}}" class="btn btn-success">Add Commision</a>
</div>
    <table id="category_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Business Area</th>
                <th>Platform Charge</th>
                <th>Transaction  Charge</th>
                <th>Total Charge</th>
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
                    url: "{{ route('commision.list.render') }}",
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
                        "targets": 0,
                        "name": "S_no",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "targets": 1,
                        "name": "parent_category",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "targets": 1,
                        "name": "parent_category",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "targets": 1,
                        "name": "parent_category",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "targets": 1,
                        "name": "category_name",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "targets": 1,
                        "name": "parent_category",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "targets": 2,
                        "name": "action",
                        'searchable': true,
                        'orderable': true
                    }


                ]
            });
        }
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
            $('#warning-alert-modal').on('click', '.confirm', function() {
                $.ajax({
                        url: "{{route('commisiom.delete')}}",
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
        let fd = new FormData();
        fd.append('_token', "{{ csrf_token() }}");
        fd.append('id', id); // Corrected variable name
            $.ajax({
                url: "{{ route('commision.stausChange') }}",
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
                            setTimeout(function() {
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                },
                complete: function() {
                    $("#load").hide();
                },
                error: function(jqXHR, exception) {
                    console.log(jqXHR.responseText); // Log the error for debugging purposes
                }
            });
        });

        });

</script>

<script>
    function updateCheckboxValue(checkbox) {
        var hiddenInput = document.querySelector('input[name="status"][type="hidden"]');
        hiddenInput.value = checkbox.checked ? 1 : 0;
    }
</script>
@endsection
