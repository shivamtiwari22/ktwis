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
                        <p class="mt-3">Are You Sure to Delete this Content</p>
                        <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                        <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Warning Alert Modal -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dynamic Content</li>
            </ol>
        </nav>
        <a href="{{ route('admin.appereance.addContent') }}">
            <button type="button" class="btn btn-primary">Add Content</button>
        </a>
    </div>
    <hr>
    <div class="card mt-1 p-3">
        <table id="" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Banner Image</th>
                    <th>Meta Title</th>
                    <th>Meta Description</th>
                    <th>Action</th>
                </tr>
            </thead>

            @foreach ($data as $item)
       
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->type }}</td>
                        <td> <img src="{{ asset('public/admin/contact/' . $item->banner_image) }}" alt=""
                                width="50px" height="50px"></td>
                        <td>{{ $item->meta_title }}</td>
                        <td>{{ $item->meta_description }}</td>
                        <td><a href="{{ route('admin.appereance.editContent', $item->id) }}" class="btn btn-primary"><i
                                    class="fa fa-edit"></i></a>
                            <a href="javascript::void(0)" class="btn btn-danger delete" data-id="{{ $item->id }}"> <i
                                    class="fa fa-trash"></i></a>
                        </td>
                    </tr>
          
            @endforeach
        </table>
    </div>
@endsection

@section('script')
    <script>
        // $(function() {
        //     $.fn.tableload = function() {
        //         $('#blog_appereance').dataTable({
        //             "scrollX": true,
        //             "processing": true,
        //             pageLength: 10,
        //             "serverSide": true,
        //             "bDestroy": true,
        //             "responsive": false,
        //             'checkboxes': {
        //                 'selectRow': true
        //             },
        //             "ajax": {
        //                 url: "{{ route('admin.appereance.list_blogs') }}",
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
        //                     "name": "title",
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

        //     $('body').on("click", ".delete_blogs", function(e) {
        //         var id = $(this).data('id');
        //         var name = $(this).data('name');
        //         let fd = new FormData();
        //         fd.append('id', id);
        //         fd.append('name', name);
        //         fd.append('_token', '{{ csrf_token() }}');


        //         $("#warning-alert-modal-text").text(name);
        //         $('#warning-alert-modal').modal('show');
        //         $('#warning-alert-modal').on('click', '.confirm', function() {
        //             $.ajax({
        //                     url: "{{ route('admin.appereance.delete_blogs') }}",
        //                     type: 'POST',
        //                     data: fd,
        //                     dataType: "JSON",
        //                     contentType: false,
        //                     processData: false,
        //                 })
        //                 .done(function(result) {
        //                     if (result.status) {
        //                         $.NotificationApp.send("Success", result.message, "top-right", "rgba(0,0,0,0.2)", "success");
        //                         $.fn.tableload();
        //                     } else {
        //                         $.NotificationApp.send("Error", result.message, "top-right", "rgba(0,0,0,0.2)", "error");
        //                     }
        //                 })
        //                 .fail(function(jqXHR, exception) {
        //                     console.log(jqXHR.responseText);
        //                 });
        //         });
        //     });

        // });
    </script>


    <script>
        $('body').on("click", ".delete", function(e) {
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
                        url: "{{ route('admin.appereance.deleteContent') }}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.message, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            window.location.reload();
                        } else {
                            $.NotificationApp.send("Error", result.message, "top-right",
                                "rgba(0,0,0,0.2)", "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            });
        });
    </script>
@endsection
