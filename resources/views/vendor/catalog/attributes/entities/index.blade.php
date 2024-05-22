@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .colorpick-eyedropper-input-trigger {
            display: none;
        }

        .div.dataTables_wrapper div.dataTables_length select {
            width: auto !important;
        }


        .dataTables_length {
            display: none;
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
                        <input type="hidden" id="attr-val-id">
                        <p class="mt-3">Are you sure you want to delete ?
                            <span style="background-color: yellow">Product variants would also delete that contains these
                                attribute values </span>
                        </p>
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger my-2" id="confirm-delete">Confirm</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- Warning Alert Modal -->

    <!-- Add attribute modal  -->
    <div class="modal fade" id="myModal_attribute" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Add Attribute Value</h4>
                    <button type="button" id="add_attri_close" class="close" data-dismiss="modal" aria-hidden="true">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <form id="attribute_form_value" method="POST" action="#">
                    @csrf

                    <div class="form-control">
                        <label for="attribute_type_id" class="with-help">Attribute</label>
                        <input type="hidden" name="attr_id" value="{{ $attr_id }}" id="attr_id">
                        {{-- <select class="form-control" required id="attribute_id" name="attribute_id">
                            @foreach ($attributes as $attribute)
                                <option value="{{ $attribute->id }}">{{ $attribute->attribute_name }}</option>
                            @endforeach
                        </select> --}}

                        <input class="form-control" name="attribute" value="{{ $attributes->attribute_name }}"
                            type="text" readonly id="">
                        <input class="form-control" name="attribute_id" value="{{ $attributes->id }}" type="hidden"
                            id="">
                    </div>
                    <div class="form-control">
                        <div class="row mt-2">
                            <div class="col-md-8 nopadding-right">
                                <label for="name">Attribute Value*</label>
                                <input class="form-control" placeholder="Attribute Value" required name="attribute_value"
                                    type="text" id="attribute_value">
                            </div>
                            <div class="col-md-4 nopadding-left">
                                <label for="order">List order*</label>
                                <input class="form-control" placeholder="List order" min="1" name="value_list_order"
                                    type="number" id="value_list_order">
                            </div>
                        </div>

                        @if ($attributes->attribute_type == 'Color')
                            <div class="row mt-2  ">
                                <div class="col-md-12 nopadding-right">
                                    <label for="name">Color Attribute*</label>
                                    <div style="display: flex">
                                        <input class="form-control" placeholder="Color Attribute" name="color_attribute"
                                            type="text" id="color-attribute">
                                        <input type="color" id="color-picker">

                                    </div>
                                </div>

                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <input class="btn btn-primary btn-new" id="save_attribute_value" type="submit" value="Save">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add attribute modal  -->

    <!-- edit attribute modal -->
    <div class="modal fade" id="edit_attribute" tabindex="-1" role="dialog" aria-labelledby="edit_attribute"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit Attribute Value</h4>
                    <button type="button" id="edit_attri_close" aria-hidden="true">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="attribute_edit_form">
                    @csrf
                    <div class="form-control">
                        <label for="attribute_type_id" class="with-help">Attribute</label>
                        <input type="hidden" name="attr_id" value="{{ $attr_id }}" id="attr_id">
                        {{-- <select class="form-control select2-normal" required id="attribute_id_edit"
                            name="attribute_id_edit">
                            <option selected="selected" value="">Select Attribute</option>
                            @foreach ($attributes as $attribute)
                                <option value="{{ $attribute->id }}">{{ $attribute->attribute_name }}</option>
                            @endforeach --}}

                        <input class="form-control" name="attribute" value="{{ $attributes->attribute_name }}" readonly
                            type="text" id="">
                        <input class="form-control" name="attribute_id_edit" value="{{ $attributes->id }}"
                            type="hidden" id="">

                        </select>
                    </div>
                    <div class="form-control">
                        <div class="row">
                            <div class="col-md-8 nopadding-right">
                                <label for="name">Attribute Value*</label>
                                <input class="form-control" placeholder="Attribute Value" required
                                    name="attribute_value_edit" type="text" id="attribute_value_edit">
                            </div>
                            <div class="col-md-4 nopadding-left">
                                <label for="order">List order*</label>
                                <input class="form-control" placeholder="List order" min="1"
                                    name="value_list_order_edit" type="number" id="value_list_order_edit">
                            </div>
                        </div>

                        @if ($attributes->attribute_type == 'Color')
                            <div class="row mt-2  ">
                                <div class="col-md-12 nopadding-right">
                                    <label for="name">Color Attribute*</label>
                                    <div style="display: flex">
                                        <input class="form-control" placeholder="Color Attribute" name="color_attribute"
                                            type="text" id="edit-color-attribute">
                                        <input type="color" id="edit-color-picker" value="">

                                    </div>
                                </div>

                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <input class="btn btn-primary btn-new" id="update_attribute" type="submit" value="Update">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- edit attribute modal -->
    <!-- view attribute modal  -->
    <div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="view_entities"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">View Attribute</h4>
                    <button type="button" id="view_attri_close" aria-hidden="true">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="form-control">
                    <label for="attribute_type_id" class="with-help">Attribute</label>
                    <input type="hidden" name="attr_value_id_view" id="attr_value__id_view">
                    {{-- <select class="form-control select2-normal" required id="attribute_id_view" disabled
                        name="attribute_id_edit">
                        @foreach ($attributes as $attribute)
                            <option value="{{ $attribute->id }}">{{ $attribute->attribute_name }}</option>
                        @endforeach
                    </select> --}}
                    <input class="form-control" name="attribute" value="{{ $attributes->attribute_name }}" readonly
                        type="text" id="">

                </div>
                <div class="form-control">
                    <div class="row">
                        <div class="col-md-8 nopadding-right">
                            <label for="name">Attribute Value*</label>
                            <input class="form-control" placeholder="Attribute name" required disabled
                                name="attribute_value_view" type="text" id="attribute_value_view">
                        </div>
                        <div class="col-md-4 nopadding-left">
                            <label for="order">List order *</label>
                            <input class="form-control" placeholder="List order" disabled name="value_list_order_view"
                                type="number" id="value_list_order_view">
                        </div>
                    </div>

                    @if ($attributes->attribute_type == 'Color')
                        <div class="row mt-2  ">
                            <div class="col-md-12 nopadding-right">
                                <label for="name">Color Attribute*</label>
                                <div style="display: flex">
                                    <input class="form-control" readonly placeholder="Color Attribute"
                                        name="color_attribute" type="text" id="view-color-attribute">
                                    <input type="color" id="view-color-picker">

                                </div>
                            </div>

                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- view attribute modal  -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.attributes.index') }}">Attribute</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Attribute Value</li>
        </ol>
    </nav>
    <div style="display: flex; justify-content: space-between; align-items: flex-end;" class="form-control">

        <h3 class="box-title">Attribute: {{ $attributes->attribute_name }} | Type: {{ $attributes->attribute_type }}
        </h3>

        <div class="box-tools pull-right">
            <button type="button" id="attribute_button" class="btn btn-primary" data-toggle="modal"
                data-target="#myModal_attribute">
                Add Attribute Value
            </button>
        </div>
    </div>
    <div class="card mt-1 p-3">
        <table id="attribute_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Values</th>
                    <th>Color</th>
                    <th>Pattern</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $("body").on("click", "#add_attri_close", function(event) {
            $('#myModal_attribute').modal('hide');

        })

        $(document).on('click', '#edit_attri_close', function(event) {
            $("#edit_attribute").modal("hide");
        })

        $(document).on('click', '#view_attri_close', function(event) {
            $("#view_entities").modal("hide");

        })


        $(document).on('change', '#color-picker', function() {
            $('#color-attribute').val($(this).val())
        });

        $(document).on('change', '#edit-color-picker', function() {
            $('#edit-color-attribute').val($(this).val())
        });
    </script>


    <script>
        $('body').on("click", "#delete_attri_value", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#attr-val-id').val(id);
            $("#warning-alert-modal-text").text(name);
            $('#warning-alert-modal').modal('show');

        });


        $('#confirm-delete').on('click', function() {
            var attr_id = $('#attr-val-id').val();
            let fd = new FormData();
            fd.append('id', attr_id);
            fd.append('_token', '{{ csrf_token() }}');

            $.ajax({
                    url: "{{ route('vendor.attribute.delete.entities') }}",
                    type: 'POST',
                    data: fd,
                    dataType: "JSON",
                    contentType: false,
                    processData: false,
                })
                .done(function(result) {
                    console.log(result);
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "success");
                        $.fn.tableload();
                        location.reload();

                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)",
                            "error");
                    }
                })
                .fail(function(jqXHR, exception) {
                    console.log(jqXHR.responseText);
                });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#myModal_attribute').modal('hide');

            $('#attribute_button').on('click', function() {
                $('#myModal_attribute').modal('show');
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#save_attribute_value').click(function() {
                event.preventDefault();

                if (validateForm()) {
                    var formData = new FormData($('#attribute_form_value')[0]);
                    $.ajax({
                        url: "{{ route('vendor.attributes.entities.store_attr_value') }}",
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            // console.log(response);
                            toastr.clear();
                            toastr.success(response.message, 'Success', {
                                class: 'toast-success',
                                timeOut: 3000,
                                closeButton: true
                            });
                            window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.log(xhr.responseText);
                            toastr.clear();
                            toastr.error(error, 'Error', {
                                timeOut: 3000,
                                closeButton: true
                            });
                        }
                    });
                }
            });

            function validateForm() {
                var isValid = true;

                $('.error-message').remove();

                $('#attribute_id, #attribute_value, #value_list_order').each(function() {
                    var $input = $(this);
                    var maxCharLimit = 30;
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else if ($input.val().length > maxCharLimit) {
                        var errorMessage = 'Maximum ' + maxCharLimit + ' characters allowed';
                        $input.addClass('is-invalid');
                        $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else if ($input.attr('id') === 'value_list_order' && parseFloat($input.val()) < 0) {
                        var errorMessage = 'Value cannot be negative';
                        $input.addClass('is-invalid');
                        $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                $('#attribute_id, #attribute_value, #value_list_order').on('input change', function() {
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
                var id = $('#attr_id').val();
                $('#attribute_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    "pageLength": 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.attributes.list_entities', ['id' => ':id']) }}".replace(
                            ':id', id),
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
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "order",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "values",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 3,
                            "name": "color",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 4,
                            "name": "pattern",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 5,
                            "name": "action",
                            'searchable': true,
                            'orderable': true
                        }
                    ],
                    "drawCallback": function(settings) {
                // Initialize tooltips after each table redraw
                $('[data-toggle="tooltip"]').tooltip();
            }
                });
            };

            $.fn.tableload();

        });


        $('body').on("click", "#edit_attribute_button", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#edit_attribute').modal('show');

            $.ajax({
                url: "{{ route('vendor.attribute.show_entity', ['id' => ':id']) }}".replace(':id', id),
                type: 'GET',
                dataType: "JSON",
                success: function(response) {
                    $('#attribute_id_edit').val(response.attribute_id).trigger('change');
                    $('#attribute_value_edit').val(response.attribute_value);
                    $('#value_list_order_edit').val(response.value_list_order);
                    $('#edit-color-attribute').val(response.color_attribute);
                    $('#edit-color-picker').val(response.color_attribute);
                }
            });


            $('#update_attribute').on('click', function() {
                event.preventDefault();

                if (validateForm_edit()) {

                    let fd = new FormData($('#attribute_edit_form')[0]);
                    $.ajax({
                        url: "{{ route('vendor.attribute.update_entities', ['id' => ':id']) }}"
                            .replace(':id', id),
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            $('#edit_attribute').modal('hide');
                            // Handle success response, e.g., show a success message or update the UI
                            // toastr.clear();
                            toastr.success(response.message, 'Success', {
                                class: 'toast-success',
                                timeOut: 2000,
                                closeButton: true
                            });
                            // $.fn.tableload();
                            location.reload();
                        }
                    });
                }
            });


            function validateForm_edit() {
                var isValid = true;

                $('.error-message').remove();

                $('#attribute_id_edit, #attribute_value_edit, #value_list_order_edit').each(function() {
                    var $input = $(this);
                    var maxCharLimit = 30;
                    if ($.trim($(this).val()) === '') {
                        var errorMessage = 'This field is required';
                        $(this).addClass('is-invalid');
                        $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else if ($input.val().length > maxCharLimit) {
                        var errorMessage = 'Maximum ' + maxCharLimit + ' characters allowed';
                        $input.addClass('is-invalid');
                        $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else if ($input.attr('id') === 'value_list_order_edit' && parseFloat($input.val()) < 0) {
                        var errorMessage = 'Value cannot be negative';
                        $input.addClass('is-invalid');
                        $input.after('<span class="error-message" style="color:red;">' + errorMessage +
                            '</span>');
                        isValid = false;
                    } else {
                        $(this).removeClass('is-invalid');
                    }
                });

                $('#attribute_id_edit, #attribute_value_edit, #value_list_order_edit').on('input change',
                    function() {
                        $(this).removeClass('is-invalid');
                        $(this).next('.error-message').remove();
                    });

                return isValid;
            }
        });
    </script>
    <script>
        $('body').on("click", "#view_entities_button", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#view_entities').modal('show');

            $.ajax({
                url: "{{ route('vendor.attribute.view_entity', ['id' => ':id']) }}".replace(':id', id),
                type: 'GET',
                dataType: "JSON",
                success: function(response) {
                    console.log(response.id);
                    $('#attr_value__id_view').val(response.id);
                    $('#attribute_id_view').val(response.attribute_id).trigger('change');
                    $('#attribute_value_view').val(response.attribute_value);
                    $('#value_list_order_view').val(response.value_list_order);
                    $('#view-color-attribute').val(response.color_attribute);
                    $('#view-color-picker').val(response.color_attribute);

                }
            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('select').selectpicker();
        });
    </script>
@endsection


@section('script')
@endsection
