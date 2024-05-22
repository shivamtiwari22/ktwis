@extends('admin.layout.app')

@section('meta_tags')

@endsection


@section('title')

@endsection


@section('css')
<style>
    .star {
        color: #ccc;
        font-size: 24px;
    }

    .filled-star {
        color: goldenrod;
    }


    .half-star {
        position: relative;
        display: inline-block;
        font-size: 24px;
    }

    .half-star::before {
        content: '\2605';
        position: absolute;
        left: 0;
        width: 50%;
        overflow: hidden;
        color: goldenrod;
        z-index: 1;
    }

    .half-star::after {
        content: '\2606';
        position: absolute;
        left: 50%;
        width: 0%;
        overflow: hidden;
        color: goldenrod;
        z-index: 0;
    }
</style>
@endsection


@section('main_content')
<!-- Warning Alert Modal -->
<div style="display: flex; justify-content: space-between; align-items: flex-end;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Review</li>
        </ol>
    </nav>
</div>
<hr>
<div class="card mt-1 p-3">
    <table id="review_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Vendor</th>
                <th>Product</th>
                <th>Rating</th>
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
            $('#review_table').dataTable({
                "scrollX": true,
                "processing": true,
                "responsive": false,
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('admin.reviews.list') }}",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}";
                    },
                    dataFilter: function(data) {
                        var json = jQuery.parseJSON(data);
                        json.recordsTotal = json.recordsTotal;
                        json.recordsFiltered = json.recordsFiltered;
                        json.data = json.data;
                     
                        console.log(JSON.stringify(json));
                 
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
                        "name": "rating",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "rating",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "rating",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "rating",
                        'searchable': true,
                        'orderable': true
                    },
                ]
            });
        };

        $.fn.tableload();
    });
</script>
@endsection