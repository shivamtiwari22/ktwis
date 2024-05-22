@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')

    <head>

    </head>
@endsection


@section('css')
    <style>
        /* .mysize {
            max-width: 10%;
            position: fixed;
            right: 8%;
            padding-bottom: 0%;
        } */
        /* Add this to your existing CSS or in a separate style tag or file */
.top-right {
    position: absolute;
    top: 10px; /* Adjust the top position to your desired distance from the top */
    right: 10px; /* Adjust the right position to your desired distance from the right */
}


        .labeldata {
            color: red;
        }

        .note-insert {
            display: none;
        }

        .note-view {
            display: none;
        }
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Specifications</li>
        </ol>
    </nav>

    <div class="accordion row">

        <div class="accordion accordion-flush" id="accordionFlushExample">
            <div class="accordion-item">

                <div class="card p-4">
                    <label>Inbox</label>

                    <hr>
                    <button type="button" class="btn btn-primary top-right " data-bs-toggle="modal"
                        data-bs-target="#exampleModales">
                        Add New
                    </button>

                    <table id="review_table" class="table table-striped dt-responsive nowrap w-100 ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Message</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Specifications  </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reply_form">
                        <div>
                            <input type="hidden" id="inputID" name="id" readonly>
                            <label class="">Message<span class="text-danger"></span></label>
                            <textarea id="inputDescription" name="message" class="form-control summernote"></textarea>
                        </div>
                        <div class="mt-2" style="text-align: right;">
                            <input type="button" class="btn btn-warning mx-auto mydata mytype " value="Update">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
   
    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Warning</h4>
                        <p class="mt-3">Are you sure you want to delete</p>
                        <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="exampleModales" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Add Specifications</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reply_formes">
                    <div>
                        <label class="">Product  <span class="text-danger">*</span></label>
                        <select name="category" class="form-control" id="customerDropdown">
                            <option value="">Select product </option>
                          
                    
                        @foreach ($productsWithoutSpecifications as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                        @endforeach
                            {{-- @foreach ($productsWithoutSpecifications as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach --}}
                        </select>
                        <label class="mt-2">Message<span class="text-danger">*</span></label>
                        <textarea id="content" name="message" class="form-control summernote"></textarea>

                    </div>
                    <div class="mt-2" style="text-align: right;">
                        <input type="button" class="btn btn-success mx-auto mydatadraft mytype" value="Submit">
                    </div>
                    
                </form>
            </div>
           
          </div>
        </div>
      </div>
@endsection

@section('script')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $('.summernote').summernote({
            height: 100
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on('click', '#reply_formes .mydatadraft', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var form = $(this).closest('form');
                var formData = new FormData(form[0]);
                $.ajax({
                    url: "{{ route('vendor.specifications.add_specification') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.message, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            setTimeout(function() {
                                window.location.href = response.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.message, "top-right",
                                "rgba(0,0,0,0.2)", "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        var message = response.message;
                        console.log(message);
                        $.NotificationApp.send("Error", message, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                    }
                });
            });
        });
    </script>
    <script>
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
                        url: "{{ route('vendor.specifications.delete_specification') }}",
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
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)",
                                "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            });
        });
    </script>




    <script>
        function getSummernoteContent() {
            return $('#inputDescription').summernote('getCode');
        }
        $(document).on('click', '.openModalButton', function() {
            var disputeId = $(this).data('dispute');
            $.ajax({
                url: "{{ route('vendor.specifications.spacification_edit') }}",
                method: 'GET',
                data: {
                    disputeId: disputeId
                },
                success: function(response) {
                    var data = response.data;
                    $('#inputID').val(data.id);
                    $('#inputDescription').summernote('code', data.message);
                    $('#staticBackdrop').modal('show');
                },
                error: function(error) {
                    console.error('Error loading content: ' + error);
                }
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            $(document).on('click', '#reply_form .mydata', function(e) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                e.preventDefault();
                var form = $(this).closest('form'); // Get the parent form of the clicked button
                var formData = new FormData(form[0]); // Corrected line - use form[0] instead of form
                // alert(formData);
                $.ajax({
                    url: "{{ route('vendor.specifications.update_specification') }}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.message, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            setTimeout(function() {
                                window.location.href = response.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.message, "top-right",
                                "rgba(0,0,0,0.2)", "error");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText);
                        var response = JSON.parse(xhr.responseText);
                        var message = response.message;
                        console.log(message);
                        $.NotificationApp.send("Error", message, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                    }
                });
            });
        });
    </script>


    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#review_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    "responsive": false,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    "limit": false,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.specifications.specifications_data') }}",
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
                            "name": "",
                            'searchable': true,
                            'orderable': false
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "rating",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 3,
                            "name": "message",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 4,
                            "name": "rating",
                            'searchable': true,
                            'orderable': true
                        },
                    ],
                    "drawCallback": function(settings) {
                // Initialize tooltips after each table redraw
                $('[data-toggle="tooltip"]').tooltip();
                    },
                });
            };

            $.fn.tableload();
        });
    </script>
@endsection
