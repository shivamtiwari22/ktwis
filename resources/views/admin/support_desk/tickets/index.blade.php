@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .data {
            text-align: right;
        }
    </style>
@endsection

@section('main_content')
<div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-warning"></i>
                    <h4 class="mt-2">Confirmation</h4>
                    <p class="mt-3">Are you Sure you Want to do this<b> <span id="warning-alert-modal-text"></span></b>.</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->  
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disputes</li>
        </ol>
    </nav>
       <div class="card p-4  mydata">   <label for="">OPEN TICKETS</label>
        <div class="data mb-1">
            {{-- <button type="button" class="btn btn-box-tool btn-hide" data-widget="collapse"> --}}
                <button type="button" class="btn btn-box-tool btn-hide" data-widget="collapse"
                    onclick="toggleButton(this)">
                    <i class="fa fa-minus"><b>+</b></i>
                </button>

                <button type="button" class="btn btn-box-tool btn-hide" data-widget="collapse"
                    onclick="toggleButtones(this)">
                    <i class="fa fa-minus"><b>x</b></i>
                </button>

        </div>
    
          <div class="card  myvariable ">
     
        <table id="rate_table" class="table table-striped dt-responsive nowrap w-100 ">
            <hr>
            <thead>
                <tr>
                    <th>Merchant</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Replies</th>
                    <th>Assigned to</th>
                    <th>Last Updated</th>
                    <th>Option</th>
                </tr>
            </thead>

        </table>
          </div>
</div>
<div class="card p-4 mydatas">
    <label for="">CLOSED TICKETS</label>
    <div class="data mb-1">
            <button type="button" class="btn btn-box-tool btn-hide" data-widget="collapse"
            onclick="toggleButtoness(this)">
            <i class="fa fa-minus"><b>+</b></i>
        </button>
        <button type="button" class="btn btn-box-tool btn-hide" data-widget="collapse"
        onclick="toggleButtoneses(this)">
        <i class="fa fa-minus"><b>x</b></i>
    </button>
    </div>
    <div class="card myVariable">
        <table id="show_table" class="table table-striped dt-responsive nowrap w-100">
            <hr>

            <thead>
                <tr>
                    <th>Merchant</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Assigned to</th>
                    <th>Last Updated</th>
                    <th>Option</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
@endsection


@section('script')

    <script>
        $(".myVariable").hide();
        function toggleButtoness(button) {
            const icon = button.querySelector("i");
            const currentSign = icon.textContent.trim();
            if (currentSign === "-") {
                // alert('hello');
                $(".myVariable").hide();
                icon.innerHTML = "<b>+</b>";

            } else {

                icon.innerHTML = "<b>-</b>";
                $(".myVariable").show();
            }
        }

        function toggleButtoneses(button) {
            $('.mydatas').hide();
        }
    </script>
    <script>
        function toggleButton(button) {
            const icon = button.querySelector("i");
            const currentSign = icon.textContent.trim();

            if (currentSign === "-") {
                // alert('hello');
                $(".myvariable").hide();
                icon.innerHTML = "<b>+</b>";

            } else {

                icon.innerHTML = "<b>-</b>";
                $(".myvariable").show();
            }
        }

        function toggleButtones(button) {
            $('.mydata').hide();
        }
    </script>

    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#rate_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    "responsive": false,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                
                    dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],
                    "ajax": {
                        url: "{{ route('admin.disputes.list_ticket_as') }}",
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
                            "name": "customer_name",
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
                            "name": "refund_requested",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "refund_amount",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "response",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "last_updated",
                            'searchable': true,
                            'orderable': true
                        },

                    ]
                });
            };

            $.fn.tableload();
        });
    </script>
    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#show_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    "responsive": false,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
        //             dom: 'Bfrtip',
        // buttons: [
        //     'copy', 'csv', 'excel', 'pdf', 'print'
        // ],
                    "ajax": {
                        url: "{{ route('admin.disputes.Close_list_tickets') }}",
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
                            "width": "10%",
                            "targets": 0,
                            "name": "S_no",
                            'searchable': true,
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
                            "targets": 3,
                            "name": "status",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 4,
                            "name": "refund_requested",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 5,
                            "name": "response",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 6,
                            "name": "last_updated",
                            'searchable': true,
                            'orderable': true
                        },

                    ]
                });
            };

            $.fn.tableload();
        $('body').on("click", ".deleteType", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');
            
            // alert('hello');
            
            $("#warning-alert-modal-text").text(name);
            // Replace $.confirm with the success alert modal code
            $('#warning-alert-modal').modal('show');
            // Optionally, you can listen for the modal's "Continue" button click event
            $('#warning-alert-modal').on('click', '.btn', function() {
                $.ajax({
                        url: "{{route('admin.disputes.restore')}}",
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
      window.location.href = result.location; // Redirect to the specified URL
      window.location.reload(); // Reload the new page after redirection
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
