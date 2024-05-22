@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
    <style>
        .container {
            display: flex;
            align-items: center;
        }

        blockquote {
            margin: 20px 0 30px;
            padding-left: 20px;
            border-left: 5px solid #1371b8;
        }

        .card-padding {
            padding: 0.5rem 1.5rem;
        }

        .label-info {
            background-color: #00c0ef !important;
            color: white;
            padding: 0px 4px;
        }

        .label-outline {
            background-color: transparent;
            border: 1px solid #d2d6de;
            padding: 0px 4px;
        }

        /* Styles for the modal container */
        .modal {
            display: none;
            /* Hide the modal by default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            /* Semi-transparent black background */
            z-index: 9999;
            /* Ensure the modal is on top of other elements */
        }

        /* Styles for the modal content */
        .modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            /* Adjust the width as needed */
            max-width: 500px;
            /* Set a maximum width if desired */
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }

        /* Styles for the close button */
        /* #closeModalBtn {
            margin-top: 10px;
        } */

        /* CSS to style the layout */
        .container {
            display: flex;
            /* align-items: center; */
            margin-top: 3%;
        }

        /* Add some space between the checkbox and text */
        .checkbox-label {
            margin-left: 10px;
        }
        
    </style>
@endsection

@section('main_content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.disputes.index') }}">Disputes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disputes Reply</li>
        </ol>
    </nav>
    <input type="hidden" name="id" id="id" value="{{ $dispute->id }}">

    <div class="row">

        <div class="col-8">
            <div class="card p-2 mt-2">
                <div class="row">
                    <div class="container">
                        ORDER:#{{ $dispute->order_id }}

                        <h5 style="margin-right: 10px; margin-left: 10px;">
                            <span class="label"
                                style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #e61616; color: #fff; width: 80px; height: 22px;">Disputed</span>
                        </h5>
                        <div class="d-flex justify-content-end ">
                            @if (strtolower($orderstatus->status) == 'pending')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">PENDING</span>
                            @elseif (strtolower($orderstatus->status) == 'processing')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #fff; width: 60px; height: 22px;">PROCESSING</span>
                            @elseif (strtolower($orderstatus->status) == 'complited')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff; width: 60px; height: 20px;">COMPLITED</span>
                            @elseif (strtolower($orderstatus->status) == 'canceled')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff; width: 60px; height: 20px;">CANCELED</span>
                            @elseif (strtolower($orderstatus->status) == 'dispatched')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff; width: 60px; height: 20px;">DISPATCHED</span>
                            @elseif (strtolower($orderstatus->status) == 'delivered')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff; width: 60px; height: 20px;">DELIVERED</span>
                            @elseif (strtolower($orderstatus->status) == 'returned')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff; width: 60px; height: 20px;">RETURNED</span>
                            @endif
                        </div>
                    </div>
                </div>
                <hr>
                <br>
                <div disabled class="form-control" cols="10">
                  <div class="col">

                      <div class="row align-items-start">
                            <div class="col-8 ">
                                Payment:                              </div>
                            <div class="col-2">
                           </div>
                            <div class="col-2">
                                @if (strtolower($dataes->status)  == 'success')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">PAID</span>
                            @elseif (strtolower($dataes->status)  == 'unpaid')
                                <span class="label"
                                    style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #f10800; color: #fff; width: 80px; height: 22px;">UNPAID</span>
                            @endif                    
                           </div>
                    </div>
                    </div>
      
                </div>
                <b> Order Details</b>
                <hr>
                <div class="row align-items-start">
                    @foreach ($data as $dataeses)
                        <div class="col-8 ">
                            {{ $dataeses    ->name }}
                        </div>
                    @endforeach
                    @foreach ($orderdata as $myvariables)
                        <div class="col-2">
                            {{ $myvariables->price_without_discount }} X {{ $myvariables->quantity }}
                        </div>
                        <div class="col-2">
                            {{ $myvariables->sub_total }}
                        </div>
                    @endforeach




                </div>
                <div class="row align-items-start mt-3 ">
                    <div class="col-8">
                        <a href="#"> ADD ADMIN NOTE</a>
                    </div>
                    <div class="col-2">
                        <hr>

                        <label>Total</label>
                    </div>
                    <div class="col-2">
                        <hr>
                        {{ $myvariables->sub_total }}
                    </div>
                    <div class="col-8 ">

                    </div>
                    <div class="col-2">
                        <hr>
                        Discount
                    </div>
                    <div class="col-2">
                        <hr>
                        0
                    </div>
                    <div class="col-8 ">

                    </div>
                    <div class="col-2">
                        <hr>
                        Taxes Domestic 0%
                    </div>
                    <div class="col-2">
                        <hr>
                        0
                    </div>
                    <div class="col-8 ">

                    </div>
                    <div class="col-2">
                        <hr>
                        <b> Grand Total</b>
                    </div>
                    <div class="col-2">
                        <hr>
                        {{ $myvariables->sub_total }}
                    </div>
                </div>

            </div>

            <div class="card mt-2">
                <div class="card-header">
                    {{-- <label>{{  $dataes->status}}</label> --}}
                    <a id="clickme"
                    class="btn btn-danger" style="font-size: 70%;">MARK  As {{$dataes->status}}
                  
                </a>
                
                <a href="{{ route('admin.disputes.refund_datas', ['id' => $dispute->id]) }}" class="btn btn-outline-secondary btn-light" style="font-size: 70%;">INITIATE A REFUND</a>

                    <a href="#" class="btn btn-outline-secondary btn-light" style="font-size: 70%; margin-left: 5%;"
                        id="updateStatusBtn">UPDATE STATUS</a>
                    <a href="#" class="btn btn-warning" style="font-size: 70%;">CANCEL ORDER</a>
                    <a href="#" class="btn btn-primary" style="font-size: 70%;">FULLFILL ORDER</a>

                </div>
                <div class="card-body card-padding">


                </div>
            </div>
        </div>
        {{-- side right --}}
        <div class="col-4">


            <div class="card mt-2">
                <div class="card-header">
                    Customer
                </div>
                <div class="card-body card-padding">
                    <h5 class="card-title"><strong>{{ $customer->name }}</h5>
                    <h5 class="card-title"><strong>{{ $customer->email }}</h5>
                    <h5 class="card-title"><strong>{{ $customer->details }}</h5>

                    <hr>
                </div>
            </div>
            <div class="card mt-2">
                <div class="card-header">
                    Refund Request
                    <hr>
                    <div class="row align-items-start">

                        <div class="col-6">
                            <h5>{{ \Carbon\Carbon::parse($dispute->created_at)->diffForHumans(null, true) }}
                                ago </h5>
                        </div>


                        <div class="col-3">
                            <h5 class=""> ${{ $dispute->refund_amount }} </h5>
                        </div>
                        <div class="col-3">
                            <h5 class="">
                                @if ($dispute->status == 'new')
                                    <span class="label"
                                        style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #000; background-color: #fff;">NEW</span>
                                @elseif ($dispute->status == 'open')
                                    <span class="label"
                                        style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #5bc0de; color: #fff; width: 60px; height: 22px;">OPEN</span>
                                @elseif ($dispute->status == 'waiting')
                                    <span class="label"
                                        style="display: inline-block; padding: 4px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; background-color: #00c0ef; color: #fff; width: 60px; height: 20px;">WAITING</span>
                                @endif
                            </h5>
                        </div>
                    </div>




                </div>

            </div>

        </div>
    </div>
    </div>
    <div class="modal" id="statusModal">
        <form id="reply_form">
            <div class="modal-content">
                <h2>UPDATE</h2>
                <hr>


                <input type="hidden" name="id" id="id" value="{{ $dispute->id }}">
                <div class="col-sm-12">
                    <label for="">ORDER STATUS </label><br>
                    <select name="reply_status" id="reply_statuses" class="form-control">
                        <option value="" selected disabled>Select Status</option>
                        <option value="pending" {{ $orderstatus->status == 'pending' ? 'selected' : '' }}>pending</option>
                        <option value="processing" {{ $orderstatus->status == 'processing' ? 'selected' : '' }}>processing
                        </option>
                        <option value="complited" {{ $orderstatus->status == 'complited' ? 'selected' : '' }}>complited
                        </option>
                        <option value="canceled" {{ $orderstatus->status == 'canceled' ? 'selected' : '' }}>canceled
                        </option>
                        <option value="dispatched" {{ $orderstatus->status == 'dispatched' ? 'selected' : '' }}>dispatched
                        </option>
                        <option value="delivered" {{ $orderstatus->status == 'delivered' ? 'selected' : '' }}>delivered
                        </option>
                        <option value="returned" {{ $orderstatus->status == 'returned' ? 'selected' : '' }}>returned
                        </option>
                    </select>
                </div>
                <div>
                    {{-- <div class="container">
                        <input type="checkbox" name="check" id="check" onchange="updateValue()"> Check me

                        <label class="checkbox-label ">
                            <h5>SEND A NOTIFICATION EMAIL TO CUSTOMER?,</h5>
                        </label>
                    </div>  --}}
                    <label class="ml-2">
                        <h6>* Required fields.</h6>
                    </label>
                </div>
                  
                    <div class="row">
                        <div class="col-md-7">
                        </div>
                        <div class="col-md-5">
                            <button class="btn btn-warning" id="closeModalBtn">close</button>
                            <button class="btn btn-primary" id="update" type="submit">Update</button>
                        </div>
                    </div>
                    
            </div>
        </form>

    </div>

    <div class="modal" id="myModal">
        <form id="replyes">

            <div class="modal-content">
                <div class="close-container" style="
                text-align: end;
            ">
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                <input type="hidden" name="id" id="id" value="{{ $dispute->id }}">
                
                <p class="text-center">Payment status changes </p>

                    <button class="btn btn-primary mt-3" id="update" type="submit">Update</button>
            
                </div>
            </form>
      </div>
