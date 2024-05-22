@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style>
       .mysize {
        
        position: absolute;
        top: 10px; /* Adjust the top position to your desired distance from the top */
        right: 10px; /* Adjust the right position to your desired distance from the right */
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
                    <p class="mt-3">Are you sure you want to do this testimonial </p>
                    <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal --> 
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Testimonial</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <label for="">Testimonial</label>
        <hr>
        {{-- <button type="button" class="btn btn-primary mysize" data-bs-toggle="modal" data-bs-target="#exampleModales">
            Add Sale Banner
          </button>   --}}
          <a href="{{route('admin.test.test_monial_add')}}" class="btn btn-primary mysize">Add 
        </a>
        <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Customer</th>
                    <th>Testimonial</th>
                    <th>Date</th>
                    <th>Rating</th>
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
                    "responsive": false,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('admin.test.test_show') }}",
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
                            "name": "action",
                            'searchable': true,
                            'orderable': true
                        }
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
            $('#warning-alert-modal').on('click', '.confirm', function() {
                $.ajax({
                        url: "{{route('admin.test.delete_testimonial')}}",
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
    //   window.location.href = result.location; // Redirect to the specified URL
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
