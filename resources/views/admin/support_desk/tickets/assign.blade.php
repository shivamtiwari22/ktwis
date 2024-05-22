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
                    <label for="">ASSIGN </label><br>
                    <select name="user_name" id="reply_status" class="form-control">
                        @foreach($data as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                    </select>
                </div>
              
                
            </div><br><br>

            <div class="d-flex">
                <button type="submit" class="btn btn-success mx-auto"> Submit</button>
            </div>
        </div>
    </form>
@endsection


@section('script')
   
  
    
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
                url: "{{ route('admin.disputes.update_assign_ticket') }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    if (response.status) {
                        // Success notification and redirect
                        $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success");
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
