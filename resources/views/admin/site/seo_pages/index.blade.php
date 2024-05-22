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
                    <h4 class="mt-2">Confirm</h4>
                    <p class="mt-3">Are You Sure to Delete this Vendor Application</p>
                    <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div style="display: flex; justify-content: space-between; align-items: flex-end;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Seo Page</li>
        </ol>
    </nav>
    <a href="{{route('admin.appereance.seo.create_pages')}}"><button type="button" class="btn btn-primary">Add Pages</button></a>
</div>
<hr>
<div class="card mt-1 p-3">
    <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Meta Title</th>
                <th>Date and Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

            @foreach ($pages as $item)
            <tr>
                <td>{{$loop->iteration}}</td>
                <td>{{$item->type}}</td>
                <td>{{$item->meta_title}}</td>
                <td>{{$item->created_at->format('M j, Y H:i:s')}}</td>
                <td>
                    <a href="{{route('admin.appereance.seo.view_pages', $item->id) }}" class="px-2 btn btn-primary text-white mx-1" id="showproduct"><i class="dripicons-preview"></i></a>
                <a href="{{route('admin.appereance.seo.edit_pages', $item->id) }}" class="px-2 btn btn-warning text-white mx-1" id="showproduct"><i class="dripicons-document-edit"></i></a>
                <button class="px-2 btn btn-danger delete_page" id="delete_page" data-id="{{ $item->id }}" data-name="{{ $item->title }}"><i class="dripicons-trash"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('script')
<script>

    //   $(document).ready(function() {
    //         $('#rate_table').DataTable({
    //             // dom: 'Bfrtip',
    //             buttons: [{
    //                 extend: 'csv',
    //                 split: ['pdf', 'excel', 'csv'],
    //             }]
    //         });
    //     });


    // $(function() {
    //     $.fn.tableload = function() {
    //         $('#page_appereance').dataTable({
    //             "scrollX": true,
    //             "processing": true,
    //             "responsive": false,
    //             pageLength: 10,
    //             "serverSide": true,
    //             "bDestroy": true,
    //             'checkboxes': {
    //                 'selectRow': true
    //             },
    //             "ajax": {
    //                 url: "{{ route('admin.appereance.list_pages') }}",
    //                 "type": "POST",
    //                 "data": function(d) {
    //                     d._token = "{{ csrf_token() }}";
    //                 },
    //                 dataFilter: function(data) {
    //                     var json = jQuery.parseJSON(data);
    //                     json.recordsTotal = json.recordsTotal;
    //                     json.recordsFiltered = json.recordsFiltered;
    //                     json.data = json.data;
    //                     return JSON.stringify(json);
    //                 }
    //             },
    //             "order": [
    //                 [0, 'DESC']
    //             ],
    //             "columns": [

    //                 {
    //                     "width": "5%",
    //                     "targets": 0,
    //                     "name": "S_no",
    //                     'searchable': true,
    //                     'orderable': true
    //                 },
    //                 {
    //                     "width": "10%",
    //                     "targets": 1,
    //                     "name": "banner_image",
    //                     'searchable': true,
    //                     'orderable': true
    //                 },

    //                 {
    //                     "width": "10%",
    //                     "targets": 1,
    //                     "name": "type",
    //                     'searchable': true,
    //                     'orderable': true
    //                 },

    //                 {
    //                     "width": "10%",
    //                     "targets": 1,
    //                     "name": "date_time",
    //                     'searchable': true,
    //                     'orderable': true
    //                 },

    //                 {
    //                     "width": "10%",
    //                     "targets": 2,
    //                     "name": "action",
    //                     'searchable': true,
    //                     'orderable': true
    //                 }


    //             ]
    //         });
    //     }
    //     $.fn.tableload();

        $('body').on("click", ".delete_page", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');
            let fd = new FormData();
            fd.append('id', id);
            fd.append('name', name);
            fd.append('_token', '{{ csrf_token() }}');


            $("#warning-alert-modal-text").text(name);
            $('#warning-alert-modal').modal('show');
            $('#warning-alert-modal').on('click', '.confirm', function() {
                $.ajax({
                        url: "{{route('admin.appereance.seo.delete_page')}}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.message, "top-right", "rgba(0,0,0,0.2)", "success");

                            window.location.reload();
                        } else {
                            $.NotificationApp.send("Error", result.message, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            });
        });


</script>
@endsection