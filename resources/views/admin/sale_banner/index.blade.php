@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<style>
        .mysize {
        
    position: absolute;
    top: 10px; /* Adjust the top position to your desired distance from the top */
    right: 10px; /* Adjust the right position to your desired distance from the right */
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
        <li class="breadcrumb-item active" aria-current="page">Sale Banner</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <label>Sale Banner</label>
    <hr>
    <button type="button" class="btn btn-primary mysize" data-bs-toggle="modal" data-bs-target="#exampleModales">
        Add Sale Banner
      </button>  
    <table id="order_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>
<div class="modal fade" id="exampleModales" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Add Sale Banner</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="reply_formes">
                <div>
                    <label class="mt-2 ">File (PNG,JPG,JPENG,2MB)<span class="text-danger">*</span></label>
                    <input   type="file"  id="content" name="file_data" class="form-control ">
                </div>
                <div class="mt-2">
                    <label for="">Link <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="link">
                </div>
                <div class="mt-2" style="text-align: right;"> 
                    <input type="submit" class="btn btn-success mx-auto mydatadraft mytype"  >
                </div>
            </form>
        </div>
       
      </div>
    </div>
  </div>
  <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="staticBackdropLabel">Update Sale Banner </h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="reply_form">
                <div>
                    <input type="hidden" id="inputID" name="id" >
                    <label class="">File (PNG,JPG,JPENG,2MB)</label>
                    <input type="file" class="form-control" id="inputsubjectes" name="file_data">
                </div>

                <div class="mt-2">
                    <label for="">Link <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="link" id="inputLink">
                </div>
                <div class="mt-2" style="text-align: right;"> 
                      <button type="submit" class="btn btn-info mx-auto mydata mytype">Update</button>
                
                </div>
            </form>
            
         </div>
      
      </div>
    </div>
  </div>
  <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-warning"></i>
                    <h4 class="mt-2">Warning</h4>
                    <p class="mt-3">Are you sure you want to delete</p>
                    <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection


@section('script')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script type="text/javascript">
    $('.summernote').summernote({
        height: 100
    });
</script>
<script>

    $(document).ready(function() {

        $('#reply_form').validate({
            rules: {
                file_data: {
                    imageFormat: true,
                        filesize: 1024

                },
                link:{
                    required:true,
                    linkvalid:true

                }
               
            },
            
            messages: {
                file_data: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                }
        });

        $.validator.addMethod("linkvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please enter valid link.");

        $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");
        

      $(document).on('submit', '#reply_form', function(e) {
        $.ajaxSetup({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
        });
        e.preventDefault();
        var form = $(this).closest('form'); // Get the parent form of the clicked button
        var formData = new FormData(form[0]); // Corrected line - use form[0] instead of form
        // alert(formData);
        $.ajax({
          url: "{{ route('admin.setting.sale_banner_update') }}",
          method: "POST",
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
                    console.log(response);
                    if (response.status) {
                        $.NotificationApp.send("Success", response.message, "top-right",
                            "rgba(0,0,0,0.2)", "success");
                        setTimeout(function() {
                            window.location.href = response.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", response.message, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                    }
                },
          
          error: function(xhr, status, error) {
            console.log(xhr.responseText);
            var response = JSON.parse(xhr.responseText);
            var message = response.message;
            console.log(message);
            $.NotificationApp.send("Error", message, "top-right",
              "rgba(0,0,0,0.2)", "error");
          }
        });
      });
    });
  </script>
<script>

 
        $(document).on('click', '.openModalButton', function() {
            var disputeId = $(this).data('dispute');
            $.ajax({
                url: "{{ route('admin.setting.sale_banner_edit') }}",
                method: 'GET',
                data: {
                    disputeId: disputeId
                },
                success: function(response) {
                    var data = response.data;
                    $('#inputID').val(data.id);
                    $('#inputsubject').val(data.image);
                    $('#inputLink').val(data.link);
                  $('#staticBackdrop').modal('show');
                },
                error: function(error) {
                    console.error('Error loading content: ' + error);
                }
            });
        });
    </script>
<script>


    $(document).ready(function() {

        $('#reply_formes').validate({
            rules: {
                file_data: {
                    required: true,
                    imageFormat: true,
                        filesize: 1024

                },
                link: {
                    required:true,
                    linkvalid:true
                }
               
            },

            messages: {
                file_data: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    },
                }
        });


        $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");
        



        $(document).on('submit', '#reply_formes', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var form = $(this).closest('form');
            var formData = new FormData(form[0]); 
            $.ajax({
                url: "{{ route('admin.setting.add_sale_banner') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    if (response.status) {
                        $.NotificationApp.send("Success", response.message, "top-right",
                            "rgba(0,0,0,0.2)", "success");
                        setTimeout(function() {
                            window.location.href = response.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", response.message, "top-right",
                            "rgba(0,0,0,0.2)", "error");
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    var response = JSON.parse(xhr.responseText);
                    var message = response.message;
                    console.log(message);
                    $.NotificationApp.send("Error", message, "top-right",
                        "rgba(0,0,0,0.2)", "error");
                }
            });
        });
    });
</script>
<script>
    $(function() {
        $.fn.tableload = function() {
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
                    url: "{{ route('admin.setting.sale_banner_data') }}",
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
                        "name": "vendor_name",
                        'searchable': true,
                        'orderable': true
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
                        "targets": 2,
                        "name": "action",
                        'searchable': true,
                        'orderable': true
                    }
                ]
            });
        };

        $.fn.tableload();
        // status change
        $('body').on('change', '.change_status' , function(e){
                e.preventDefault();

                var status_value =   $(this).val();
                var sale_id =   $(this).attr('data-id');
                let fd = new FormData();
                fd.append('_token', "{{ csrf_token() }}");
                fd.append('sale_id', sale_id);
                fd.append('status_value', status_value);

                $.ajax({
                    url: "{{ route('admin.setting.sale_banner_status_update') }}",
                    type: "POST",
                    data: fd,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
            })
            .done(function(result) {
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                        setTimeout(function() {
                            window.location.href = result.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                    }
                })
                .fail(function(jqXHR, exception) {
                    console.log(jqXHR.responseText);
                });
        });

    });
</script>
<script>
    $('body').on("click", ".deleteType", function(e) {
          var id = $(this).data('id');
          var name = $(this).data('name');
          let fd = new FormData();
          fd.append('id', id);
          fd.append('_token', '{{ csrf_token() }}');
          $("#warning-alert-modal-text").text(name);
          $('#warning-alert-modal').modal('show');
          $('#warning-alert-modal').on('click', '.confirm', function() {
              $.ajax({
                      url: "{{route('admin.setting.sale_banner_delete')}}",
                      type: 'POST',
                      data: fd,
                      dataType: "JSON",
                      contentType: false,
                      processData: false,
                  })
                  .done(function(result) {
                      if (result.status) {
                          $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                          setTimeout(function() {
                              window.location.href = result.location;
                          }, 1000);
                      } else {
                          $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                      }
                  })
                  .fail(function(jqXHR, exception) {
                      console.log(jqXHR.responseText);
                  });
          });
      });
</script>
@endsection