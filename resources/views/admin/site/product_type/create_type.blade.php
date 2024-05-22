@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style>
    .red {
        color: red;
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
                    <p class="mt-3">Are You Sure to Delete this Vendor Application</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{route('admin.producttype.index')}}">Product Type</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Product Type</li>
    </ol>
</nav>
<h2>Add <span class="badge badge-success-lighten">Product Type</span></h2>
<hr>
<form id="add_product_type">
    @csrf
    <div class="row">
        <div class="col-sm-4">
            <label for="type">Type:<span class="red">*</span> </label>
            <select id="type" name="type" class="form-control">
                <option value="">Select Type</option>
                <option value="flash_sale">Flash Sale</option>
                <option value="featured_item">Featured Item</option>
                <option value="deal_of_the_day">Deal Of The Day</option>
                <option value="trending_item">Trending Items</option>
            </select>
        </div>
        <div class="col-sm-4">
            <label for="status">Status:<span class="red">*</span> </label>
            <select id="page_status" name="page_status" class="form-control">
                <option value="">Select Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div><br>

    <table class="table table-default" id="table_type">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Product Name</th>
                <th>Categories</th>
            </tr>
        </thead>
    </table>
    <div class="d-flex">
        <button type="submit" class="btn btn-success mx-auto"> Submit</button>
    </div><br>
</form>

@endsection

@section('script')
<script>
    $(function() {
        $.fn.tableload = function() {
            $('#table_type').dataTable({
                "scrollX": true,
                "processing": true,
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('admin.producttype.list_product_form') }}",
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
                        "name": "image",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "product_name",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "categories",
                        'searchable': true,
                        'orderable': true
                    },
                ]
            });
        }
        $.fn.tableload();

    });
</script>
<script>
    $(document).ready(function() {

        $('#type').change(function() {
            var selectedType = $(this).val();

            if (selectedType === 'deal_of_the_day') {
                $('input[name="product_id[]"]').prop('checked', false);
                $('input[name="product_id[]"]').removeAttr('disabled');
            } else {
                $('input[name="product_id[]"]').removeAttr('disabled');
            }
        });
        $('#add_product_type').submit(function(e) {
            e.preventDefault();

            if (validateForm_edit()) {

                var form = $(this);
                var formData = new FormData(form[0]);

                $.ajax({
                    url: "{{route('admin.producttype.store_product_type')}}",
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        console.log(response);
                        if (response.status) {
                            $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success")
                            setTimeout(function() {
                                window.location.href = response.location;
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", response.message, "top-right", "rgba(0,0,0,0.2)", "error")
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = JSON.parse(xhr.responseText).message;
                        console.log(xhr.responseText);
                        $.NotificationApp.send("Error", errorMessage, "top-right", "rgba(0,0,0,0.2)", "error");

                    }
                });
            }
        });

        function validateForm_edit() {
            var isValid = true;

            $('.error-message').remove();

            $('#type, #page_status,#product_id').each(function() {
                if ($.trim($(this).val()) === '') {
                    var errorMessage = 'This field is required';
                    $(this).addClass('is-invalid');
                    $(this).after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                    isValid = false;
                } else {
                    $(this).removeClass('is-invalid');
                }
            });

            if ($('#type').val() === 'deal_of_the_day') {
                var selectedProducts = $('input[name="product_id[]"]:checked');

                if (selectedProducts.length !== 1) {
                    var errorMessage = 'Please select only one product.';
                    $('#table_type').addClass('is-invalid');
                    $('#table_type').after('<span class="error-message" style="color:red;">' + errorMessage + '</span>');
                    isValid = false;
                }
            }


            $('#type, #page_status,#product_id').on('input change', function() {
                $(this).removeClass('is-invalid');
                $(this).next('.error-message').remove();
            });

            return isValid;
        }
    });
</script>

@endsection