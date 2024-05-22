@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style></style>

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
                        <input type="hidden" id="attr-id">
                        <p class="mt-3">Are you sure you want to delete this product?

                        </p>
                        <span style="background-color: yellow">Product variants would also delete that contains these
                            attributes </span>
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger my-2" id="confirm-delete">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Warning Alert Modal -->

    <!-- Add attribute modal  -->
    <div class="modal fade" id="myModal_attribute" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myCenterModalLabel">Add Attribute</h4>
                    <button type="button" id="add_attri_close"  class="close" data-dismiss="modal" aria-hidden="true">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="attribute_form" method="POST" action="#">
                        @csrf
                        <div class="form-control">
                            <label for="attribute_type_id" class="with-help">Attribute type*</label>
                            <select class="form-control select-normal select" id="attribute_type" name="attribute_type">
                                <option selected="selected" value="">Select Attribute type</option>
                                <option value="Color">Color</option>
                                <option value="Radio">Radio</option>
                                <option value="Select">Select</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <div class="row">
                                <div class="col-md-8 nopadding-right">
                                    <label for="name">Attribute name*</label>
                                    <input class="form-control" placeholder="Attribute name" name="name" type="text"
                                        id="name">
                                </div>
                                <div class="col-md-4 nopadding-left">
                                    <label for="order">List order</label>
                                    <input class="form-control" placeholder="List order" min="1" name="order"
                                        type="number" id="order">
                                </div>
                            </div>
                        </div>
                        <div class="form-control">
                            <label for="categories[]">Categories</label>
                            <select class="form-control select2-normal select2" multiple="multiple" id="categories[]"
                                name="categories[]">
                                @foreach ($categoryOptions as $categoryId => $categoryName)
                                    <option value="{{ $categoryId }}">{{ ucwords($categoryName) }}</option>
                                @endforeach
                            </select>
                            <label id="categories[]-error" class="error" for="categories[]"></label>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="save_attribute" value="save"
                                id="">Save</button>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <!-- Add attribute modal  -->

    <!-- edit attribute modal -->
    <div class="modal fade" id="edit_attribute" tabindex="-1" role="dialog" aria-labelledby="edit_attribute"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit Attribute</h4>
                    <button type="button" id="edit_attri_close"  class="close" data-dismiss="modal" aria-hidden="true">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="attribute_edit_form">
                    @csrf
                    <div class="form-control">
                        <label for="attribute_type_id" class="with-help">Attribute type*</label>
                        <select class="form-control select2-normal" required id="attribute_type_id_edit"
                            name="attribute_type_id_edit">
                            <option selected="selected" value="">Select Attribute type</option>
                            <option value="Color">Color</option>
                            <option value="Radio">Radio</option>
                            <option value="Select">Select</option>
                        </select>
                    </div>
                    <div class="form-control">
                        <div class="row">
                            <div class="col-md-8 nopadding-right">
                                <label for="name">Attribute name*</label>
                                <input class="form-control" placeholder="Attribute name" required name="attr_id"
                                    type="hidden" id="attr_id">
                                <input class="form-control" placeholder="Attribute name" required name="name_edit"
                                    type="text" id="name_edit">
                            </div>
                            <div class="col-md-4 nopadding-left">
                                <label for="order">List order</label>
                                <input class="form-control" placeholder="List order" name="order_edit" min="1"
                                    type="number" id="order_edit">
                            </div>
                        </div>
                    </div>
                    <div class="form-control">
                        <label for="categories[]">Categories</label>
                        <select class="form-control select2-normal select2" multiple="multiple" id="categories_edit[]"
                            name="categories_edit[]">
                            @foreach ($categoryOptions as $categoryId => $categoryName)
                                <option value="{{ $categoryId }}">{{ ucwords($categoryName) }}</option>
                            @endforeach
                        </select>
                        <label for="categories_edit[]" id="categories_edit[]-error" class="error"></label>
                    </div>

                    <div class="modal-footer">
                        <input class="btn btn-primary btn-new" id="update_attribute" type="submit" value="Update">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- edit attribute modal -->

    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attribute Lists</li>
            </ol>
        </nav>
        <button type="button" id="attribute_button" class="btn btn-success" data-toggle="modal"
            data-target="#myModal_attribute">
            Add Attribute
        </button>
    </div>
    <hr>
    <div class="card mt-1 p-3">
        <table id="attribute_table" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Categories Name</th>
                    <th>No. of Categories</th>
                    <th>Entities</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection

