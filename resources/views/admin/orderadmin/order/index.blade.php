@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style>
      .search_list {
            display: grid;
    grid-template-columns: auto auto auto auto auto auto;
    column-gap: 5px;
        }
</style>
@endsection


@section('main_content')

<!-- Warning Alert Modal -->
<div class="modal fade" id="view_entities" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Order Details</h4>
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
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Order</li>
    </ol>
</nav>

<div class="add-back">
    <form class="form-inline search_list">
        <div class="search-element bg-light rounded showdata">
            <select class="form-select" id="order_status" aria-label="Search" data-width="">
                <option value="">Search by Order Status</option>
                @foreach($statuses  as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach

            </select>
        </div>
        <div class="search-element bg-light rounded ">
            <select class="form-select" id="payment_status" aria-label="Search" data-width="">
                <option value="">Search by Payment Status</option>
                <option value="success">Paid</option>
                <option value="unpaid">Unpaid</option>
            </select>
        </div>
        <div class="search-element bg-light rounded">
            <input type="date" name="from_date" class="form-control lg-light from_date"
                value="{{ date('Y-01-01') }}" aria-label="Search">
        </div>
        <div class="search-element bg-light rounded">
            <input type="date" name="to_date" class="form-control lg-light to_date"
                value="{{ date('Y-m-d') }}" aria-label="Search">
        </div>

        <button type="submit" id="searchButtones" class="form-control  ml-3 add-search  btn btn-primary"
            data-width="200">Search</button>
    </form>
</div>

<div class="card p-4 mt-4">
    <table id="order_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Order Number</th>
                <th>Order Date</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
@endsection


@section('script')
<script>

$(document).ready(function() {
            $('#searchButtones').click(function(event) {
                event.preventDefault();
                var from_date = $('.from_date').val();
                var to_date = $('.to_date').val();
                var payment_status = $('#payment_status').val();
                var order_status = $('#order_status').val();
                $.fn.tableload(from_date, to_date, payment_status, order_status);
            });
        });


    $(function() {
        $.fn.tableload = function(from_date, to_date, payment_status, order_status) {
            
            $('#order_table').dataTable({
                "scrollX": true,
                "processing": true,
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('admin.order.list') }}",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}";
                        d.order_status = order_status;
                        d.payment_status = payment_status;
                        d.from_date = from_date;
                        d.to_date = to_date;
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
                        "name": "vendor_name",
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
            url: "{{ route('admin.carts.view_order', ['id' => ':id']) }}".replace(':id', id),
            type: 'GET',
            dataType: "JSON",
            success: function(response) {
                console.log(response);
                var wishlistData = '';
                wishlistData += '<h5>Customer : ' + response[0].name + '</h5>';
                wishlistData += '<h5>Email : ' + response[0].email + '</h5>';
                wishlistData += '<h5>Member Since : ' + formatDate(response[0].created_at) + '</h5> <hr>';

                wishlistData +=
                    '<div class="row"><div class="col-sm-4"><b>Product Name</b></div><div class="col-sm-4"><b>Image</b></div><div class="col-sm-4"><b>Price</b></div></div><hr>';
                if (response.length > 0 && response[0].order) {

                    response[0].order.forEach(function(order) {
                        order.order_items.forEach(function(orderItem) {
                            wishlistData += '<div class="row"><div class="col-sm-4"><p> ' + orderItem.product.name + '</p></div>';

                            if (orderItem.product.inventory) {
                                wishlistData += '<div class="col-sm-4"><p> <img src="{{asset("public/vendor/featured_image")}}/' + orderItem.product.featured_image + '" alt="" width="50px;"></div>';
                                wishlistData += '<div class="col-sm-4"><p>' + orderItem.product.inventory.offer_price + '</p></div>';
                            }

                            if (orderItem.variant) {
                                wishlistData += '<div class="col-sm-4"><p><img src="{{asset("public/vendor/featured_image/inventory_with_variant")}}/' + orderItem.variant.image_variant + '" alt="Product Image" width="50px;"></div>';
                                wishlistData += '<div class="col-sm-4"><p>' + orderItem.variant.offer_price + '</p></div>';
                            }

                            wishlistData += '</div><hr>';
                        });
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