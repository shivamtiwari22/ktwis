@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')

    <head>
        <!-- Other meta tags and stylesheets -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
@endsection


@section('css')
    <style>
        .accordion-button {
            padding: 1% !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: #ffffff;
        }

        a.liAct {
            display: inline-block;
            color: rgb(238, 5, 5);
        }

        /* CSS to change the color of the link text to white */
        .white-link {
            color: rgb(8, 8, 8);
            /* Add other styles as needed */
        }
        .mydata{
        margin-top: 5%;
    /* margin-bottom: -5%; */
    margin-left: 10%;

       }
       .btn-info a {
            color: white;
            text-decoration: none;
            /* Remove underline from the link */
        }
        .btn-secondary a {
            color: white;
            text-decoration: none;
            /* Remove underline from the link */
        }
        .mytype{
            margin-top:0%;
        }

        .fa {
    font-family: 'FontAwesome';
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
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('vendor.message.message_index') }}">Message</a></li>
            <li class="breadcrumb-item active" aria-current="page">Draft</li>
                </ol>
    </nav>
     <!-- The Modal -->
     <div id="myModal" class="modal">
        <div class="modal-content">
            <div class="myclose">

                <span class="close ">&times;</span>
            </div>
            <form id="reply_form">

                <div>
                    <label>SEARCH CUSTOMER*</label>
                    <select name="customer" class="form-control" id="customerDropdown">
                        <option value="">select customer </option>

                        @foreach ($datas as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>

                    <label class="mt-2">SUBJECT*</label>
                    <input type="text" name="subject" class="form-control">
                
                    <label class="mt-2">MESSAGE*</label>
                    <textarea id="content" name="message" class="form-control summernote"></textarea>
                    <label class="mt-2">Upload file</label>
                    <input type="file" name="file_data" class="form-control">
                </div>
                <div class="mt-2">

                    <button type="submit" class="btn btn-success mx-auto"> Save as draft</button>

                    <input type="button" class="btn btn-info mx-auto mydata mytype"  value="Save">



                </div>
            </form>
        </div>
    </div>



      
      <!-- Modal -->
      <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Send a Template</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reply_form">

                    <div>
                        <label>Select Customer<span class="text-danger">*</span></label>
                        <select name="customer" class="form-control" id="customerDropdown">
                            <option value="">Select customer </option>
    
                            @foreach ($datas as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <label class="mt-2">Select Email Template<span class="text-danger">*</span></label>
                        <select name="email" class="form-control" id="customerDropdown">
                            <option value="">Select Template </option>
    
                            @foreach ($message as $data)
                                <option value="{{ $data->id }}">{{ $data->subject }}</option>
                            @endforeach
                        </select>
    
    
                   
                        <label class="mt-2">Upload file</label>
                        <input type="file" name="file_data" class="form-control">
                    </div>
                    <div class="mt-2" style="text-align: right;">
    
                        <input type="button" class="btn btn-success mx-auto mydatadraft mytype"  value="Save as draft">
    
                        <input type="button" class="btn btn-info mx-auto mydataes mytype"  value="Save">
    
    
    
                    </div>
            
                </form>
        </div>
          
          </div>
        </div>
      </div>
      
      <!-- Modal -->
      <div class="modal fade" id="staticBackdropes" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Compose New Message</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="save-draft"></button>
            </div>
            <div class="modal-body">
                <form id="reply_form">

                    <div>
                        <label>Select Customer<span class="text-danger">*</span></label>
                        <select name="customer" class="form-select select2 customer_value" id="customerDropdown">
                            <option value="">Select customer </option>
    
                            @foreach ($datas as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} | {{$customer->email}}</option>
                            @endforeach
                        </select>
    
                        <label class="mt-2">Subject<span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" id="subject">
                    
                        <label class="mt-2">Message<span class="text-danger">*</span></label>

                        <textarea id="content" name="message" class="form-control summernote"></textarea>
                        <label class="mt-2">Upload file</label>
                        <input type="file" name="file_data" class="form-control">
                    </div>
                    <div class="mt-2" style="text-align: right;">
    
                        <button type="submit" class="btn btn-success mx-auto"> Save as draft</button>
    
                        <input type="button" class="btn btn-info mx-auto mydata mytype"  value="Save">
    
    
    
                    </div>
                </form>
        </div>
          
          </div>
        </div>
      </div>
    <div class=" accordion row">
        <div class="col-md-3">
            <div class="card mt-2">
                <button type="button" class="btn btn-secondary  mt-2" data-bs-toggle="modal" data-bs-target="#staticBackdropes">
                    <small>Compose New Message</small>   </button>
                
    {{-- <button type="button" class="btn btn-info  mt-2" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
        <small>Send A Template</small>      </button> --}}
            </div>
            <div class="card mt-2">

                <div class="card-body card-padding">
                    <label>Folders</label>
                    <hr>
                    <li class="list-group-item bg-transparent text-dark">
                        <a href="{{ route('vendor.message.message_index') }}"><i class="fa fa-inbox"></i> Inbox</a>
                    </li>
                    <li class="list-group-item bg-transparent text-dark">
                        <a href="{{ route('vendor.message.sent_message') }}" class=""><i class="fa fa-envelope-o"></i>
                            Sent</a>
                    </li>
                    <li class="list-group-item bg-transparent text-dark">
                        <a href="{{ route('vendor.message.draft_message') }}" class="liAct"><i
                                class="fa fa-file-text-o"></i> Drafts</a>
                    </li>
                    <li class="list-group-item bg-transparent text-dark">
                        <a href="{{ route('vendor.message.spams_message') }}"><i class="fa fa-filter"></i> Spam</a>
                    </li>
                    <li class="list-group-item bg-transparent text-dark">
                        <a href="{{ route('vendor.message.trash_message') }}"><i class="fa fa-trash-o"></i> Trash</a>
                    </li>



                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">

                    <div class="card p-4">
                        <label>Draft</label>
                        <hr>

                        <div class="dropdown mb-3">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" id="spam_trash" style="display: none">
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu ">
                                <li>
                                    <p class="white-link mydata" id="submit-btn"><i class="fa fa-filter icon"></i> Spam</p>
                                </li>
                                <hr>
                                <li>
                                    <p class="white-link mydata" id='submit-trash'><i class="fa fa-trash-o icon"></i> Trash
                                    </p>
                                </li>
                            </ul>
                            <button id="reloadButton" class="btn btn-secondary "><i class="fa fa-refresh"></i></button>

                        </div>
                        <table id="review_table" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all-checkbox"></th>
                                    <th>Customer</th>
                                    <th>Message</th>
                                    <th>File</th>
                                    <th>Creation</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    @endsection


    @section('script')
        <script>
            const reloadButton = document.getElementById("reloadButton");

            reloadButton.addEventListener("click", function() {
                location.reload();
            });

            $(document).on("click", '.task-checkbox', function() {
                    if ($(this).prop("checked")) {
                          $('#spam_trash').show();
                    }
                    else {
                        $('#spam_trash').hide();

                    }
                });


                $(document).on("click", '#select-all-checkbox', function() {
                    if ($(this).prop("checked")) {
                          $('#spam_trash').show();
                    }
                    else {
                        $('#spam_trash').hide();

                    }
                });
        </script>

        <script>
            $(document).ready(function() {
                $(document).on("click", '.task-checkbox', function() {
                    if ($(this).prop("checked")) {
                        var id = $(this).data("id");
                    }
                });

                $("#submit-trash").click(function() {
                    var selectedIds = [];
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $(".task-checkbox:checked").each(function() {
                        selectedIds.push($(this).data("id"));
                    });

                    var formData = new FormData();
                    for (var i = 0; i < selectedIds.length; i++) {
                        formData.append("ids[]", selectedIds[i]);
                    }

                    $.ajax({
                        url: "{{ route('vendor.message.draft_trash_data') }}",
                        type: "POST",
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
            $(document).ready(function() {
                $(document).on("click", '.task-checkbox', function() {
                    if ($(this).prop("checked")) {
                        var id = $(this).data("id");
                    }
                });

                $("#submit-btn").click(function() {
                    var selectedIds = [];
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $(".task-checkbox:checked").each(function() {
                        selectedIds.push($(this).data("id"));
                    });

                    // Convert the data to a regular form data object
                    var formData = new FormData();
                    for (var i = 0; i < selectedIds.length; i++) {
                        formData.append("ids[]", selectedIds[i]);
                    }
                    // alert(formData);
                    $.ajax({
                        url: "{{ route('vendor.message.draft_spams_data') }}",
                        type: "POST",
                        data: formData, // Use the formData object here
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













            $(document).ready(function() {
                $(document).on('click', '#save-draft', function(e) {
                    e.preventDefault();
                      var customer_id = $('.customer_value').val();
                      var message = $('#content').summernote('code');
                      var subject = $('#subject').val(); 
                
                    $.ajax({
                        url: "{{ route('vendor.message.save-as-draft') }}",
                        method: "POST",
                        data: {
                            'customer' : customer_id,
                            'message' : message,
                            'sub' : subject
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            console.log(response);
                         $('.customer_value').val('');
                         $('#content').summernote('code','');
                         $('#subject').val(''); 

                            // if (response.status) {
                            //     $.NotificationApp.send("Success", response.message, "top-right",
                            //         "rgba(0,0,0,0.2)", "success");
                             
                            // } else {
                            //     $.NotificationApp.send("Error", response.message, "top-right",
                            //         "rgba(0,0,0,0.2)", "error");
                            // }
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
            $(document).ready(function() {
                $('#review_table').DataTable();

                $('#select-all-checkbox').on('change', function() {
                    if (this.checked) {
                        $('.task-checkbox').prop('checked', true);
                    } else {
                        $('.task-checkbox').prop('checked', false);
                    }
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
                            url: "{{ route('vendor.message.message_data_draft') }}",
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
                                "targets": 1,
                                "name": "rating",
                                'searchable': true,
                                'orderable': true
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
                                "targets": 2,
                                "name": "rating",
                                'searchable': true,
                                'orderable': true
                            },
                        ]
                    });
                };

                $.fn.tableload();
            });
        </script>
            <script type="text/javascript">
                $('.summernote').summernote({
                    height: 50
                });
            </script>
        
        <script>
            $(document).ready(function() {
                $(document).on('click', '#reply_form .mydatadraft', function(e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    e.preventDefault();
                    var form = $(this).closest('form');
                    var formData = new FormData(form[0]); 
                    $.ajax({
                        url: "{{ route('vendor.message.email_template_draft') }}",
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
            $(document).ready(function() {
                $(document).on('click', '#reply_form .mydataes', function(e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    e.preventDefault();
                    var form = $(this).closest('form');
                    var formData = new FormData(form[0]); 
                    $.ajax({
                        url: "{{ route('vendor.message.email_template') }}",
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
            $(document).ready(function() {
                $(document).on('click', '#reply_form .mydata', function(e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    e.preventDefault();
                    var form = $(this).closest('form');
                    var formData = new FormData(form[0]); 
                    $.ajax({
                        url: "{{ route('vendor.message.composer_data_send_save') }}",
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
            $(document).ready(function() {
                $(document).on('submit', '#reply_form', function(e) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    e.preventDefault();
                    var form = $(this);
                    var formData = new FormData(form[0]);
                    $.ajax({
                        url: "{{ route('vendor.message.composer_data_send') }}",
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
    @endsection
