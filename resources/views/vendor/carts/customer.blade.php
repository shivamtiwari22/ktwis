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
<div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
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
</div>

<!--  -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Wishlist List</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <!-- <div class="d-flex justify-content-end mb-2"> -->
    <button class="btn btn-success" data-toggle="modal" id="add_carts">Add Carts</button>
    {{ $user}}
    <input id="search_customer" class="form-control" placeholder="Search Customer" name="search_customer" type="text">
    <ul id="search-results"></ul>
    <!-- </div> -->
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
    $(document).ready(function() {
        $("#search_customer").hide();
        $("#add_carts").click(function() {
            $("#search_customer").toggle(1000);
        });
    });
</script>
<script>
    $(document).ready(function() {
        $('#search_customer').on('input', function() {
            var query = $(this).val();
            if (query.length >= 3) {
                $.ajax({
                    url: '{{ route("vendor.carts.search_customer") }}',
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
                                var html = `<div class="row form-control" style="border:1px light  grey;">
                                                <p class="admin-user-widget-title">
                                                ` + result.name + `      |        ` + result.email + `
                                                </p>
                                                <form id="user_form">
                                                    <input type="text" name="user_id" value=" ` + result.id + `  " id="user_id">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-success">Proceed</button>
                                                </form>
                                            </div>`;
                                $searchResults.append(html);
                            });
                            $('#user_form').on('submit', function(e) {
                                e.preventDefault();

                                var userId = $(this).find('#user_id').val();
                                alert(userId);
                                $.ajax({
                                    url: "{{ route('vendor.carts.get_customer') }}",
                                    type: "POST",
                                    data: {
                                        user_id: userId,
                                        _token: $('input[name="_token"]').val()
                                    },
                                    dataType: 'json',
                                    headers: {
                                        'X-CSRF-TOKEN': $('input[name="_token"]').val()
                                    },

                                    success: function(result) {
                                        console.log(result);
                                        if (result.status) {
                                            $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success")
                                            // setTimeout(function() {
                                            //     window.location.href = result.location;
                                            // }, 1000);
                                        } else {
                                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error")
                                        }
                                    },
                                });
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

</script>
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
                ]
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
        //                 url: "{{route('vendor.shipping.rates.delete')}}",
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
                wishlistData += '<h5>Customer : ' + response[0].name + '</h5>';
                wishlistData += '<h5>Email : ' + response[0].email + '</h5>';
                wishlistData += '<h5>Member Since : ' + formatDate(response[0].created_at) + '</h5> <hr>';

                if (response.length > 0 && response[0].wishlists) {
                    wishlistData += '<h5>Products:</h5>';

                    response[0].wishlists.forEach(function(wishlist) {

                        wishlistData += '<p>Product Name: ' + wishlist.product.name + '</p>';
                        wishlistData += '<p>Price: ' + wishlist.product.inventory.offer_price + '</p>';
                        wishlistData += '<p>Image: ' + ' <img src="{{asset("public/vendor/featured_image")}}/' + wishlist.product.featured_image + '" alt="Product Image" width="50px;">';

                        if (wishlist.product.inventoryVariants && wishlist.product.inventoryVariants.length > 0) {
                            wishlistData += '<ul>';
                            wishlist.product.inventoryVariants.forEach(function(inventoryVariant) {
                                wishlistData += '<li>Variant ID: ' + inventoryVariant.id + '</li>';
                                wishlistData += '<li>Variant Name: ' + inventoryVariant.offer_price + '</li>';
                            });
                            wishlistData += '</ul>';
                        }
                        wishlistData += '<hr>';
                    });
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