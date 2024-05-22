@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')

 
@endsection


@section('css')
    <style>
        .accordion-button {
            padding: 1% !important;
        }

        .accordion-button:not(.collapsed) {
            background-color: #ffffff;
        }

        a.liAct {
            display: inline-block;
            color: rgb(238, 5, 5);
        }

        /* CSS to change the color of the link text to white */
        .white-link {
            color: rgb(8, 8, 8);
            /* Add other styles as needed */
        }

        .note-insert {
            display: none;
        }

        .note-view {
            display: none;
        }
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disputes</li>
        </ol>
    </nav>
    <div class=" accordion row">
        <div class="col-md-12">
         
            <div class="card mt-2">

                <div class="card-body card-padding">
                    <label>Form</label>
                     <hr>
                     <form id="reply_form">

                     <div>
                        <label>SEARCH CUSTOMER*</label>
                        <select name="customer" class="form-control" id="customerDropdown">
                            <option value="">select customer </option>

                             @foreach ($data as $customer)
                            <option value="{{ $customer->id}}">{{$customer->name}} | {{$customer->email}}</option>
                         @endforeach
                        </select>
                        
                
                        
                        <label class="mt-2">SUBJECT*</label>
                        <input type="text" name="subject" class="form-control">
                        <label class="mt-2">MESSAGE*</label>
                        <textarea id="content" name="message" class="form-control summernote"></textarea>
                        <label class="mt-2">Upload file</label>
                        <input type="file" name="file_data" class="form-control">
                     </div>
                     <div class="mt-2">

                         <button type="submit" class="btn btn-success mx-auto"> Save as draft</button>

<input type="button" class="btn btn-info mx-auto mydata" value="Save">


                         
                        </div>
                     </form>

                </div>
            </div>
        </div>
   
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
        $('#customerDropdown').select2();
        alert('hello');
    });
</script>
<script>
    $(document).ready(function() {
      $(document).on('click', '#reply_form .mydata', function(e) {
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
          url: "{{ route('vendor.message.composer_data_send_save') }}",
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
     $(document).ready(function() {
        $(document).on('submit', '#reply_form', function(e) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
                var form = $(this);
                var formData = new FormData(form[0]);
                $.ajax({
                    url: "{{ route('vendor.message.composer_data_send') }}",
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
    @endsection
