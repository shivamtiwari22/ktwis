@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')

<style>

.field-icon {
            float: right;
            margin-left: -25px;
            margin-top: -25px;
            position: relative;
            z-index: 2;
            right: 8px;

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
                    <input type="hidden" name="" id="user_id">
                    <p class="mt-3">Are You Sure to Delete this Vendor Application</p>
                    <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Merchants</li>
    </ol>
</nav>
<div class="card mt-1 p-3">
    <div class="d-flex justify-content-end mb-2">
        <a href="javascript::void(0)" class="btn btn-success"  data-toggle="modal" data-target="#exampleModal">Add New Vendor</a>
        </div>
    <table id="category_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Avatar</th>
                <th>Name</th>
                <th>Email</th>
                <th>Shop</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>



  

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-vendor"  enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-8">
                            <div class="form-group">
                                <label for="name" class="with-help">Merchant Name <span class="text-danger">*</span></label>
                                <input class="form-control" placeholder="Merchant name" required 
                                    name="name" type="text" id="name">
                                 
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <label for="">Status <span class="text-danger">*</span> </label>
                            <select name="status" id="" class="form-select">
                                <option value="inactive"  >Inactive</option>
                                <option value="active"  >Active</option>
                            </select>
                        </div>

                    </div>
                    <div class="row mt-2">

                        <div class="col-sm-6">
                            <label for="active" class="with-help">Email <span class="text-danger">*</span></label>
                            <input class="form-control" placeholder="Emial" name="email" type="email" required 
                                id="">

                        </div>
                        <div class="col-sm-6">
                            <label for="">Temporary Password  <span class="text-danger">*</span></label>

                            <input class="form-control" placeholder="Password" name="password" required 
                                type="password" id="new_pass">
                                <span toggle="#new_pass" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6">
                            <label for="">Shop Name <span class="text-danger">*</span></label>
                            <input class="form-control" placeholder="Shop name" name="shop_name" type="text" required  
                                id="">
                        </div>
                        <div class="col-sm-6">
                            <label for="">Legal Name  <span class="text-danger">*</span></label>
                            <input class="form-control" placeholder="Legal name" name="legal_name" type="text" required  
                            id="">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6">
                            <label for="">Timezone <span class="text-danger">*</span></label>
                            <input class="form-control" placeholder="Timzezone" name="timezone" type="text"   required
                            id="">
                        </div>

                        <div class="col-sm-6">
                            <label for="">External Url </label>
                            <input class="form-control" placeholder="External url" name="shop_url" type="text"  
                            id="">
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-12">
                            <label for="">Description</label>
                            <textarea id="content" name="message" class="form-control summernote"></textarea>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-sm-12">
                            <label for="">Avatar</label>
                            <input type="file" name="profile_pic" class="form-control" >
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-secondary mt-n1" id="user-save">Save</button>
            </div>
            </form>
        </div>

    </div>
</div>



</div>
</div>
@endsection

@section('script')
<script>
    $(function() {
        $.fn.tableload = function() {
            $('#category_table').dataTable({
                "scrollX": true,
                "responsive": false,
                "processing": true,
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('admin.vendor.applications.list') }}",
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
                "columns": [
                    {
                        "width": "5%",
                        "targets": 0,
                        "name": "S_no",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "category_name",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "category_name",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "parent_category",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "parent_category",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "parent_category",
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
            });
        };

        $.fn.tableload();

        $('body').on("click", ".deleteUser", function(e) {
            var id = $(this).data('id');
            var name = $(this).data('name');
              $('#user_id').val(id);
          

            $("#warning-alert-modal-text").text(name);
            // Replace $.confirm with the success alert modal code
            $('#warning-alert-modal').modal('show');
            // Optionally, you can listen for the modal's "Continue" button click event


      
        });


        $('#warning-alert-modal').on('click', '.confirm', function() {
            var user_id =   $('#user_id').val();
                let fd = new FormData();
            fd.append('id', user_id);
            fd.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: "{{route('admin.vendor.applications.list.delete')}}",
                    type: 'POST',
                    data: fd,
                    dataType: "JSON",
                    contentType: false,
                    processData: false,
                })
                .done(function(result) {
                    console.log(result);
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

<script type="text/javascript">
    $('.summernote').summernote({
        height: 50
    });
</script>

<script>

$(document).ready(function(){
    
$('#add-vendor').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 36,
                    },
                    email: {
                        required: true,
                        maxlength: 40,
                        email: true,
                        emailWithDomain: true

                    },
                    password: {
                        required: true,
                        minlength:6
                    },
                    shop_name: {
                        required: true,
                        maxlength: 26,


                    },
                    legal_name: {
                        required: true,
                        maxlength: 26,

                    },
                    shop_url: {
                        linkvalid: true,

                    },
                    profile_pic: {
                        imageFormat:true,   
                        filesize: 1024 ,

                    },

                },
                messages: {
                    profile_pic: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB",
                    }
                },
            });

            $.validator.addMethod("emailWithDomain", function(value, element) {
                return this.optional(element) || /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(value);
            }, "Please enter a valid email address.");

            $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");

            $.validator.addMethod("linkvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please enter valid link.");

})



    $(document).on('submit','#add-vendor', function(e) {

        e.preventDefault();    
        // $('#user-save').prop('disabled', true);

        let fd = new FormData(this);
        fd.append('_token', "{{ csrf_token() }}");

        $.ajax({
                url: "{{ route('admin.vendor.applications.add.vendor') }}",
                type: "POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
            })
            .done(function(data) {
                // console.log(data);
                if (data.status) {
                    $.NotificationApp.send("Success", data.msg, "top-right", "rgba(0,0,0,0.2)",
                        "success");
                    setTimeout(function() {
                        window.location.reload()
                    }, 1000);

                    $('#user-save').prop('disabled', false);
                    $('#exampleModal').modal('hide');

                } else {
                    $.NotificationApp.send("Error", data.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                }
            })
            .fail(function(jqXHR, exception) {
                // console.log(jqXHR.responseText);
                $.NotificationApp.send("Error", data.msg, "top-right", "rgba(0,0,0,0.2)", "error");

            });
    });
</script>
<script>
      $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
</script>


@endsection
