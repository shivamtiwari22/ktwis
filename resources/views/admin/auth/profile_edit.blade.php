@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection


@section('main_content')

<body>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- toastr -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <style>
        .red {
            color: red;
        }
    </style>
</body>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
    </ol>
</nav>

<div class="card p-4 mt-4">
    <h2>Edit <span class="badge badge-success-lighten">Profile</span></h2>
    <hr>
    <form action="#" id="profileForm" enctype="multipart/form-data" method="post">
        <input type="hidden" name="id" id="id" value="{{ $user->id }}">
        <div class="row">
            <div class="col-sm-6">
                <label for="fullname" class="form-label">Full Name<span class="red">*</span></label>
                <input class="form-control" type="text" id="fullname" value="{{ $user->name }}" name="name" placeholder="Enter your name"><br>

                <label for="emailaddress" class="form-label">Email address :</label>
                <input class="form-control" type="email" readonly id="emailaddress" value="{{$user->email}}"><br>

                <label for="emailaddress" class="form-label">Role :</label>
                <input class="form-control" type="role" readonly value="{{$role->role}}">
            </div>

            <div class="col-sm-1"></div>

            <div class="col-sm-5"><br><br>
                @if ($user->profile_pic)
                <img src="{{ asset(asset('public/admin/profile_pic/' . $user->profile_pic)) }}" width="100px" height="100px" class="mb-2" alt="Profile"><br><br>
            @else
                <img src="{{ asset(asset('public/assets/images/users/avatar-1.jpg')) }}" width="50px"
                    height="60px" class="mb-2" alt="Profile"> <br> <br>
            @endif

                <label for="fullname" class="form-label">Profile Image <span>(JPG, JPEG, PNG, 2MB max)</span> :</label>
                <input type="file" name="profile_pic" id="profile_pic" class="form-control" accept="image/*">
            </div>
        </div><br><br>

        <div class="d-flex text-center justify-content-end">
            <button class="btn btn-primary mx-auto" type="submit"><i class="mdi mdi-login"></i> Update </button>
        </div>
    </form>
</div>
@endsection


@section('script')
<script>
    $(document).ready(function() {
        $('#profileForm').validate({
            rules: {
                name: {
                    required: true,
                    minlength: 4,
                    maxlength: 30,
                },
                profile_pic: {
                        imageFormat: true,
                        filesize: 2024
                    }
            },

            messages: {
                    profile_pic: {
                        imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                        filesize: "Maximum file size is 2MB"
                    }
                },
        })

        $.validator.addMethod("filesize", function(value, element, param) {
                var maxSize = param * 1024; // Convert param to bytes
                return this.optional(element) || (element.files[0].size <= maxSize);
            }, "File size must be less than {0} KB");

            $.validator.addMethod("imageFormat", function(value, element) {
                var allowedFormats = ["jpg", "jpeg", "png"];
                var extension = value.split('.').pop().toLowerCase();
                return this.optional(element) || $.inArray(extension, allowedFormats) !== -1;
            }, "Invalid image format. Allowed formats: jpg, jpeg, png");
    })
</script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).on('submit','#profileForm',function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: "{{ route('admin.profile.update') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log(response);
                    console.log(response.location);
                    if (response.status) {
                        $.NotificationApp.send("Success", response.msg, "top-right", "rgba(0,0,0,0.2)", "success")
                        setTimeout(function() {
                            window.location.href = response.location;
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", response.msg, "top-right", "rgba(0,0,0,0.2)", "error")
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                }
            });
        });
    });
</script>

<script>
    // document.getElementById('fullname').addEventListener('input', function() {
    //     var maxLength = 256;
    //     var input = this.value;

    //     if (input.length > maxLength) {
    //         this.value = input.slice(0, maxLength);
    //         document.getElementById('validationMessage').textContent = '*Fullname cannot exceed 256 characters.';
    //     } else {
    //         document.getElementById('validationMessage').textContent = '';
    //     }
    // });
</script>
@endsection