@section('script')


    <script>
        $("body").on("click", "#add_attri_close", function(event) {
            $('.modal-backdrop.fade').css("opacity", "0");

        })

        $(document).on('click','#edit_attri_close',function(event){
            $("#edit_attribute").modal('hide');
            // $('.modal-backdrop.fade').css("opacity", "0");
        })

        $('#myModal_attribute .select2').select2({
            dropdownParent: $('#myModal_attribute')
        });

        $('#edit_attribute .select2').select2({
            dropdownParent: $('#edit_attribute')
        });
    </script>
    <script>
        $('body').on("click", "#delete_attri", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#attr-id').val(id);
            $("#warning-alert-modal-text").text(name);
            $('#warning-alert-modal').modal('show');
        });


        $('#confirm-delete').on('click', function() {
            var attr_id = $('#attr-id').val();
            // console.log(attr_id);
            let fd = new FormData();
            fd.append('id', attr_id);
            fd.append('_token', '{{ csrf_token() }}');
            $.ajax({
                    url: "{{ route('vendor.attribute.delete') }}",
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
                        setTimeout(function() {
                            window.location.href = result.location;
                        }, 1000);
                        $.fn.tableload();
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
        // $(document).ready(function() {
        //     // $('#myModal_attribute').modal('hide');

        //     $('#attribute_button').on('click', function() {
        //         $('#myModal_attribute').modal('hide');
        //     });
        // });
    </script>
    <script>
        $(document).ready(function() {
            $('#attribute_form').validate({

                rules: {
                    attribute_type: 'required',
                    name: {
                        maxlength: 26,
                        required: true
                    },
                    'categories[]': 'required'

                }
            });



            $(document).on('submit', '#attribute_form', function(e) {
                event.preventDefault();
                var formData = new FormData($('#attribute_form')[0]);
                $.ajax({
                    url: "{{ route('vendor.attributes.store_attribute') }}",
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
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

            });

            // function validateForm() {
            //     var isValid = true;

            //     $('.error-message').remove();

            //     $('#attribute_type, #name, #order, #categories\\[\\]').each(function() {
            //         if ($.trim($(this).val()) === '') {
            //             var errorMessage = 'This field is required';
            //             $(this).addClass('is-invalid');
            //             $(this).after('<span class="error-message" style="color:red;">' + errorMessage +
            //                 '</span>');
            //             isValid = false;
            //         } else {
            //             $(this).removeClass('is-invalid');
            //         }
            //     });

            //     $('#attribute_type, #name, #order, #categories\\[\\]').on('input change', function() {
            //         $(this).removeClass('is-invalid');
            //         $(this).next('.error-message').remove();
            //     });

            //     return isValid;
            // }
        });
    </script>
    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#attribute_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    pageLength: 10,
                    "serverSide": true,
                    "responsive": false,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.attributes.list') }}",
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
                            "name": "name",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 3,
                            "name": "categories",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 3,
                            "name": "categories",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 4,
                            "name": "category",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 5,
                            "name": "count_attr_value",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "20%",
                            "targets": 6,
                            "name": "action",
                            'searchable': true,
                            'orderable': true
                        }
                    ]
                    ,
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
                url: "{{ route('vendor.attribute.show', ['id' => ':id']) }}".replace(':id', id),
                type: 'GET',
                dataType: "JSON",
                success: function(response) {
                    var attribute = response.attribute;
                    var categoryOptions = response.categoryOptions;
                    var selectedCategories = response.selectedCategories;
                    var id = response.attr_id;

                    $('#attribute_type_id_edit').val(attribute.attribute_type);
                    $('#name_edit').val(attribute.attribute_name);
                    $('#order_edit').val(attribute.list_order);
                    $('#attr_id').val(attribute.id);

                    // Select the appropriate categories
                    $('#categories_edit\\[\\]').val(selectedCategories);
                    $('#categories_edit\\[\\]').trigger('change');
                }
            });





            function validateForm_edit() {
                var isValid = true;

                $('.error-message').remove();

                $('#attribute_type_id_edit, #name_edit, #order_edit').each(function() {
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

                $('#attribute_type_id, #name, #order ').on('input change', function() {
                    $(this).removeClass('is-invalid');
                    $(this).next('.error-message').remove();
                });

                return isValid;
            }
        });



        $(document).ready(function() {
            $('#attribute_edit_form').validate({
                rules: {
                    attribute_type: 'required',
                    name_edit: {
                        maxlength: 26,
                        required: true
                    },
                    'categories_edit[]': 'required'

                }
            });
        });



        $(document).on('submit', '#attribute_edit_form', function(e) {
            e.preventDefault();
            let fd = new FormData($('#attribute_edit_form')[0]);
            $.ajax({
                url: "{{ route('vendor.attribute.update') }}",
                type: 'POST',
                data: fd,
                dataType: "JSON",
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#edit_attribute').modal('hide');
                    // Handle success response, e.g., show a success message or update the UI
                    toastr.clear();
                    toastr.success(response.message, 'Success', {
                        class: 'toast-success',
                        timeOut: 3000,
                        closeButton: true
                    });
                    $.fn.tableload();
                }
            });

        });
    </script>
@endsection


@section('script')
@endsection
