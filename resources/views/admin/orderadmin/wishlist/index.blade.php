@extends('admin.layout.app')

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
            {{-- <div class="modal-body"> --}}
                <div class="modal-body" style="max-height: 400px; overflow-y: auto;">


            </div>
        </div>
    </div>
</div>

<!--  -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Wishlist List</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <!-- <div class="d-flex justify-content-end mb-2">
        <a href="{{route('vendor.shipping.rates.create')}}" class="btn btn-success">Add Rate</a>
    </div> -->
    <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Customer Name</th>
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
                    url: "{{ route('admin.wishlist.list') }}",
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
                        'orderable': false
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

    });
</script>
<script>
    $('body').on("click", "#view_wishlist", function(e) {
        var id = $(this).data('id');
        var name = $(this).data('name');

        $('#view_entities').modal('show');

        $.ajax({
            url: "{{ route('admin.wishlist.view_wishlist', ['id' => ':id']) }}".replace(':id', id),
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
                        wishlistData += '<table class="table">';
                        wishlistData +=
                            '<thead><tr><th>Product Name</th><th>Image</th><th>Price</th></tr></thead>';
                        wishlistData += '<tbody>';

                        response[0].wishlists.forEach(function(wishlist) {
                            console.log(wishlist);
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

                    day = day < 10 ? '0' + day : day;
                    month = month < 10 ? '0' + month : month;

                    return day + '/' + month + '/' + year;
                }
            },
        });
    });
</script>
@endsection