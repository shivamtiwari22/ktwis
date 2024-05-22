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
        a.liAct {
            display: inline-block;
            color: rgb(238, 5, 5);
        }

        /* CSS to change the color of the link text to white */
        .white-link {
            color: rgb(8, 8, 8);
            /* Add other styles as needed */
        }

        .btn-secondary a {
            color: white;
            text-decoration: none;
            /* Remove underline from the link */
        }

        .btn-info a {
            color: white;
            text-decoration: none;
            /* Remove underline from the link */
        }

        .mydata {
            margin-top: 5%;
            /* margin-bottom: -5%; */
            margin-left: 10%;

        }

        .icon {
            margin-left: 10%;

        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .myModal {
            width: 400px;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 115%;
            max-width: 650px;
            top: -20%;
        }

        .mytype {
            margin-top: 0%;
        }

        .myclose {
            position: relative/absolute;

        }

        .top-right {
            position: absolute;
            top: 10px;
            /* Adjust the top position to your desired distance from the top */
            right: 10px;
            /* Adjust the right position to your desired distance from the right */
        }

        .labeldata {
            color: red;
        }

        .mymodal {
            top: -10%;
        }
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Return Policy</li>
        </ol>
    </nav>

    <div class="accordion row">

        <div class="">
            <div class="accordion accordion-flush" id="accordionFlushExample">
                <div class="accordion-item">

                    <div class="card p-4">
                        <label>Inbox</label>
                        <hr>
                        <button type="button" class="btn btn-primary top-right " data-bs-toggle="modal"
                            data-bs-target="#exampleModales">
                            Add Return Policy
                        </button>


                        <table id="review_table" class="table table-striped dt-responsive nowrap w-100 ">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Type</th>
                                    <th>Subject</th>
                                    <th>Content</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Return Policy </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reply_form">
                        <div>
                            <input type="hidden" id="inputID" name="id" readonly>
                            <label class=""> Category<span class="text-danger">*</span></label>
                            <div class="selDiv">
                                <select name="category" class="form-control" id="category">
                                    <option value="">Select Category </option>
                                    @foreach ($categories as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="">Subject</label>
                            <input type="email" class="form-control" id="inputsubject" name="subject">
                            <label class="">Message</label>
                            <textarea id="inputDescription" name="message" class="form-control summernote"></textarea>
                        </div>
                        <div class="mt-2" style="text-align: right;">

                            <input type="button" class="btn btn-warning mx-auto mydata mytype" value="Update">
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
    <div class="modal fade mymodal" id="exampleModales" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Return Policy </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reply_formes">
                        <div>
                            <label class=""> Category<span class="text-danger">*</span></label>
                            <select name="category" class="form-control" id="customerDropdown">
                                <option value="" selected disabled>Select Category </option>
                                @foreach ($productsWithoutSpecifications as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->category_name }}</option>
                                @endforeach
                            </select>



                            <label class="mt-2 ">Subject <span class="text-danger">*</span></label>
                            <input type="text" id="content" name="subject" class="form-control">

                            <label class="mt-2  ">Message<span class="text-danger">*</span></label>
                            <textarea id="content" name="message" class="form-control summernote">
                                *Return Policy*

Thank you for shopping with [Your E-commerce Store Name]! We strive to ensure your satisfaction with every purchase. If you are not entirely satisfied with your purchase, we're here to help.

*Returns*

You have [number of days, e.g., 30 days] to return an item from the date you received it. To be eligible for a return, your item must be unused and in the same condition that you received it. Your item must be in the original packaging. Your item needs to have the receipt or proof of purchase.

*Refunds*

Once we receive your item, we will inspect it and notify you that we have received your returned item. We will immediately notify you on the status of your refund after inspecting the item. If your return is approved, we will initiate a refund to your original method of payment. You will receive the credit within a certain amount of days, depending on your card issuer's policies.

*Shipping*

You will be responsible for paying for your own shipping costs for returning your item. Shipping costs are non­refundable.

*Contact Us*

If you have any questions on how to return your item to us, contact us at [your contact information].

*Exceptions*

Certain items are exempt from being returned. These include perishable goods, gift cards, downloadable software products, health and personal care items.

*Damaged or Defective Items*

In the rare event that your item arrives damaged or defective, please contact us immediately for assistance. We will work with you to promptly resolve the issue.

*Exchanges*

If you need to exchange an item for a different size or color, please contact us to arrange the exchange. Exchanges are subject to item availability.

*Final Sale Items*

Please note that items marked as "Final Sale" are not eligible for return or exchange unless they arrive damaged or defective.

*Policy Updates*

We reserve the right to update or modify this return policy at any time without prior notice. Any changes will be effective immediately upon posting to this page.

Thank you for shopping with us!

[Your E-commerce Store Name] Team
                            </textarea>

                        </div>
                        <div class="mt-2" style="text-align: right;">
                            <input type="button" class="btn btn-success mx-auto mydatadraft mytype" value="Submit">
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
                        <button type="button" class="btn btn-warning my-2 confirm"
                            data-bs-dismiss="modal">Confirm</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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
                    url: "{{ route('vendor.return_policy_add') }}",
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
                        url: "{{ route('vendor.delete_return_policy') }}",
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
                url: "{{ route('vendor.return_policy_edit') }}",
                method: 'GET',
                data: {
                    disputeId: disputeId
                },
                success: function(response) {
                    var data = response.data;
                    $('#inputID').val(data.id);
                    $('#inputsubject').val(data.subject);
                    $('#category option[value="' + data.category_id + '"]').attr("selected",
                        "selected");
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
                    url: "{{ route('vendor.update_return_policy') }}",
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
                        url: "{{ route('vendor.return_cancellation_data') }}",
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
