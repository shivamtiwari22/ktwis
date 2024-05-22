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
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--  -->
    <!-- <div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="view_entities" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">View Wishlist</h4>
                    </div>
                    <div class="form-control">
                        <label for="attribute_type_id" class="with-help">Wishlist</label>
                        <input type="text" name="user_id" id="user_id">
                        <input type="text" name="user_name" id="user_name">
                        <input type="text" name="user_email" id="user_email">
                        <input type="text" name="created_at" id="created_at">
                        <input type="text" name="product_id" id="product_id">
                        <select class="form-control select2-normal" required id="attribute_id_view" disabled name="attribute_id_edit">
                           
                        </select>
                    </div>
                    <div class="form-control">
                        <div class="row">
                            <div class="col-md-8 nopadding-right">
                                <label for="name">Attribute Value*</label>
                                <input class="form-control" placeholder="Attribute name" required disabled name="attribute_value_view" type="text" id="attribute_value_view">
                            </div>
                            <div class="col-md-4 nopadding-left">
                                <label for="order">List order</label>
                                <input class="form-control" placeholder="Viewing order" disabled name="value_list_order_view" type="number" id="value_list_order_view">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    <!-- Modal -->
    <!-- <div id="view_entities" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Wishlist Details</h5>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div> -->
    <div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Wishlist Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                {{-- <div class="modal-body"> --}}
                    <div class="modal-body" style="max-height: 400px; overflow-y: auto;">


                </div>
            </div>
        </div>
    </div>

    <!--  -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Wishlist List</li>
        </ol>
    </nav>
    <div class="card p-4 mt-4">
        <!-- <div class="d-flex justify-content-end mb-2">
                <a href="{{ route('vendor.shipping.rates.create') }}" class="btn btn-success">Add Rate</a>
            </div> -->
        <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Customer</th>
                    <th>Last Wishlisted On</th>
                    <th>Quantity</th>
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
                    pageLength: 10,
                    "serverSide": true,
                    "bDestroy": true,
                    'checkboxes': {
                        'selectRow': true
                    },
                    "ajax": {
                        url: "{{ route('vendor.wishlist.list') }}",
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

            // $('body').on("click", ".delete_rate", function(e) {
            //     var id = $(this).data('id');
            //     var name = $(this).data('name');

            //     let fd = new FormData();
            //     fd.append('id', id);
            //     fd.append('_token', '{{ csrf_token() }}');
            //     $("#warning-alert-modal-text").text(name);
            //     $('#warning-alert-modal').modal('show');
            //     $('#warning-alert-modal').on('click', '.btn', function() {
            //         $.ajax({
            //                 url: "{{ route('vendor.shipping.rates.delete') }}",
            //                 type: 'POST',
            //                 data: fd,
            //                 dataType: "JSON",
            //                 contentType: false,
            //                 processData: false,
            //             })
            //             .done(function(result) {
            //                 if (result.status) {
            //                     $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
            //                     setTimeout(function() {
            //                         window.location.href = result.location;
            //                     }, 1000);
            //                 } else {
            //                     $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
            //                 }
            //             })
            //             .fail(function(jqXHR, exception) {
            //                 console.log(jqXHR.responseText);
            //             });
            //     });
            // });
        });
    </script>
    <script>
        $('body').on("click", "#view_wishlist", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');

            $('#view_entities').modal('show');

            $.ajax({
                url: "{{ route('vendor.wishlist.view', ['id' => ':id']) }}".replace(':id', id),
                type: 'GET',
                dataType: "JSON",
                success: function(response) {
                    console.log(response);
                    var wishlistData = '';
                    wishlistData += '<h5> <strong>Customer </strong> : ' + response[0].name + '</h5>';
                    wishlistData += '<h5> <strong>Email </strong> : ' + response[0].email + '</h5>';
                    wishlistData += '<h5><strong>  Member Since </strong>  : ' + formatDate(response[0].created_at) +
                        '</h5> <hr>';

                    if (response.length > 0 && response[0].wishlists) {
                        wishlistData += '<h5>Products:</h5>';
                        wishlistData += '<table class="table">';
                        wishlistData +=
                            '<thead><tr><th>Product Name</th><th>Image</th><th>Price</th></tr></thead>';
                        wishlistData += '<tbody>';

                        response[0].wishlists.forEach(function(wishlist) {
                            wishlistData += '<tr>';
                            wishlistData += '<td>' + wishlist.product.name + '</td>';
                            wishlistData +=
                                '<td><img src="{{ asset('public/vendor/featured_image') }}/' +
                                wishlist.product.featured_image +
                                '" alt="Product Image" width="50px;"></td>';

                            if (wishlist.product.inventory) {
                                wishlistData += '<td>' + wishlist.product.inventory
                                    .offer_price + '</td>';
                            }
                            if (wishlist.variant) {
                                wishlistData += '<td>' + wishlist.variant.offer_price + '</td>';
                            }

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

                        // Pad day and month with leading zeros if needed
                        day = day < 10 ? '0' + day : day;
                        month = month < 10 ? '0' + month : month;

                      return   moment(dateString).format("MMM d,Y");

                        // return day + '/' + month + '/' + year;
                    }
                },
            });
        });
    </script>
@endsection
