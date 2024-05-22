@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')

    <head>
        {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    </head>
@endsection


@section('css')
    <style>
        #profile-container {

            margin: 0 auto;
            background-color: #fff;
            padding: 10px;

        }

        #customer-info {
            text-align: left;
            padding: 10px;
        }

        #customer-info p {
            margin: 5px 0;
        }

        #customer-info .label {
            font-weight: bold;
        }


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
    @php
        use Carbon\Carbon;
    @endphp
    <!-- Warning Alert Modal -->
    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Warning</h4>
                        <p class="mt-3">Are you sure you want to delete this customer </p>
                        <button type="button" class="btn btn-warning my-2 delete" data-bs-dismiss="modal">Confirm</button>
                        <button type="button" class="btn btn-danger my-2 " data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Customers</li>
        </ol>
    </nav>


    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="javascript::void(0);" data-target="#exampleModal" data-toggle="modal" class="btn btn-success">Add
                Customer</a>
        </div>
        <table id="myExample" class="table table-striped  nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Avtar</th>
                    <th>Customer Name</th>
              
                    <th>Email</th>
                    <th>Member Since</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            @if ($customer->profile_pic)
                                <img src="{{ asset('public/customer/profile/' . $customer->profile_pic) }}" alt=""
                                    width="50px" height="60px">
                            @else
                                <img src="https://www.gravatar.com/avatar/f82262222694aaf364eae2a611272f7b?s=30&d=mm"
                                    alt="Avatar" width="40px">
                            @endif
                        </td>
                        <td>
                            {{ $customer->name }}
                        </td>
                    
                        <td>
                            {{ $customer->email }}
                        </td>
                        <td>

                            {{ Carbon::parse($customer->created_at)->diffForHumans(null, true) . ' ago' }}
                        </td>
                        <td>

                            <a href="#" class="px-2 btn btn-primary text-white btn-sml "   
                                data-target="#exampleModal_{{ $customer->id }}" data-toggle="modal" id="showClient"   ><i     data-toggle="tooltip" data-placement="top" title="View Customer"
                                    class="dripicons-preview"></i></a>
                            {{-- <a href="#"class="px-2 btn btn-warning text-white btn-sml "
                                data-target="#editCustomer_{{ $customer->id }}" data-toggle="modal" id="editClient"><i
                                    class="dripicons-document-edit"></i></i></a> --}}
                            {{-- <button class="px-2 btn btn-danger  btn-sml  deleteUser" id="DeleteClient"
                                data-id="{{ $customer->id }}"><i class="dripicons-trash"></i></button>
                            <button class="px-2 btn btn-secondary  btn-sml  change_password" data-toggle="modal"
                                data-target="#changePassword" data-id="{{ $customer->id }}"><i
                                    class="fas fa-lock"></i></button> --}}
                        </td>
                    </tr>


                    {{-- View Profile Model  --}}
                    <div class="modal fade" id="exampleModal_{{ $customer->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">

                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Profile</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6 mt-2">
                                            <div id="profile-container">
                                                <h1>Customer Profile</h1>
                                                <div id="customer-info">
                                                    <p><span class="label">Name:</span> {{ $customer->name }}</p>
                                                    {{-- <p><span class="label">Nick name:</span> {{ $customer->nickname }}
                                                    </p> --}}
                                                    <p><span class="label">Email:</span> {{ $customer->email }} </p>
                                                    <p><span class="label">Phone:</span> {{ $customer->mobile_number }}
                                                    </p>
                                                    <p><span class="label">Address:</span>
                                                        {{ $customer->user_address ? $customer->user_address->address : '' }}
                                                    </p>
                                                    <p><span class="label">Membership Since:</span>
                                                        {{ Carbon::parse($customer->created_at)->diffForHumans(null, true) . ' ago' }}
                                                    </p>
                                                    <p><span class="label">Description:</span>
                                                        {{ $customer->details }}</p>
                                                    <!-- Add more customer information here -->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mt-2 text-center">

                                            @if ($customer->profile_pic)
                                                <img src="{{ asset('public/customer/profile/' . $customer->profile_pic) }}"
                                                    alt="Profile Pic" width="80%" height="80%">
                                            @else
                                                <img src="https://www.gravatar.com/avatar/f82262222694aaf364eae2a611272f7b?s=30&d=mm 
