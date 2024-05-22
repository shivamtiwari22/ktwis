@extends('admin.layout.app')

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


    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Customers</li>
        </ol>
    </nav>


    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            {{-- <a href="javascript::void(0);" data-target="#exampleModal" data-toggle="modal" class="btn btn-success">Add
                Customer</a> --}}
        </div>
        <table id="myExample" class="table table-striped  nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Avtar</th>
                    <th>Customer Name</th>
                    <th>Mobile Number</th>
                    <th>Email</th>
                    <th>Member Since</th>
                    <th>Status</th>
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
                            {{ $customer->mobile_number ?? '-' }}
                        </td>
                        <td>
                            {{ $customer->email }}
                        </td>
                        <td>

                            {{ Carbon::parse($customer->created_at)->diffForHumans(null, true) . ' ago' }}
                        </td>
                        <td>
                            <select name="" class="chage_status" data-id="{{$customer->id}}" >
                                <option value="1"  {{$customer->customer_status == 1 ? "selected" : ""}}>Acitve</option>
                                <option value="0"   {{$customer->customer_status == 0 ? "selected" : ""}}>Inactive</option>
                            </select></td>
                        <td>

                            <a href="#" class="px-2 btn btn-primary text-white btn-sml "
                                data-target="#exampleModal_{{ $customer->id }}" data-toggle="modal" id="showClient"><i
                                    data-toggle="tooltip" data-placement="top" title="View Customer"
                                    class="dripicons-preview"></i></a>
                            <a href="#"class="px-2 btn btn-warning text-white btn-sml " data-target="#editCustomer"
                                data-toggle="modal" id="editClient" data-id="{{ $customer->id }}"><i
                                    class="dripicons-document-edit"></i></i></a>
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
                                                    <p><span class="label">Nick name:</span> {{ $customer->nickname }}
                                                    </p>
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
                @endforeach
            </tbody>
        </table>


        <div class="modal fade" id="editCustomer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">

            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="append-data">

                    </div>

                </div>
            </div>
        @endsection

        @section('script')
            <script>
                $(document).on('click', '#editClient', function(e) {
                    e.preventDefault();

                    let fd = new FormData();
                    let id = $(this).attr('data-id');
                    fd.append('_token', "{{ csrf_token() }}");
                    fd.append('customer_id', id);


                    $.ajax({
                            url: "{{ route('admin.customer.getData') }}",
                            type: "POST",
                            data: fd,
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                        })
                        .done(function(result) {
                            // console.log(result.state);
                            if (result.status) {

                                var html = '';
                                $.each(result.countries, function(i, content) {
                                    // console.log(content);
                                    html +=
                                        `<option value="${content.id}" ${content.id == result.data.user_address.country ? 'selected' : ''} > ${content.country_name}</option>`;
                                })


                                // console.log(html);

                                var state = '';
                                // $.each(result.state, function(key, index) {
                                //     // console.log(index);
                                //     state += `<option value="${index.id}" > ${index.state_name}</option>`;
                                // });

                                console.log(state);


                                var img = "{{ asset('public/customer/profile/') }}" + "/" + result.data.profile_pic;
                                $('#append-data').html(`
                            <form id="edit-customer" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <input type="hidden" name="id" value="${result.data.id}">
                                                    <input type="hidden" name="address_id"
                                                        value="${result.data.user_address ?  result.data.user_address.id : ''}">
                                                    <label for="name" class="with-help">Full Name <span
                                                            class="text-danger">*</span></label>
                                                    <input class="form-control" placeholder="Full name" required
                                                        value="${result.data.name}" name="name" type="text"
                                                        id="name">

                                                </div>
                                            </div>

                                        </div>
                                        <div class="row mt-2">

                                            <div class="col-sm-6">
                                                <label for="active" class="with-help">Email <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="Email" readonly name="email"
                                                    type="email" required id=""
                                                    value="${result.data.email}">

                                            </div>
                                            <div class="col-sm-6">
                                                <label for=""> DOB </label>

                                                <input class="form-control" placeholder="DOB" name="dob"
                                                    value="${result.data.dob}" type="date" id="" max="{{date('Y-m-d')}}">
                                            </div>
                                        </div>


                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <label for="">Description</label>
                                                <textarea id="content" name="description" class="form-control summernote">${ result.data.details}</textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-6">
                                                <label for="">Address Line 1</label>
                                                <input class="form-control" placeholder="Address Line 1"
                                                    name="address_line1" type="text"
                                                    value="${ result.data.user_address ?   result.data.user_address.floor_apartment : ''}"
                                                    id="mini_order">
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="">Address Line 2 <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="Address Line 2"
                                                    name="address_line2" type="text" required
                                                    value="${ result.data.user_address ?   result.data.user_address.address : ''}"
                                                    id="mini_order">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-4">
                                                <label for="">City <span class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="City" name="city"
                                                    type="text" required
                                                    value="${ result.data.user_address ?   result.data.user_address.city : ''}"
                                                    id="mini_order">
                                            </div>

                                            <div class="col-sm-4">
                                                <label for="">Zip/Postal Code <span
                                                        class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="Zip/Postal Code" name="zip_code" min="0"
                                                    type="number" required
                                                    value="${ result.data.user_address ?   result.data.user_address.zip_code : ''}"
                                                    id="mini_order">
                                            </div>

                                            <div class="col-sm-4">
                                                <label for="">Phone <span class="text-danger">*</span></label>
                                                <input class="form-control" placeholder="Phone" name="phone" min="1"
                                                    type="number" required value="${ result.data.mobile_number}"
                                                    id="mini_order">
                                            </div>
                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-6">
                                                <label for="">Country <span class="text-danger">*</span></label>
                                                <select class="form-select" id="country_id" name="country" required>
    
                                                    <option value="" selected disabled>Select Country</option>
                                                    ${html}
                                                </select>
                                            </div>

                                            <div class="col-sm-6">
                                                <label for="">State <span class="text-danger">*</span></label>
                                                <select class="form-select" id="state_id" name="state" required>
                                                    <option value="" selected disabled>Select State</option>
                                                    ${state}
                                                </select>
                                            </div>

                                        </div>

                                        <div class="row mt-2">
                                            <div class="col-sm-12">
                                                <label for="">Avatar</label><img
                                                    src="${img}"
                                                    alt="" width="50px" height="60px">
                                                <input type="file" name="profile_pic" class="form-control">
                                            </div>
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-secondary mt-n1" id="user-save">Save</button>
                                </div>
                                </form>
                            `)

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
                            url: "{{ route('admin.customer.edit') }}",
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
                        url: "{{ route('admin.getStates') }}",
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



                $(document).on('change', '.chage_status', function(e) {
            e.preventDefault();
          
            var status_value =   $(this).val();
                var id =   $(this).attr('data-id');
            let fd = new FormData();
            fd.append('_token', "{{ csrf_token() }}");
            fd.append('id', id);
                fd.append('status_value', status_value);
            $.ajax({
                url: "{{ route('admin.customer.status') }}",
                type: "POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $("#load").show();
                },
                success: function(result) {
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                   
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                    }
                },
                complete: function() {
                    $("#load").hide();
                },
                error: function(jqXHR, exception) {
                    console.log(jqXHR.responseText);
                }
            });
        });






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
            </script>
        @endsection
