@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
<link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/css/bootstrap-switch-button.min.css"
rel="stylesheet">
<style>
.switch.ios,
.switch-on.ios,
.switch-off.ios {
    border-radius: 20rem;
}

.switch.ios .switch-handle {
    border-radius: 20rem;
}



.custom-switch {
      position: relative;
      display: inline-block;
      width: 44px;
      height: 24px;
    }
    .custom-switch input {
      display: none;
    }
    .custom-switch .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      -webkit-transition: .4s;
      transition: .4s;
      border-radius: 24px;
    }
    .custom-switch .slider:before {
      position: absolute;
      content: "";
      height: 20px;
      width: 20px;
      left: 2px;
      bottom: 2px;
      background-color: white;
      -webkit-transition: .4s;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider {
      background-color: #2196F3;
    }
    input:focus + .slider {
      box-shadow: 0 0 1px #2196F3;
    }
    input:checked + .slider:before {
      -webkit-transform: translateX(20px);
      -ms-transform: translateX(20px);
      transform: translateX(20px);
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
                        <h4 class="mt-2">Warning</h4>
                        <p class="mt-3">Are you Sure you Want to Delete<b> <span
                                    id="warning-alert-modal-text"></span></b>.</p>

                                    <input type="hidden" name="id" id="client-id">
                                    <button type="button" class="btn btn-warning my-2 confirm" data-bs-dismiss="modal">Confirm</button>
                                    <button type="button" class="btn btn-danger my-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a aria-current="page">Settings</a></li>
            <li class="breadcrumb-item"><a aria-current="page">Clients</a></li>
        </ol>
    </nav>
    <div class="card mt-1 p-3">
        <div class="d-flex justify-content-end mb-2">
            <button class="btn btn-success"  data-toggle="modal" data-target="#exampleModal" > Add Client</button>
        </div>
        <table id="category_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Client Id</th>
                    <th>Client Secret</th>
                    <th>Name</th>
                    <th>Redirect</th>
                    <th>Creation Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($clients as $client)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$client->id}}</td>
                    <td>{{$client->secret}}</td>
                    <td>{{$client->name}}</td>
                    <td>{{$client->redirect}}</td>
                    <td>{{date("d-m-Y", strtotime($client->created_at));}}</td>
                    <td>
                        {{-- <input type="checkbox" data-toggle="switchbutton" id="switch" class="ChangeStatus"
                        {{ $client->password_client == 1 ? 'checked' : '' }} data-id="{{$client->id}}" class="mode" data-style="ios"> --}}


                        <label class="custom-switch">
                            <input type="checkbox"     class="ChangeStatus"  id="switch"  data-id="{{ $client->id}}"    {{ $client->password_client == 1 ? 'checked' : '' }}>
                            <span class="slider"></span>
                          </label> 
                    </td>
                    <td>
                        <a class=" px-2 btn btn-danger deleteTypes"  id="DeleteClient" data-id="{{$client->id}}">
                            <i class="dripicons-trash"></i>
                            </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>



    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Form</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <form id="client-form">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="name" class="with-help">Name</label>
                                <input class="form-control" placeholder="Name" required
                                    name="name" type="text" id="name" >
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6">
                            <label for="">Redirect URL</label>
                            <input class="form-control" placeholder="URL" name="redirect_url" 
                                type="text" required id="">
                        </div>
                        <div class="col-sm-6 mt-3">
                            <label for="">Status</label>
                            <input type="checkbox" data-toggle="switchbutton" id="switch" name="status" class="mode" data-style="ios">
                        </div>
                    </div>
                 

            </div>
                    <div class="modal-footer">
            
              <button type="submit" class="btn btn-primary">Save</button>
                       </div>
              </form>
          </div>
        </div>
      </div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"> </script>

    <script>
            $(document).on('change', '.ChangeStatus', function() {
                var check = $(this).prop('checked');
                var id = $(this).attr('data-id');
                $.ajax({
                    url: "{{ route('admin.client.statusChange') }}",
                    type: "POST",
                    data: {
                        checkValue:check,
                        id:id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                 
                    beforeSend: function() {
                        $("#load").show();
                    },
                    success: function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error");
                        }
                    },
                    complete: function() {
                        $("#load").hide();
                    },
                    error: function(jqXHR, exception) {
                        console.log(jqXHR.responseText); // Log the error for debugging purposes
                    }
                });
            });


            $(document).on("click", ".deleteTypes", function() {
            // Replace $.confirm with the success alert modal code
            $('#warning-alert-modal').modal('show');
                var cl_id = $(this).attr('data-id');
                $('#client-id').val(cl_id);


            });
                $('#warning-alert-modal').on('click', '.confirm', function() {

                      var id =   $('#client-id').val();
                    $.ajax({
                            url: "{{ route('admin.client.delete') }}",
                            type: 'POST',
                            data: {
                                id:id
                            },
                            headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                           
                        })
                        .done(function(result) {
                            if (result.status) {
                                $.NotificationApp.send("Success", result.msg, "top-right",
                                    "rgba(0,0,0,0.2)", "success");
                                setTimeout(function() {
                                window.location.reload();
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", result.msg, "top-right",
                                    "rgba(0,0,0,0.2)", "error");
                            }
                        })
                        .fail(function(jqXHR, exception) {
                            console.log(jqXHR.responseText);
                        });
                
                });
             



                $('#client-form').validate({
            rules: {
                name: {
                    required: true,
                    maxlength:26
                },
                redirect_url: {
                    linkvalid: true,
                },
             
            },
        });

        $.validator.addMethod("linkvalid", function(value, element) {
                return this.optional(element) ||
                    /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i
                    .test(value);
            }, "Please enter valid link.");



     $(document).on('submit', '#client-form', function(e) {
        e.preventDefault();
        let fd = new FormData(this);
        fd.append('_token', "{{ csrf_token() }}");
        $.ajax({
            url: "{{ route('admin.client.add') }}",
            type: "POST",
            data: fd,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(result) {
                console.log(result);
                if (result.status) {
                    $.NotificationApp.send("Success", result.msg, "top-right",
                        "rgba(0,0,0,0.2)", "success")
                         window.location.reload();

                } else {
                    $.NotificationApp.send("Error", result.msg, "top-right",
                        "rgba(0,0,0,0.2)", "error")
                }
            },
        });
    });
    </script>



@endsection