@endsection


@section('script')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>

    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <!-- Add Bootstrap CSS -->
<script>
    // Function to open the modal
function openModal() {
  var modal = document.getElementById("myModal");
  modal.style.display = "block";
}

// Function to close the modal
function closeModal() {
  var modal = document.getElementById("myModal");
  modal.style.display = "none";
}

document.getElementById("clickme").addEventListener("click", openModal);

</script>
    <script>
        $(document).ready(function() {
            var updateStatusBtn = $("#updateStatusBtn");

            var closeModalBtn = $("#closeModalBtn");

            updateStatusBtn.click(function(e) {
                e.preventDefault();
                $("#statusModal").fadeIn();
            });

            closeModalBtn.click(function() {
                $("#statusModal").fadeOut();
            });
        });
    </script>
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
            $('#reply_form').validate({
                rules: {
                    reply_status: {
                        required: true,
                    },
                    content: {
                        required: true,
                    },
                },
            });
        })
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
                url: "{{ route('admin.disputes.add_amin_note') }}",
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
  <script>
        $(document).ready(function() {
        $(document).on('submit', '#replyes', function(e) {
            $.ajaxSetup({   
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            e.preventDefault();
            var form = $(this);
            var formData = new FormData(form[0]);
          
            $.ajax({
                url: "{{ route('admin.disputes.initiate_refund') }}",
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
