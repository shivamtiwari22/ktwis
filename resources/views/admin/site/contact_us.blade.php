@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')

@endsection


@section('main_content')


<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
               <li class="breadcrumb-item active" aria-current="page">Contact Us</li>
    </ol>
</nav>

<hr>
<form id="add_blogs_form">
    @csrf
    <div class="row">
        <div class="col-sm-6">
            <label for="title">Email:<span class="red">*</span></label>
            <input type="email" id="" name="email"  value="{{$contact ? $contact->email : ''}}" class="form-control" placeholder="Enter Your Email">
        </div>
        <div class="col-sm-6">
            <label for="banner_image">Phone:<span class="red">*</span></label>
            <input type="text" id="" name="phone" class="form-control"  value="{{$contact ? $contact->phone : ''}}" placeholder="Enter Your Phone">
        </div>
      
    </div><br>

    <div class="row">
        <div class="col-sm-12">
            <label for="content">Address:</label>
        <textarea id="content" name="address" class="form-control summernote">{!!$contact ? $contact->address : ''!!}</textarea>
        </div>
       
    <div>
     


    <div class="d-flex mb-2">
        <button type="submit" class="btn btn-success mx-auto"> Submit</button>
    </div>
</form>

@endsection

@section('script')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>



<script type="text/javascript">
    $(document).ready(function() {
        $('.summernote').summernote({
            height: 200
        });
    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(document).ready(function() {
        $('#add_blogs_form').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 255,
                    emailWithDomain : true
                },
                phone: {
                    required: true,
                    number: true,
                    maxlength: 12,
                    minlength:8

                },
                address: {
                    required: true
                },
              
            },
          
        });

        $.validator.addMethod("emailWithDomain", function(value, element) {
                return this.optional(element) || /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(value);
            }, "Please enter a valid email address.");
    })
</script>
<script>
    $(function() {
        $(document).on('submit', '#add_blogs_form', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(form[0]);

            $.ajax({
                url: "{{route('admin.appereance.update_contact_us')}}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    if (response.status) {
                        $.NotificationApp.send("Success", response.message, "top-right", "rgba(0,0,0,0.2)", "success")
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    } else {
                        $.NotificationApp.send("Error", response.message, "top-right", "rgba(0,0,0,0.2)", "error")
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    $.NotificationApp.send("Error", xhr.responseText, "top-right", "rgba(0,0,0,0.2)", "error");

                }
            });
        });
    });
</script>

@endsection