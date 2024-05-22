@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
@endsection


@section('css')
    <style>
        .edit_select_attri .select2-selection__choice__remove {
            display: none !important;
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
                        <p class="mt-3">Are You Sure You Want to Delete this Inventory </p>
                        <button type="button" class="btn btn-warning my-2 delete" data-bs-dismiss="modal">Confirm</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal" >Cancel</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- add with variant modal -->
    <div id="inventory_with_variant" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-body">
                    <div class="text-center mt-2 mb-4">
                        <a href="" class="text-success">
                            <span>Add Variant<img src="assets/images/logo-dark.png" alt="" height="18"></span>
                        </a>
                    </div>
                    <form class="ps-3 pe-3" id="set_variant_form">
                        @csrf
                        <div id="add_to_modal" class="mb-3">
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" id="set_variant_button" class="btn btn-primary"
                                style="float: right;margin-top: 20px;">Set Variant</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- add with variant modal -->


    {{-- edit Variant with attribute  --}}
    <div id="edit_inventory_with_variant" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="
                justify-content: end;
                border-bottom: unset;
                margin-bottom: -13px;
            ">
                  
                    <button type="button" class="close" data-dismiss="modal" id="edit-btn" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
                <div class="modal-body">
                    <div class="text-center mt-2 mb-2">
                        <a href="" class="text-success">
                            <span>Edit Variant<img src="assets/images/logo-dark.png" alt="" height="18"></span>
                        </a>
                    </div>
                    <form class="ps-3 pe-3" id="update_variant_form">
                        @csrf
                        <div id="edit_to_modal" class="mb-3">
                        </div>
                        <div class="col-sm-3">
                            <button type="submit" id="update_variant_button" class="btn btn-primary"
                                style="float: right;margin-top: 20px;">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>





    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Inventory</li>
        </ol>
    </nav>
    <div class="card mt-1 p-3">
        <button class="btn btn-success " data-toggle="modal" id="inventory" style="float:right;">Add Inventory</button>
        <input id="searchProduct" class="form-control" placeholder="Search a product by it's GTIN, Name or Model Number"
            name="searchProduct" type="text">
        <ul id="search-results"></ul>
    </div>

    <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
        <li class="nav-item">
            <a href="#home1" data-bs-toggle="tab" aria-expanded="true" class="nav-link rounded-0 active">
                <i class="mdi mdi-home-variant d-md-none d-block"></i>
                <span class="d-none d-md-block">Inventory without Variant</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="#profile1" data-bs-toggle="tab" aria-expanded="false" class="nav-link rounded-0 ">
                <i class="mdi mdi-account-circle d-md-none d-block"></i>
                <span class="d-none d-md-block">Inventory with Variant</span>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="card mt-1 p-3 tab-pane show active" id="home1">
            <table id="inventory_table" class="table table-striped dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>Sku</th>
                        <th>Price</th>
                        <th>Stock Qty</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="card mt-1 p-3 tab-pane" id="profile1">
            <table id="inventory_table_variant" class="table table-striped dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product Name</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
@section('script')
  @if (Session::has('msg'))
        <script>
            $(document).ready(function() {
                toastr.error('{{ Session::get('msg') }}');    
            });
        </script>
    @endif

    <script>
        $('.select2').select2({
            dropdownParent: $('#inventory_with_variant')
        });

        $(document).on('click','#edit-btn',function(){
            $("#edit_inventory_with_variant").modal('toggle');
        })
    </script>
    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#inventory_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.products.list_inventory') }}",
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
                            "targets": 2,
                            "name": "featured_image",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "sku",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "price",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 1,
                            "name": "offer_price",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "stock_qty",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 3,
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

        // delete products
        $('body').on("click", "#delete_inventory", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('sku');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');

            $("#warning-alert-modal-text").text(name);

            $('#warning-alert-modal').modal('show');

            $('#warning-alert-modal').on('click', '.delete', function() {
                $.ajax({
                        url: "{{ route('vendor.inventory.list.delete') }}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            toastr.clear();
                            toastr.success(result.msg, 'Success', {
                                class: 'toast-success',
                                timeOut: 2000,
                                closeButton: true
                            });
                            // $.NotificationApp.sen    d("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                            setTimeout(function() {
                                window.location.href = result.location;
                            }, 1000);
                        } else {
                            toastr.clear();
                            toastr.error(error, 'Error', {
                                timeOut: 2000,
                                closeButton: true
                            });
                            // $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            });
        });
    </script>
    <script>
        $(function() {
            $.fn.tableload = function() {
                $('#inventory_table_variant').dataTable({
                    "scrollX": true,
                    "processing": true,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.inventory.list_inventory_with_variant') }}",
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
                            "targets": 2,
                            "name": "featured_image",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "title",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "sku",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "price",
                            'searchable': true,
                            'orderable': true
                        },
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

        // delete products
        $('body').on("click", "#deleteinventory_variant", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('sku');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');

            $("#warning-alert-modal-text").text(name);

            $('#warning-alert-modal').modal('show');

            $('#warning-alert-modal').on('click', '.delete', function() {
                $.ajax({
                        url: "{{ route('vendor.inventory.list_variant.delete') }}",
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
        $(document).ready(function() {
            $('#searchProduct').on('input', function() {
                var query = $(this).val();
                if (query.length >= 2) {
                    $.ajax({
                        url: '{{ route('vendor.inventory.search_inventory') }}',
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function(response) {

                            console.log(response);
                            var results = response;
                            var $searchResults = $('#search-results');
                            $searchResults.empty();

                            if (results.length > 0) {
                                $.each(results, function(index, result) {
                                    var variantButton = result.has_variant == 1 ?
                                        `<a href="javascript:void(0)" class="px-2 btn btn-danger add_with_variant " data-id="${result.id}" data-name="${result.name}"  ${result.already_added ? 'disabled' : '' }  title=" ${result.already_added ? 'Varaint already added' : '' }">Add to inventory with variant</a>` :
                                        `<a href="{{ url('vendor/stocks/inventory/add/') }}/${result.id}" class="px-2 btn bg-primary" style="color:white;">Add to inventory</a>`;

                                    var html =
                                        `<div class="admin-user-widget">
                                                <span class="admin-user-widget-img">
                                                    <img src='{{ asset('public/vendor/featured_image/' . '` + result.featured_image + `') }}' width="100" alt="` +
                                        result.featured_image + `">
                                                </span>
                                                <div class="row">
                                                    <div class="admin-user-widget-content col-sm-6">
                                                    <div class="row">
                                                        <span class="admin-user-widget-title">
                                                        Product Name : ` + result.name + `
                                                        </span>
                                                    </div>
                                                    <div class="row">
                                                        <span class="admin-user-widget-text text-muted">
                                                        Model number:  ` + result.model_number + `
                                                        </span>
                                                    </div>
                                                    <div class="row">
                                                        <span class="admin-user-widget-text text-muted">
                                                        Brand: ` + result.brand + `
                                                        </span>
                                                    </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="option-btn" style=" margin-top: -50px;">
                                                            ${variantButton}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>`;
                                    $searchResults.append(html);
                                    html = html.replace('REPLACE_ID', result.id);
                                    // <a href="javascript:void(0)" class="px-2 btn btn-danger btn-sm view_product_inventory" data-id="` + result.id + `" data-name="` + result.name + `" >View details</a>
                                });
                            } else {
                                var listItem = $('<li>').text('No results found.');
                                $searchResults.append(listItem);
                            }
                        }
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#searchProduct").hide();
            $("#inventory").click(function() {
                $("#searchProduct").toggle(1000);
            });
        });
    </script>
    <script>
        $('body').on("click", ".add_with_variant", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $.ajax({
                url: "{{ route('vendor.inventory.get_modal_data') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    name: name
                },
                success: function(result) {


                     console.log(result);
                   if(result.status == false){
                        $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                   }
                   else {
                    // console.log(result);
                    $("#inventory_with_variant").modal('toggle');
                    var html = "";

                    var attributes = {};
                    var productIds = [];

                    result.forEach(function(attribute) {
                        var attribute_id = attribute.attribute_id;
                        if (!attributes.hasOwnProperty(attribute_id)) {
                            attributes[attribute_id] = {
                                attribute_name: attribute.attribute_name,
                                attribute_values: []
                            };
                        }
                        attributes[attribute_id].attribute_values.push({
                            attribute_value_id: attribute.attribute_value_id,
                            attribute_value: attribute.attribute_value
                        });
                        if (!productIds.includes(attribute.product_id)) {
                            productIds.push(attribute.product_id);
                        }
                    });

                    for (var attribute_id in attributes) {
                        var attribute = attributes[attribute_id];
                        html += '<div class="d-flex row">' +
                            '<div class="col-sm-2"><input type="hidden" name="product_id" id="product_id" value="' +
                            productIds + '"><br>' +
                            '<label for="attribute_' + attribute_id + '" class="form-label">' +
                            attribute.attribute_name + '</label>:  </div><br><br>' +
                            '<div class="col-sm-10"><select class="select2 form-control main select2 bytelogic" required id="attribute_' +
                            attribute_id +
                            '" data-toggle="select2" multiple data-placeholder="Choose ..."><br>';

                        console.log(attributes);
                        attribute.attribute_values.forEach(function(attribute_value) {
                            html += '<option value="' + attribute_value.attribute_value_id +
                                '">' + attribute_value.attribute_value + '</option><br>';
                        });

                        html += '</select><br></div></div>';
                    }

                    $('#add_to_modal').html(html);
                    $('.bytelogic').select2({
                        dropdownParent: $('#add_to_modal')
                    });

                }

                    $('#set_variant_form').on('submit', function(event) {
                        event.preventDefault();
                        var productID = $('#product_id').val();
                        var selectedValues = {};
                        for (var attribute_id in attributes) {
                            var selectBox = $('#attribute_' + attribute_id);
                            var selectedOptions = selectBox.val();
                            selectedValues[attribute_id] = selectedOptions;
                        }

                        var permutations = generatePermutations(selectedValues);
                        var combinations = generateCombinations(selectedValues);

                        console.log("Permutations:", permutations);
                        console.log("Combinations:", combinations);

                        $.ajax({
                            url: "{{ route('vendor.inventory.get_variant_inventory') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                selectedValues: selectedValues,
                                permutations: permutations,
                                combinations: combinations,
                                productID: productID,
                            },
                            success: function(response) {
                                window.location.href =
                                    "{{ route('vendor.inventory.get_variant_file') }}";
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    });

                    function generatePermutations(selectedValues) {
                        var values = Object.values(selectedValues);
                        var permutations = [];

                        function permuteHelper(currentPerm, remainingValues) {
                            if (remainingValues.length === 0) {
                                permutations.push(currentPerm);
                                return;
                            }

                            for (var i = 0; i < remainingValues[0].length; i++) {
                                var newPerm = currentPerm.concat(remainingValues[0][i]);
                                permuteHelper(newPerm, remainingValues.slice(1));
                            }
                        }

                        permuteHelper([], values);
                        return permutations;

                    }

                    function generateCombinations(selectedValues) {
                        var values = Object.values(selectedValues);
                        var combinations = [];

                        function combineHelper(currentComb, remainingValues) {
                            if (remainingValues.length === 0) {
                                combinations.push(currentComb);
                                return;
                            }

                            for (var i = 0; i < remainingValues[0].length; i++) {
                                var newComb = currentComb.concat(remainingValues[0][i]);
                                combineHelper(newComb, remainingValues.slice(1));
                            }
                        }

                        combineHelper([], values);
                        return combinations;
                    }
                }
            });

        });
    </script>


    {{-- Edit Varints  --}}
    <script>
        $('body').on("click", ".edit_variants", function(e) {
            var id = $(this).data('id');
            var inventory_id = $(this).data('invt');
            $.ajax({
                url: "{{ route('vendor.inventory.get_attributes') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    inventory_id: inventory_id
                },
                success: function(result) {
                    console.log(result.attribute_value_id);

                    if(result.status == false){
                        $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                         return exit();
                   }

                    // if($.inArray(String(9), result.attribute_value_id) !== -1 ){
                    //     console.log("hello");
                    // }
                    // else{
                    //     console.log($.inArray("9",result.attribute_value_id) !== -1);
                    // }
                    $("#edit_inventory_with_variant").modal('toggle');
                    var html = "";

                    var attributes = {};
                    var productIds = [];

                    result.data.forEach(function(attribute) {
                        var attribute_id = attribute.attribute_id;
                        if (!attributes.hasOwnProperty(attribute_id)) {
                            attributes[attribute_id] = {
                                attribute_name: attribute.attribute_name,
                                attribute_values: []
                            };
                        }
                        attributes[attribute_id].attribute_values.push({
                            attribute_value_id: attribute.attribute_value_id,
                            attribute_value: attribute.attribute_value
                        });
                        if (!productIds.includes(attribute.product_id)) {
                            productIds.push(attribute.product_id);
                        }
                    });

                    for (var attribute_id in attributes) {
                        var attribute = attributes[attribute_id];
                        html += '<div class="d-flex row  edit_select_attri">' +
                            '<div class="col-sm-2"><input type="hidden" name="product_id" id="product_edit_id" value="' +
                            productIds +
                            '"><br>   <input type="hidden" name="variant_id" id="variant-id" value="' +
                            result.invt_id + '">  <br>   ' +

                            '<label for="attribute_' + attribute_id + '" class="form-label">' +
                            attribute.attribute_name + '</label>:  </div><br><br>' +
                            '<div class="col-sm-10 mt-3"><select class="select2 form-control main select2 bytelogical" required id="attributes_' +
                            attribute_id +
                            '" data-toggle="select2" multiple data-placeholder="Choose ..."><br>';

                        // console.log(attributes);
                        attribute.attribute_values.forEach(function(attribute_value) {
                            html +=
                                `<option value="${attribute_value.attribute_value_id}" ${$.inArray(String(attribute_value.attribute_value_id), result.attribute_value_id) !== -1 ? 'selected  disabled' : ''}   >${attribute_value.attribute_value}</option> <br>`;
                        });

                        html += '</select><br></div></div>';

                    }

                    $('#edit_to_modal').html(html);
                    $('.bytelogical').select2({
                        dropdownParent: $('#edit_to_modal')
                    });

                    $('#update_variant_form').on('submit', function(event) {
                        event.preventDefault();
                        var productID = $('#product_edit_id').val();
                        var inventory_variantId = $('#variant-id').val();
                        var selectedValues = {};

                        for (var attribute_id in attributes) {

                            var selectBox = $('#attributes_' + attribute_id);


                            const dropdown = document.getElementById("attributes_"+attribute_id);

                            const selectedDisabledOptions = Array.from(dropdown.options).filter(
                                option => option.selected && option.disabled);
                            var disabledValues = selectedDisabledOptions.map(option => option
                                .value);

                                // console.log(disabledValues);
                            var selectedOptions = selectBox.val();
                             var array_merge = selectedOptions.concat(disabledValues);
                            selectedValues[attribute_id] = array_merge;
                        }

                        var permutations = generatePermutations(selectedValues);
                        var combinations = generateCombinations(selectedValues);

                        //  console.log(attribute_id);

                        console.log("Permutations:", permutations);
                        console.log("Combinations:", combinations);


                        $.ajax({
                            url: "{{ route('vendor.inventory.get_edit_inventory') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                selectedValues: selectedValues,
                                permutations: permutations,
                                combinations: combinations,
                                productID: productID,
                                inventory_variantId : inventory_variantId
                            },
                            success: function(response) {
                                window.location.href = "{{ route('vendor.inventory.list_variant_edit') }}";
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    });

                    function generatePermutations(selectedValues) {
                        var values = Object.values(selectedValues);
                        var permutations = [];

                        function permuteHelper(currentPerm, remainingValues) {
                            if (remainingValues.length === 0) {
                                permutations.push(currentPerm);
                                return;
                            }

                            for (var i = 0; i < remainingValues[0].length; i++) {
                                var newPerm = currentPerm.concat(remainingValues[0][i]);
                                permuteHelper(newPerm, remainingValues.slice(1));
                            }
                        }

                        permuteHelper([], values);
                        return permutations;

                    }

                    function generateCombinations(selectedValues) {
                        var values = Object.values(selectedValues);
                        var combinations = [];

                        function combineHelper(currentComb, remainingValues) {
                            if (remainingValues.length === 0) {
                                combinations.push(currentComb);
                                return;
                            }

                            for (var i = 0; i < remainingValues[0].length; i++) {
                                var newComb = currentComb.concat(remainingValues[0][i]);
                                combineHelper(newComb, remainingValues.slice(1));
                            }
                        }

                        combineHelper([], values);
                        return combinations;
                    }
                }
            });

        });
    </script>




    <script>
        $('body').on("click", ".view_product_inventory", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('sku');

            $.ajax({
                url: "{{ route('vendor.inventory.view_product_modal') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id,
                    name: name
                },
                success: function(result) {
                    console.log(result);
                    $("#inventory_with_variant").modal('toggle');
                }
            });

        });
    </script>
@endsection