"
                                                    alt="Avatar" width="80%" height="80%">
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- <div class="modal fade" id="editCustomer_{{ $customer->id }}" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">

                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Form</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form id="edit-customer" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <input type="hidden" name="id" value="{{ $customer->id }}">
                                                    <input type="hidden" name="address_id"
                                                        value="{{ $customer->user_address ? $customer->user_address->id : '' }}">
                                                    <label for="name" class="with-help">Full Name <span
                                                            class="text-danger">*</span></label>
                                                    <input class="form-control" placeholder="Full name" required
                                                        value="{{ $customer->name }}" name="name" type="text"
                                                        id="name">

                                                </div>
                                            </div>

                                            <div class="col-sm-4">
                                                <label for="">Nick Name </label>

                                                <input class="form-control" placeholder="Nick Name" name="nick_name"
                                                    type="text" id="name" value="{{ $customer->nickname }}">
                                            </div>

                                        </div>
                                        <div class="row mt-2">

                                            <div class="col-sm-6">
                                                <label for="active" class="with-help">Email <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="Email" readonly name="email"
                                                    type="email" required id=""
                                                    value="{{ $customer->email }}">

                                            </div>
                                            <div class="col-sm-6">
                                                <label for=""> DOB </label>

                                                <input class="form-control" placeholder="DOB" name="dob"
                                                    value="{{ $customer->dob }}" type="date" id="">
                                            </div>
                                        </div>


                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <label for="">Description</label>
                                                <textarea id="content" name="description" class="form-control summernote">{{ $customer->details }}</textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-6">
                                                <label for="">Address Line 1</label>
                                                <input class="form-control" placeholder="Address Line 1"
                                                    name="address_line1" type="text"
                                                    value="{{ $customer->user_address ? $customer->user_address->floor_apartment : '' }}"
                                                    id="mini_order">
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="">Address Line 2 <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="Address Line 2"
                                                    name="address_line2" type="text" required
                                                    value="{{ $customer->user_address ? $customer->user_address->address : '' }}"
                                                    id="mini_order">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-4">
                                                <label for="">City <span class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="City" name="city"
                                                    type="text" required
                                                    value="{{ $customer->user_address ? $customer->user_address->address : '' }}"
                                                    id="mini_order">
                                            </div>

                                            <div class="col-sm-4">
                                                <label for="">Zip/Postal Code <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="Zip/Postal Code" name="zip_code"
                                                    type="number" required
                                                    value="{{ $customer->user_address ? $customer->user_address->zip_code : '' }}"
                                                    id="mini_order">
                                            </div>

                                            <div class="col-sm-4">
                                                <label for="">Phone <span class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="Phone" name="phone"
                                                    type="number" required value="{{ $customer->mobile_number }}"
                                                    id="mini_order">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-6">
                                                <label for="">Country <span class="text-danger">*</span></label>
                                                <select class="form-select" id="country_id" name="country" required>
                                                    <option value="" selected disabled>Select Country</option>
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}"
                                                            @if ($customer->user_address) {{ $country->id == $customer->user_address->country ? 'selected' : '' }} @endif>
                                                            {{ $country->country_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="">State <span class="text-danger">*</span></label>
                                                <select class="form-select" id="state_id" name="state" required>
                                                    <option value="" selected disabled>Select State</option>
                                                    @foreach ($state as $item)
                                                        <option value="{{ $item->id }}"
                                                            @if ($customer->user_address) {{ $item->id == $customer->user_address->state ? 'selected' : '' }} @endif>
                                                            {{ $item->state_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <label for="">Avatar</label><img
                                                    src="{{ asset('public/customer/profile/' . $customer->profile_pic) }}"
                                                    alt="" width="50px" height="60px">
                                                <input type="file" name="profile_pic" class="form-control">
                                            </div>
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-secondary mt-n1" id="user-save">Save</button>
                                </div>
                                </form>
                            </div>

                        </div> --}}
                @endforeach
            </tbody>
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
                        <form id="create-customer" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-sm-12">

                                    <div class="form-group">
                                        <label for="name" class="with-help">Full Name <span
                                                class="text-danger">*</span></label>
                                        <input class="form-control" placeholder="Full name" name="name"
                                            type="text" id="name">

                                    </div>
                                </div>

                                {{-- <div class="col-sm-4">
                                    <label for="">Nick Name </label>

                                    <input class="form-control" placeholder="Nick Name" name="nick_name" type="text"
                                        id="name">
                                </div> --}}

                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <label for="active" class="with-help">Email <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Email" name="email" type="email"
                                        id="">
                                </div>
                                <div class="col-sm-6">
                                    <label for=""> DOB </label>

                                    <input class="form-control" placeholder="DOB" name="dob" type="date"
                                        max="{{ date('Y-m-d') }}" id="">
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <label for="">Password <span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Password" id="password" name="password"
                                        type="password">
                                    <span toggle="#password-field"
                                        class="fa fa-fw fa-eye field-icon toggle-password"></span>

                                </div>
                                <div class="col-sm-6">
                                    <label for=""> </label>
                                    <input class="form-control" placeholder="Confirm password" name="confirm_password"
                                        type="password" id="confirm_password">
                                    <span toggle="#password-field"
                                        class="fa fa-fw fa-eye field-icon toggle-confirm"></span>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <label for="">Description</label>
                                    <textarea id="content" name="description" class="form-control summernote"></textarea>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <label for="">Address Line 1 <span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Address Line 1" name="address_line1"
                                        type="text">
                                </div>

                                <div class="col-sm-6">
                                    <label for="">Address Line 2 <span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Address Line 2" name="address_line2"
                                        type="text">
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-sm-4">
                                    <label for="">City <span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="City" name="city" type="text">
                                </div>

                                <div class="col-sm-4">
                                    <label for="">Zip/Postal Code <span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Zip/Postal Code" name="zip_code"
                                        type="text">
                                </div>

                                <div class="col-sm-4">
                                    <label for="">Phone <span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Phone" name="phone" type="text">
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <label for="">Country <span class="text-danger">*</span></label>
                                    <select class="form-select" id="country" name="country">
                                        <option value="" selected disabled>Select Country</option>
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}">
                                                {{ $country->country_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label for="">State <span class="text-danger">*</span></label>
                                    <select class="form-select" id="state" name="state">
                                        <option value="" selected disabled>Select State</option>

                                    </select>
                                </div>

                            </div>

                            <div class="row mt-2">
                                <div class="col-sm-12">
                                    <label for="">Avatar</label><span>(JPG, JPEG, PNG, 2MB max)</span>
                                    <input type="file" name="profile_pic" class="form-control">
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



        {{--  Change Password model --}}

        <div class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="change-password" enctype="multipart/form-data">
                            <div class="row mt-2">
                                <div class="col-sm-6">
                                    <input type="hidden" id="user-id" name="user_id">
                                    <label for="">New Password<span class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Password" name="password" type="password"
                                        required id="sec_password">
                                        <span toggle="#password-field"
                                        class="fa fa-fw fa-eye field-icon toggle-password-sec"></span>
                                </div>
                                <div class="col-sm-6">
                                    <label for=""> </label>
                                    <input class="form-control" placeholder="Confirm password"
                                        name="password_confirmation" type="password"  required id="sec_confirm_password">
                                        <span toggle="#password-field"
                                        class="fa fa-fw fa-eye field-icon toggle-confirm-sec"></span>
                                </div>
                            </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script>

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

            $(".toggle-password").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $('#password');
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });


          

            $(".toggle-confirm").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $('#confirm_password');
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });

            $(".toggle-password-sec").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $('#sec_password');
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });

            $(".toggle-confirm-sec").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $('#sec_confirm_password');
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });



            $(document).ready(function() {
                $('#myExample').DataTable({
                    responsive: false
                });
            });

            $(document).ready(function() {
                $('#create-customer').validate({
                    rules: {
                        name: {
                            required: true,
                            maxlength: 46,
                        },
                        email: {
                            required: true,
                            maxlength: 86,
                            email: true,
                            emailWithDomain: true

                        },
                        password: {
                            required: true,
                            minlength: 6,
                            maxlength: 16,
                            uppercase: true,
                            lowercase: true,
                            numeric: true,
                            specialChars: true,
                        },
                        confirm_password: {
                            equalTo: "#password"
                        },
                        phone: {
                            required: true,
                            number: true,
                            maxlength: 20,
                            minlength: 6
                        },
                        zip_code: {
                            required: true,
                            number: true,
                            minlength: 4,
                            maxlength: 20,
                        },
                        address_line1: {
                            required: true
                        },
                        address_line2: {
                            required: true
                        },
                        city: {
                            required: true
                        },
                        country: {
                            required: true
                        },
                        state: {
                            required: true
                        },
                        profile_pic: {
                            imageFormat: true,
                            filesize: 2024
                        }
                    },
                    messages: {
                        confirm_password: {
                            equalTo: "Password & Confirm Password does not match."
                        },
                        zip_code: {
                            number: "Please enter valid zip code"
                        },
                        profile_pic: {
                            imageFormat: "Please upload file in these format only (jpg, jpeg, png).",
                            filesize: "Maximum file size is 2MB"
                        }
                    }
                })


                $.validator.addMethod("emailWithDomain", function(value, element) {
                    return this.optional(element) || /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test(value);
                }, "Please enter a valid email address.");
                $.validator.addMethod("specialChars", function(value, element) {
                    return this.optional(element) || /[!@#$%^&*(),.?":{}|<>]/.test(value);
                }, "Please include at least one special character.");

                $.validator.addMethod("uppercase", function(value, element) {
                    return this.optional(element) || /[A-Z]/.test(value);
                }, "Password must contain at least one uppercase letter")

                $.validator.addMethod("lowercase", function(value, element) {
                    return this.optional(element) || /[a-z]/.test(value);
                }, "Password must contain at least one lowercase letter")

                $.validator.addMethod("numeric", function(value, element) {
                    return this.optional(element) || /[0-9]/.test(value);
                }, "Password must contain at least one numeric digit")
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



            $(document).on('submit', '#create-customer', function(e) {
                e.preventDefault();

                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");

                $.ajax({
                        url: "{{ route('vendor.customer.create') }}",
                        type: "POST",
                        data: fd,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)",
                                "success");
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            });


            $(document).on('submit', '#edit-customer', function(e) {
                e.preventDefault();

                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");

                $.ajax({
                        url: "{{ route('vendor.customer.edit') }}",
                        type: "POST",
                        data: fd,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)",
                                "success");
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            });



            $(document).on('change', '#country', function(e) {
                e.preventDefault();
                let id = $(this).val();
                console.log(id);

                $.ajax({
                    url: "{{ route('vendor.getStates') }}",
                    type: "POST",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        console.log(result);
                        if (result.status) {
                            $('#state').html('<option value="" selected disabled>Select State</option>');
                            $.each(result.data, function(key, val) {

                                $('#state').append(`
                    <option value="${val.id}" >${val.state_name}</option>
                    `);
                            });
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                });
            })



            $(document).on('change', '#country_id', function(e) {
                e.preventDefault();
                let id = $(this).val();
                console.log(id);

                $.ajax({
                    url: "{{ route('vendor.getStates') }}",
                    type: "POST",
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        console.log(result);
                        if (result.status) {
                            $('#state_id').html('<option value="" selected disabled>Select State</option>');
                            $.each(result.data, function(key, val) {

                                $('#state_id').append(`
                    <option value="${val.id}" >${val.state_name}</option>
                    `);
                            });
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "error")
                        }
                    },
                });
            })




            $(document).on("click", ".deleteUser", function(e) {
                var id = $(this).data('id');
                let fd = new FormData();
                fd.append('id', id);
                fd.append('_token', '{{ csrf_token() }}');
                $("#warning-alert-modal-text").text(name);

                $('#warning-alert-modal').modal('show');

                $('#warning-alert-modal').on('click', '.delete', function() {
                    $.ajax({
                            url: "{{ route('vendor.customer.delete') }}",
                            type: 'POST',
                            data: fd,
                            dataType: "JSON",
                            contentType: false,
                            processData: false,
                        })
                        .done(function(result) {
                            if (result.status) {
                                $.NotificationApp.send("Success", result.msg, "top-right",
                                    "rgba(0,0,0,0.2)",
                                    "success");
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)",
                                    "error");
                            }
                        })
                        .fail(function(jqXHR, exception) {
                            console.log(jqXHR.responseText);
                        });
                });

            });


            $(document).on('click', '.change_password', function() {
                var user_id = $(this).data('id');
                $('#user-id').val(user_id);
            })


            $(document).on('submit', '#change-password', function(e) {
                console.log('hello');
                e.preventDefault();
                let fd = new FormData(this);
                fd.append('_token', "{{ csrf_token() }}");

                $.ajax({
                        url: "{{ route('vendor.customer.passwordChange') }}",
                        type: "POST",
                        data: fd,
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)",
                                "success");
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });


            })
        </script>
    @endsection
