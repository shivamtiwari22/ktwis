@extends('vendor.layout.app')
@section('meta_tags')
@endsection
@section('title')
@endsection
@section('css')
@endsection
@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.disputes.index') }}">Disputes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disputes Reply</li>
        </ol>
    </nav>
    <form id="reply_form">
        <input type="hidden" name="id" id="id" value="{{ $dispute->id }}">
        <div class="card p-4 mt-4">
          
            <div class="row">
                <div class="col-sm-6">
                    <label for="">ORDER NUMBER </label><br>
                   <input type="text" name="order" id="attachment" class="form-control" value="{{ $dispute->order_id  }}" readonly>

                </div>
                <div class="col-sm-6">
                    <label for="">PRIORITY* </label><br>
                    <input type="text" name="priority" id="attachment" class="form-control" />

                </div>
                
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <label for="">REFUND AMOUNT*</label><br>
                   <input type="text" name="attachment" id="attachment" class="form-control" />

                </div>
              
                
            </div>
            <label for="">DESCRIPTION</label>
            <textarea id="content" name="content" class="form-control summernote"></textarea>

            <div class="container">
                <input type="checkbox" name="check" id="check" onchange="updateValue()"> Check me

                <label class="checkbox-label ">
                    <h5>SEND A NOTIFICATION EMAIL TO CUSTOMER?,</h5>
                </label>
            </div> 
            <div class="d-flex">
                <button type="submit" class="btn btn-success mx-auto"> Submit</button>
            </div>
        </div>
    </form>
@endsection


@section('script')
   
<script>
    function updateValue() {
        var checkbox = document.getElementById("check");
        var valueElement = document.getElementById("value"); // Assuming you have an element to display the value
    
        if (checkbox.checked) {
            valueElement.textContent = "1";
        } else {
            valueElement.textContent = "0";
        }
    }
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
                url: "{{ route('admin.disputes.update_refundes') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status) {
                        $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success");
                        window.location.reload();
                        setTimeout(function() {
                            window.location.href = response.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", response.message, "top-right", "rgba(0,0,0,0.2)", "error");
                    }
                },
                error: function(xhr, status, error) {
                    $.NotificationApp.send("Error", xhr.responseText, "top-right", "rgba(0,0,0,0.2)", "error");
                }
            });
        });
    });
</script>

        
@endsection
