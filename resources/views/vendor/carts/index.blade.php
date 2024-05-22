@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection

@section('main_content')
    <!-- Warning Alert Modal -->
    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Warning</h4>
                        <p class="mt-3">Are you sure you want to delete ?</p>
                        <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    {{-- <div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Wishlist Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div> --}}
    <div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Cart Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                    <!-- Your modal content here -->
                </div>
            </div>
        </div>
    </div>


    <!--  -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Carts</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <!-- <div class="d-flex justify-content-end mb-2"> -->
        <!-- <button class="btn btn-success" data-toggle="modal" id="add_carts">Add Carts</button>
        <input id="search_customer" class="form-control" placeholder="Search Customer" name="search_customer" type="text">
        <ul id="search-results"></ul> -->
        <!-- </div> -->
        <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Created at</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Quantities</th>
                    <th>Grand Total</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
@endsection


@section('script')

    <script>
        $(function() {

            $('[data-toggle="tooltip"]').tooltip();
            $.fn.tableload = function() {
                $('#rate_table').dataTable({
                    "scrollX": true,
                    "processing": true,
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.carts.list') }}",
                        "type": "POST",
                        "data": function(d) {
                            d._token = "{{ csrf_token() }}";
                        },
                        dataFilter: function(data) {
                            var json = jQuery.parseJSON(data);
                            json.recordsTotal = json.recordsTotal;
                            json.recordsFiltered = json.recordsFiltered;
                            json.data = json.data;
                            $('[data-toggle="tooltip"]').tooltip();
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
                            "name": "name",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "last_on",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "quantity",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "quantity",
                            'searchable': true,
                            'orderable': true
                        },
                        {
                            "width": "10%",
                            "targets": 2,
                            "name": "quantity",
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
                    ],
                    "drawCallback": function(settings) {
                // Initialize tooltips after each table redraw
                $('[data-toggle="tooltip"]').tooltip();
            }
                });
            };

            $.fn.tableload();

            $('body').on("click", ".deleteTypes", function(e) {
                var id = $(this).data('id');
                let fd = new FormData();
                fd.append('id', id);
                fd.append('_token', '{{ csrf_token() }}');

                $('#warning-alert-modal').modal('show');
            $('#warning-alert-modal').on('click', '.confirm', function() {
                $.ajax({
                        url: "{{ route('vendor.cart.delete') }}",
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
                                    window.location.reload();
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
        });
    </script>
    <script>
        $('body').on("click", "#view_wishlist", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#view_entities').modal('show');

            $.ajax({
                url: "{{ route('vendor.carts.view', ['id' => ':id']) }}".replace(':id', id),
                type: 'GET',
                dataType: "JSON",
                success: function(response) {
                    console.log(response);
                    var wishlistData = '';
                    wishlistData += '<h5>Customer : ' + response.customer.name + '</h5>';
                wishlistData += '<h5>Email : ' + response.customer.email + '</h5>';
                wishlistData += '<h5>Member Since : ' + response.customer.member_since + '</h5> <hr>';
   
                if (response.cart_details.length > 0 ) {
    wishlistData += '<h5>Products:</h5>';
    wishlistData += '<table class="table">';
    wishlistData += '<thead><tr><th>Product Name</th><th>Image</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead>';
    wishlistData += '<tbody>';

    response.cart_details.forEach(function(cart) {
        wishlistData += '<tr>';
        wishlistData += '<td>' + cart.name + '</td>';
        wishlistData += '<td><img src="{{asset("public/vendor/featured_image")}}/' + cart.product.featured_image + '" alt="Product Image" width="50px;"></td>';
        wishlistData += '<td>' + cart.quantity + '</td>';
        wishlistData += '<td>' + cart.price + '</td>';
        wishlistData += '<td>' + cart.base_total + '</td>';
        wishlistData += '</tr>';
    });

    wishlistData += '</tbody>';
    wishlistData += '</table>';

                        $('#view_entities .modal-body').html(wishlistData);
                    }


                    function formatDate(dateString) {
                        var date = new Date(dateString);
                        var day = date.getDate();
                        var month = date.getMonth() + 1;
                        var year = date.getFullYear();

                        day = day < 10 ? '0' + day : day;
                        month = month < 10 ? '0' + month : month;

                        return day + '/' + month + '/' + year;
                    }
                },
            });
        });


    </script>
@endsection
