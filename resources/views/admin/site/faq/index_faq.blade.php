@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .red {
            color: red !important;
        }

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
                        <p class="mt-3">Are You Sure to Delete this <span id="warning-alert-modal-text">Topic </span>?</p>
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
            <li class="breadcrumb-item active" aria-current="page">Faq</li>
        </ol>
    </nav>
    <hr>
    <!-- ADd FAQ tOPIC -->
    <div id="add_faq_topic" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="topModalLabel">Add Topic</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="add_topic">
                        @csrf
                        <div class="row">
                            <div class="col-md-8 nopadding-right">
                                <div class="form-group">
                                    <label for="name">Topic Name:<span class="red">*</span></label>
                                    <input class="form-control" placeholder="Topic Name" name="name" type="text"
                                        id="name">
                                </div>
                            </div>
                            <div class="col-md-4 nopadding-left">
                                <div class="form-group">
                                    <label for="for">For:<span class="red">*</span></label>
                                    <select class="form-control" id="faq_for" name="faq_for">
                                        <option value="">Select For</option>
                                        <option value="merchant">Merchant</option>
                                        <option value="customer">Customer</option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="status">Status:<span class="red">*</span></label>
                            <select id="topic_status" name="topic_status" class="form-control">
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ADd FAQ tOPIC -->
    <!-- edit FAQ tOPIC -->
    <div id="edit_faq_topic" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="topModalLabel">Edit Topic</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_topic">
                        @csrf
                        <div class="row">
                            <div class="col-md-8 nopadding-right">
                                <div class="form-group">
                                    <label for="name">Topic Name:<span class="red">*</span></label>
                                    <input class="form-control" placeholder="" required="" name="id_edit" type="hidden"
                                        id="id_edit">
                                    <input class="form-control" placeholder="Name" required="" name="topic_name"
                                        type="text" id="topic_name">
                                </div>
                            </div>
                            <div class="col-md-4 nopadding-left">
                                <div class="form-group">
                                    <label for="for">For:<span class="red">*</span></label>
                                    <select class="form-control" id="faq_for_edit" name="faq_for_edit">
                                        <option value="">Select For</option>
                                        <option value="merchant">Merchant</option>
                                        <option value="customer">Customer</option>
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="status">Status:<span class="red">*</span></label>
                            <select id="topic_status_edit" name="topic_status_edit" required class="form-control">
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="update_faq_topic" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>
    <!-- edit FAQ tOPIC -->
    <!-- add faq -->
    <div class="modal fade" id="add_faq" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Add FAQ</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="add_faq_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 nopadding-left">
                                <div class="form-group">
                                    <label for="for">Topic:<span class="red">*</span></label>
                                    <select class="form-control" id="faq_topic" name="faq_topic">
                                        <option value="">Select Topic</option>
                                        @foreach ($topic as $topics)
                                            <option value="{{ $topics->id }}">{{ $topics->topic_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-md-6 nopadding-right">
                                <div class="form-group">
                                    <label for="status">Status:<span class="red">*</span></label>
                                    <select id="faq_status" name="faq_status" required class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div><br>
                        <div>
                            <label for="name">Question:<span class="red">*</span></label>
                            <input class="form-control" placeholder="Question" required="" name="question"
                                type="text" id="question">
                        </div><br>
                        <div>
                            <label for="content">Answer:<span class="red">*</span></label>
                            <textarea id="answer" name="answer" required class="form-control summernote"></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="store_faq" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- add faq -->
    <!-- edit faq -->
    <div class="modal fade" id="edit_faqs_modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Edit FAQ</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body">
                    <form id="edit_faq_form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 nopadding-left">
                                <div class="form-group">
                                    <label for="for">Topic:<span class="red">*</span></label>
                                    <input type="hidden" id="id_answer" name="id_answer">
                                    <select class="form-control" id="faq_topic_edit" name="faq_topic_edit">
                                        <option value="">Select Topic</option>
                                        @foreach ($topic as $topics)
                                            <option value="{{ $topics->id }}">{{ $topics->topic_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                            <div class="col-md-6 nopadding-right">
                                <div class="form-group">
                                    <label for="status">Status:<span class="red">*</span></label>
                                    <select id="faq_status_edit" name="faq_status_edit" required class="form-control">
                                        <option value="">Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div><br>
                        <div>
                            <label for="name">Question:<span class="red">*</span></label>
                            <input class="form-control" placeholder="Question" required="" name="question_edit"
                                type="text" id="question_edit">
                        </div><br>
                        <div>
                            <label for="content">Answer:<span class="red">*</span></label>
                            <textarea id="edit_answer" name="edit_answer" required class="form-control summernote"></textarea>
                            <span id="note-error" style="color: red"></span>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="update_faq" class="btn btn-primary">Update</button>
                </div>
            </div>
        </div>
    </div>
    <!-- edit faq -->

    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>Topics</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_faq_topic">Add
                Topic</button>
        </div>
        <div class="card-body">
            <table id="topic_appereance" class="table table-striped dt-responsive wrap-table w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Topic</th>
                        <th>FAQ For</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5>FAQs</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_faq">Add
                FAQ</button>
        </div>
        <div class="card-body">
            <table id="ques_appereance" class="table table-striped dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Details</th>
                        <th>Topic</th>
                        <th>Last Update</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('script')
    // topics
    <script>
        $(document).ready(function() {
            $('#add_faq_topic .btn-primary').click(function(e) {
                e.preventDefault();
                if (validateForm_edit()) {
                    var formData = $('#add_topic').serialize();

                    $.ajax({
                        url: "{{ route('admin.appereance.store_faq_topic') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                console.log('Form data saved successfully!');
                                $.NotificationApp.send("Success", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "success")
                                setTimeout(function() {
                                    window.location.href = response.location;
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error")
                            }
                        },
                        error: function(xhr) {
                            console.log('Error saving form data: ' + xhr.responseText);
                            $.NotificationApp.send("Error saving form data:", xhr.responseText,
                                "top-right", "rgba(0,0,0,0.2)", "error");

                        }
                    });
                }
            });

            function validateForm_edit() {
                var isValid = true;

                $('.error-message').remove();

                $('#name, #faq_for, #topic_status').each(function() {
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {

                        if ($(this).attr('id') === 'name' && $(this).val().length > 26) {
            var errorMessage = 'Character limit exceeded (max 26 characters)';
            $(this).addClass('is-invalid');
            $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
                    }
                });

                $('#name, #faq_for, #topic_status').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }
        });
    </script>

    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#topic_appereance').dataTable({
                    "scrollX": true,
                    "processing": true,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('admin.appereance.list_faq_topic') }}",
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
                            'orderable': false
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "topic_name",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "faq_for",
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

            $('body').on("click", ".delete_topics", function(e) {
                var id = $(this).data('id');
                var name = $(this).data('name');
                let fd = new FormData();
                fd.append('id', id);
                fd.append('name', name);
                fd.append('_token', '{{ csrf_token() }}');


                $("#warning-alert-modal-text").text('Topic');
                $('#warning-alert-modal').modal('show');
                $('#warning-alert-modal').on('click', '.confirm', function() {
                    $.ajax({
                            url: "{{ route('admin.appereance.delete_topics') }}",
                            type: 'POST',
                            data: fd,
                            dataType: "JSON",
                            contentType: false,
                            processData: false,
                        })
                        .done(function(result) {
                            if (result.status) {
                                $.NotificationApp.send("Success", result.message, "top-right",
                                    "rgba(0,0,0,0.2)", "success");
                                // $.fn.tableload();
                                location.reload(true);
                            } else {
                                $.NotificationApp.send("Error", result.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error");
                                $.fn.tableload();
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
        $('body').on("click", "#edit_topics", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#edit_faq_topic').modal('show');

            $.ajax({
                url: "{{ route('admin.appereance.edit_faq_topic', ['id' => ':id']) }}".replace(':id', id),
                type: 'GET',
                dataType: "JSON",
                success: function(response) {
                    $('#id_edit').val(response.id);
                    $('#topic_name').val(response.topic_name);
                    $('#faq_for_edit').val(response.faq_for).trigger('change');
                    $('#topic_status_edit').val(response.status).trigger('change');
                }
            });


            $('#update_faq_topic').on('click', function() {
                event.preventDefault();

                if (validateForm_edit()) {

                    let fd = new FormData($('#edit_topic')[0]);
                    $.ajax({
                        url: "{{ route('admin.appereance.update_faq_topic') }}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status) {
                                console.log('Form updated saved successfully!');
                                $.NotificationApp.send("Success", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "success")
                                setTimeout(function() {
                                    window.location.href = response.location;
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error")
                            }
                        },
                        error: function(xhr) {
                            console.log('Error saving form data: ' + xhr.responseText);
                            $.NotificationApp.send("Error updating form data:", xhr
                                .responseText, "top-right", "rgba(0,0,0,0.2)", "error");

                        }
                    });
                }
            });


            function validateForm_edit() {
                var isValid = true;

                $('.error-message').remove();

                $('#topic_name, #faq_for_edit, #topic_status_edit').each(function() {
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                $('#topic_name, #faq_for_edit, #topic_status_edit').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });




            // var contentValue = $('.summernote').summernote('code');

            // if ($.trim(contentValue) === '' || contentValue === '<p><br></p>') {
            //     var errorMessage = 'Please enter the content';
            //     // $('#content').addClass('is-invalid');
            //     // $('#content').after('<span class="error-message" style="color:red;">' + errorMessage +
            //     //     '</span>');
            //     $('#note-error').text(errorMessage);
            //     isValid = false;
            //     console.log('yes');
            // } else {
            //     $('#content').removeClass('is-invalid');
            //     $('#note-error').text('');
            //     console.log('no');

            // }

                return isValid;
            }
        });
    </script>
    // end topics

    //faq
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 200
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#store_faq').click(function(e) {
                e.preventDefault();
                if (validateForm_edit()) {

                    var formData = $('#add_faq_form').serialize();
                    var csrfToken = '{{ csrf_token() }}';
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });

                    $.ajax({
                        url: "{{ route('admin.appereance.store_faq') }}",
                        type: 'POST',
                        data: formData,
                        success: function(response) {
                            if (response.status) {
                                console.log('Form data saved successfully!');
                                $.NotificationApp.send("Success", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "success")
                                setTimeout(function() {
                                    window.location.href = response.location;
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error")
                            }
                        },
                        error: function(xhr) {
                            console.log('Error saving form data: ' + xhr.responseText);
                            $.NotificationApp.send("Error saving form data:", xhr.responseText,
                                "top-right", "rgba(0,0,0,0.2)", "error");

                        }
                    });
                }
            });

            function validateForm_edit() {
                var isValid = true;

                $('.error-message').remove();

                $('#faq_topic, #faq_status, #question , #answer').each(function() {
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {
                        if ($(this).attr('id') === 'question' && $(this).val().length > 40) {
                            var errorMessage = 'Character limit exceeded (max 40 characters)';
                            $(this).addClass('is-invalid');
                            $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                                '</span>');
                            isValid = false;
                        } else {
                            $(this).removeClass('is-invalid');
                        }
                    }
                });

                $('#faq_topic, #faq_status, #question , #answer').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }
        });
    </script>
    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#ques_appereance').dataTable({
                    "scrollX": true,
                    "processing": true,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('admin.appereance.list_faq') }}",
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
                            'orderable': false
                        },
                        {
                            "width": "60%",
                            "targets": 1,
                            "name": "details",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "topic",
                            'searchable': true,
                            'orderable': true
                        },

                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "last_updated",
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

            $('body').on("click", ".delete_faq", function(e) {
                var id = $(this).data('id');
                var name = $(this).data('name');
                let fd = new FormData();
                fd.append('id', id);
                fd.append('name', name);
                fd.append('_token', '{{ csrf_token() }}');


                $("#warning-alert-modal-text").text('Faq');
                $('#warning-alert-modal').modal('show');
                $('#warning-alert-modal').on('click', '.confirm', function() {
                    $.ajax({
                            url: "{{ route('admin.appereance.delete_faq') }}",
                            type: 'POST',
                            data: fd,
                            dataType: "JSON",
                            contentType: false,
                            processData: false,
                        })
                        .done(function(result) {
                            if (result.status) {
                                $.NotificationApp.send("Success", result.message, "top-right",
                                    "rgba(0,0,0,0.2)", "success");
                                $.fn.tableload();
                            } else {
                                $.NotificationApp.send("Error", result.message, "top-right",
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
        $('body').on("click", "#edit_faqs", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#edit_faqs_modal').modal('show');

            $.ajax({
                url: "{{ route('admin.appereance.edit_faq', ['id' => ':id']) }}".replace(':id', id),
                type: 'GET',
                dataType: "JSON",
                success: function(response) {
                    $('#id_answer').val(response.id);
                    $('#faq_topic_edit').val(response.faq_topics_id).trigger('change');
                    $('#faq_status_edit').val(response.status).trigger('change');
                    $('#question_edit').val(response.question);
                    var content = response.answer;
                    $('#edit_answer').summernote('code', content);

                }
            });


            $('#update_faq').on('click', function() {
                event.preventDefault();

                if (validateForm_edit()) {

                    let fd = new FormData($('#edit_faq_form')[0]);
                    $.ajax({
                        url: "{{ route('admin.appereance.update_faq') }}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.status) {
                                console.log('Form updated saved successfully!');
                                $.NotificationApp.send("Success", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "success")
                                setTimeout(function() {
                                    window.location.href = response.location;
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", response.message, "top-right",
                                    "rgba(0,0,0,0.2)", "error")
                            }
                        },
                        error: function(xhr) {
                            console.log('Error saving form data: ' + xhr.responseText);
                            $.NotificationApp.send("Error updating form data:", xhr
                                .responseText, "top-right", "rgba(0,0,0,0.2)", "error");

                        }
                    });
                }
            });


            function validateForm_edit() {
                var isValid = true;

                $('.error-message').remove();

                $('#faq_topic_edit, #faq_status_edit, #question_edit, #edit_answer').each(function() {
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                $('#faq_topic_edit, #faq_status_edit, #question_edit, #edit_answer').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }
        });
    </script>
    // end faq
@endsection
